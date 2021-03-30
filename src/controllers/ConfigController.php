<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;


class ConfigController extends Controller
{
  private $loggedUser;

  public function __construct()
  {
    $this->loggedUser = UserHandler::checkLogin();
    if ($this->loggedUser === false) {
      $this->redirect('/login');
    }
  }

  public function index()
  {
    $flash = '';
    if (!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      $_SESSION['flash'] = '';
    }

    $user = UserHandler::getUser($this->loggedUser->id);

    $this->render('profile_config', [
      'loggedUser' => $this->loggedUser,
      'user' => $user,
      'flash' => $flash
    ]);
  }

  public function configAction()
  {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING);
    $work = filter_input(INPUT_POST, 'work', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $passwordConfirm = filter_input(INPUT_POST, 'passwordConfirm', FILTER_SANITIZE_STRING);

    if ($name && $email) {
      $updateFields = [];

      $user = UserHandler::getUser($this->loggedUser->id);
      
      //email
      if ($user->email != $email) {
        if (!UserHandler::emailExists($email)) {
          $updateFields['email'] = $email;
        } else {
          $_SESSION['flash'] = 'E-mail já existente.';
          $this->redirect('/config');
        }
      }
      //birthdate
      $birthdate = explode('/', $birthdate);
      if (count($birthdate) != 3) {
        $_SESSION['flash'] = 'Data de nascimento inválida.';
        $this->redirect('/config');
      } else {
        $birthdate = "$birthdate[2]-$birthdate[1]-$birthdate[0]";
        if (strtotime($birthdate) === false) {
          $_SESSION['flash'] = 'Data de nascimento inválida.';
          $this->redirect('/config');
        }
        $updateFields['birthdate'] = $birthdate;
      }

      //password
      if (!empty($password)) {
        if ($password === $passwordConfirm) {
          $updateFields['password'] = $password;
        } else {
          $_SESSION['flash'] = 'As senhas não batem.';
          $this->redirect('/config');
        }
      }
      $updateFields['name'] = $name;
      $updateFields['work'] = $work;
      $updateFields['city'] = $city;

      UserHandler::updateUser($updateFields, $this->loggedUser->id);
    }
    $this->redirect('/config');
  }
}
