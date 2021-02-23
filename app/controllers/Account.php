<?php


namespace app\controllers;

use app\Mail;
use core\Application;
use core\Auth;
use core\Controller;
use core\Form;
use core\Request;
use core\User;


class Account extends Controller
{
    public function signup(Request $request)
    {
        $user = new User();
        $form = new Form($user, ['inputTypes' => ['email' => 'email', 'password' => 'password']]);

        if ($request->method() === 'post') {
            $user->setData($request->data());
            if ($user->validate() && $user->createUser()) {
                Mail::sendActivationEmail($user->email, $user->activation_token);
                $this->redirect('/signup-success');
            } else {
                $this->render('signup', ['form' => $form]);
            }
        } else {
            $this->render('signup', ['form' => $form]);
        }
    }

    public function signupSuccess()
    {
        $this->render('signup_success');
    }

    public function activate()
    {
        $token = $this->params['token'];

        if (User::activate($token)) {
            $this->redirect('/activate-success');
        } else {
            $this->redirect('/');
            // todo add some error page
        }
    }

    public function activateSuccess()
    {
        $this->render('activate_success');
    }

    // todo implement middleware
    public function login(Request $request)
    {
        $user = new User();
        $form = new Form($user, [
            'inputTypes' => ['password' => 'password'],
            'exclude' => ['email']
        ]);

        if ($request->method() === 'post') {
            $user->setData($request->data());
            $user = $user->authenticate();
            if ($user) {
                Auth::login($user);
                Application::$app->session->addMessage('success', 'You have logged in successfully.');
                $this->redirect('/');
            } else {
                Application::$app->session->addMessage('danger', 'Login unsuccessful. Please try again.');
                $this->render('login', ['form' => $form]);
            }
        } else {
            $this->render('login', ['form' => $form]);
        }
    }

    public function logout()
    {
        Auth::logout();
        // going to another page creates a new session after the previous one has been destroyed by "logout" method
        // that's why "addMessage" method can't be used
        $this->redirect('/logout-success');
    }

    public function logoutSuccess()
    {
        $this->render('logout_success');
    }
}
