<?php


namespace core;


class Controller
{
    public $globals;
    public $params;

    public function __construct(array $params)
    {
        $this->addGlobals([
            'user' => Auth::getUser(),
            'messages' => Application::$app->session->getMessages()
        ]);

        $this->params = $params;
    }

    public function addGlobals(array $arr)
    {
        foreach ($arr as $key => $value) {
            $this->globals[$key] = $value;
        }
    }
    
    public function render(string $view, array $vars = [])
    {
        extract($this->globals);
        if ($this->params) extract($this->params, EXTR_SKIP);
        if ($vars) extract($vars, EXTR_SKIP);

        $file = Application::$rootDir . "/app/views/$view.php";

        if (file_exists($file)) {
            include $file;
        } else {
            throw new \Exception('File doesn\'t exist');
        }
    }

    public function redirect($url)
    {
        header("Location: $url", true, 303);
        exit;
    }
}

// todo change php tags to double curly braces
// todo combine header and footer into single file and add title variable