<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class LoginController extends Controller
{
  public function signin()
  {
    $flash = '';
    if (!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      $_SESSION['flash'] = '';
    }
    $this->render('signin', [
      'flash' => $flash
    ]);
  }

  public function signinAction()
  {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if ($email && $password) {
      $token = UserHandler::verifyLogin($email, $password);
      if ($token) {
        $_SESSION['token'] = $token;
        $this->redirect(('/'));
      } else {
        $_SESSION['flash'] = 'E-mail e/ou senha inválidos.';
        $this->redirect(('/login'));
      }
    } else {
      $_SESSION['flash'] = 'Campos em branco não são permitidos.';
      $this->redirect(('/login'));
    }
  }

  /*Cadastro */
  public function signup()
  {
    $flash = '';
    if (!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      $_SESSION['flash'] = '';
    }
    $this->render('signup', [
      'flash' => $flash
    ]);
  }

  public function signupAction()
  {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $birthdate = filter_input(INPUT_POST, 'birthdate');

    if ($name && $email && $password && $birthdate) {
      $birthdate = explode('/', $birthdate);
      if (count($birthdate) != 3) {
        $_SESSION['flash'] = 'Data de nascimento inválida.';
        $this->redirect(('/cadastro'));
      } else {
        $birthdate = "$birthdate[2]-$birthdate[1]-$birthdate[0]";
        if (strtotime($birthdate) === false) {
          $_SESSION['flash'] = 'Data de nascimento inválida.';
          $this->redirect(('/cadastro'));
        }
      }
      if (UserHandler::emailExists($email) === false) {
        $token = UserHandler::addUser($name, $email, $password, $birthdate);
        $_SESSION['token'] = $token;
        $this->redirect(('/'));
      } else {
        $_SESSION['flash'] = 'E-mail já cadastrado.';
        $this->redirect(('/cadastro'));
      }
    } else {
      $_SESSION['flash'] = 'Campos em branco não são permitidos.';
      $this->redirect(('/cadastro'));
    }
  }
}
