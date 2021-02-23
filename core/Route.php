<?php


namespace core;


class Route
{
    public Request $request;
    private array $routes;
    private array $params;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function convertToRegex($url)
    {
        // escape forward slashes
        $regex = preg_replace('/\//', '\/', $url);

        // convert variables with custom regular expressions
        // e.g. "{id:\d+}" gets converted to "?P<id>\d+"
        // "[^\}]" capture everything except "}" where caret sign negates expression inside square brackets and
        // backward slash escapes curly brace
        // dollar sign with number in replacement pattern means number of captured group (starts with 0)
        $regex = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<$1>$2)', $regex);

        // add start and end delimiters and case insensitive flag
        $regex = "/^$regex$/i";

        return $regex;
    }

    public function path(string $url, array $callback)
    {
        $this->routes[$this->convertToRegex($url)] = $callback;
    }

    // match url against array of urls converted to regex
    // if there is a match save params if there are any and return regex that matched this url
    public function match($url)
    {
        foreach($this->routes as $route => $_) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $this->params[$key] = $value;
                    }
                }

                return $route;
            }
        }

        return false;
    }

    public function resolve()
    {
        $url = $this->request->url();

        if ($this->match($url)) {
            // $callback is an array ['ClassName', 'methodName'])
            // $this->match($url) returns regex which url matched to extract controller from array
            $callback = $this->routes[$this->match($url)];
            $controller = new $callback[0]($this->params ?? []);
            $method = $callback[1];
            $controller->$method($this->request);
        } else {
            // todo handle 404
            exit;
        }
    }
}
