<?php


// E-mails are sent using "mailgun" service.
// "composer require mailgun/mailgun-php kriswallsmith/buzz nyholm/psr7" installs required packages.
// because it's a free version emails are only sent to authorized recipients which should be specified in account settings
namespace app;

use Mailgun\Mailgun;
use core\Application;


class Mail
{
    public static function send($to, $subject, $text, $html)
    {
        $mgClient = Mailgun::create($_ENV['MAILGUN_API_KEY']);
        $domain = $_ENV['MAILGUN_DOMAIN_NAME'];
        $params = [
            'from'    => 'https://myserver.com',
            'to'      => $to,
            'subject' => $subject,
            'text'    => $text,
            'html'    => $html,
        ];

        $mgClient->messages()->send($domain, $params);
    }

    public static function getTemplate(string $view, array $vars = [])
    {
        if ($vars) extract($vars);

        $file = Application::$rootDir . "/app/views/email_templates/$view";

        if (file_exists($file)) {
            ob_start();
            include $file;
            return ob_get_clean();
        } else {
            throw new \Exception('File doesn\'t exist');
        }
    }

    // todo change url when deploying
    public static function sendActivationEmail(string $to, string $token)
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/activate/' . $token;
        $text = self::getTemplate('account_activation.txt', ['url' => $url]);
        $html = self::getTemplate('account_activation.html', ['url' => $url]);

        self::send($to, 'Account Activation', $text, $html);
    }

    public static function sendPasswordResetEmail(string $to, string $token)
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password-reset/' . $token;
        $text = self::getTemplate('password_reset.txt', ['url' => $url]);
        $html = self::getTemplate('password_reset.html', ['url' => $url]);

        self::send($to, 'Password reset request', $text, $html);
    }
}
