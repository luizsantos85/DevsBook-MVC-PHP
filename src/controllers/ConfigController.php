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

      //avatar
      if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
        $newAvatar = $_FILES['avatar'];

        if (in_array($newAvatar['type'], ['image/jpg', 'image/jpeg', 'image/png'])) {
          $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
          $updateFields['avatar'] = $avatarName;
        }
      }

      //capa
      if (isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
        $newCover = $_FILES['cover'];

        if (in_array($newCover['type'], ['image/jpg', 'image/jpeg', 'image/png'])) {
          $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
          $updateFields['cover'] = $coverName;
        }
      }
      UserHandler::updateUser($updateFields, $this->loggedUser->id);
    }
    $this->redirect('/config');
  }

  private function cutImage($file, $width, $height, $folder)
  {
    list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
    $ratio = $widthOrig / $heightOrig;

    $newWidth = $width;
    $newHeight = $newWidth / $ratio;

    if ($newHeight < $height) {
      $newHeight = $height;
      $newWidth = $newHeight * $ratio;
    }

    $x = $width - $newWidth;
    $y = $height - $newHeight;
    $x = $x < 0 ? $x / 2 : $x;
    $y = $y < 0 ? $y / 2 : $y;

    $finalImage = imagecreatetruecolor($width, $height);
    switch ($file['type']) {
      case 'image/jpeg':
      case 'image/jpg':
        $image = imagecreatefromjpeg($file['tmp_name']);
        break;
      case 'image/png':
        $image = imagecreatefrompng($file['tmp_name']);
        break;
    }
    imagecopyresampled(
      $finalImage,
      $image,
      $x,
      $y,
      0,
      0,
      $newWidth,
      $newHeight,
      $widthOrig,
      $heightOrig
    );
    $fileName = md5(time() . rand(0, 999)) . '.jpg';

    imagejpeg($finalImage, $folder . '/' . $fileName);
    return $fileName;
  }
}
