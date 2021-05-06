<?php


namespace core;


class Application
{
    public static $rootDir;
    public static $app;
    public $db;
    public $request;
    public $session;
    public $route;

    public function __construct(string $rootDir, array $config)
    {
        self::$rootDir = $rootDir;
        self::$app = $this;
        $this->db = new Database($config['db']);
        $this->request = new Request();
        $this->session = new Session();
        $this->route = new Route($this->request);
    }

    public function run()
    {
        $this->route->resolve();
    }
}
