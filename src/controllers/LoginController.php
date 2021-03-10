<?php

namespace src\controllers;

use \core\Controller;

class LoginController extends Controller
{

  public function signin()
  {
    $this->render('signin');
  }

  public function signup()
  {
    echo 'cadastro';

  }

}
