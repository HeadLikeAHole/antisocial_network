<?php


namespace app\controllers;

use core\Controller;
use core\Request;
use app\models\Post;


class Site extends Controller
{
    public function home(Request $request)
    {
        // get all posts
        $posts = (new Post())->all();
        $this->render('home', ['posts' => $posts]);
    }
}
