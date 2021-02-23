<?php


namespace core;


class Auth
{
    public static function login($user, $rememberLogin = true)
    {
        // prevents CSRF attack
        session_regenerate_id(true);
        Application::$app->session->set('user_id', $user->id);

        if ($rememberLogin) {
            // make user logged in for 30 days
            $rememberedLogin = new RememberedLogin();
            if ($rememberedLogin->rememberLogin($user->id)) {
                setcookie('remembered_login', $rememberedLogin->remembered_token, $rememberedLogin->expires, '/');
            }
        }
    }

    public static function loginFromRememberedLogin()
    {
        $token = $_COOKIE['remembered_login'] ?? false;

        if ($token) {
            $rememberedLogin = RememberedLogin::getRememberedLogin($token);

            if ($rememberedLogin && !$rememberedLogin->hasExpired()) {
                $user = User::get('id', $rememberedLogin->user_id);
                self::login($user, false);
                return $user;
            }

            return false;
        }

        return false;
    }

    public static function getUser()
    {
        if (Application::$app->session->get('user_id')) {
            return User::get('id', Application::$app->session->get('user_id'));
        } else {
            self::loginFromRememberedLogin();
        }

        return false;
    }

    public static function deleteRememberedLogin()
    {
        $token = $_COOKIE['remembered_login'] ?? false;

        if ($token) {
            RememberedLogin::forgetLogin($token);
            setcookie('remembered_login', '', time() - 3600);
        }
    }

    public static function logout()
    {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        self::deleteRememberedLogin();
    }

    public static function saveRequestedURL() {
        Application::$app->session->set('return_to', $_SERVER['REQUEST_URI']);
    }

    public static function getReturnToURL()
    {
        return Application::$app->session->get('return_to');
    }
}