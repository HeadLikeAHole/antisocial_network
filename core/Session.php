<?php


namespace core;


class Session
{
    public function __construct()
    {
        session_save_path(dirname(__DIR__) . '/sessions');
        session_start();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function addMessage($type, $message)
    {
        if (!isset($_SESSION['messages'])) $_SESSION['messages'] = [];
        $_SESSION['messages'][] = ['type' => $type, 'text' => $message];
    }

    public function getMessages()
    {
        if (isset($_SESSION['messages'])) {
            $messages = $_SESSION['messages'];
            unset($_SESSION['messages']);
            return $messages;
        }

        return [];
    }
}
