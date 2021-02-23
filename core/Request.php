<?php


namespace core;


class Request
{
    public function url(): string
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $paramsPosition = strpos($url, '?');

        if ($paramsPosition === false) {
            return $url;
        }

        $url = substr($url, 0, $paramsPosition);

        return $url;
    }

    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function data(): array
    {
        $data = [];

        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $data;
    }
}