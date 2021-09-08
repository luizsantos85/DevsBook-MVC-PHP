<?php

namespace src\handlers;

use \src\models\User;
use \src\models\ResetPassword;

class ResetPassHandler
{
  public static function createResetPassToken($email)
  {
    $data = User::select()->where('email', $email)->one();
    $token = md5(time() . rand(0, 9999) . time());
    Resetpassword::insert([
      'id_user' => $data['id'],
      'hash' => $token,
      'expired_in' => date('Y-m-d H:i', strtotime('+30 minutes'))
    ])->execute();

    return $token;
  }

  public static function verifyResetPassToken($token)
  {
    $date = date('Y-m-d H:i', strtotime('now'));
    $data = Resetpassword::select()->where('hash', $token)->where('used', 0)->where('expired_in', '>', $date)->one();
    return $data;
  }

  public static function updateResetPass($fields,$userId,$hash)
  {
    //atualizar senha do usuario
    if (count($fields) > 0) {
      $update = User::update();
      foreach ($fields as $fieldName => $fieldValue) {
        if ($fieldName == 'password') {
          $fieldValue = password_hash($fieldValue, PASSWORD_BCRYPT);
        }
        $update->set($fieldName, $fieldValue);
      }
      $update->where('id', $userId)->execute();

      //invalidar token
      Resetpassword::update()->set('used', '1')->where('hash', $hash)->execute();
    }
  }
}
