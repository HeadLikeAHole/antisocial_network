<?php


namespace core;


class Application
{
    public static string $rootDir;
    public static self $app;
    public Database $db;
    public Request $request;
    public Session $session;
    public Route $route;

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
