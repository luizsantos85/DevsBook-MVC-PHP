<?php

use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');

$router->get('/login', 'LoginController@signin');
$router->post('/login', 'LoginController@signinAction');

$router->get('/cadastro', 'LoginController@signup');
$router->post('/cadastro', 'LoginController@signupAction');

$router->post('/post/new', 'PostController@newPost');

// $router->get('/pesquisa, ');
// $router->get('/perfil, ');
// $router->get('/logout, ');
// $router->get('/config');
// $router->get('/fotos');
// $router->get('/amigos');

