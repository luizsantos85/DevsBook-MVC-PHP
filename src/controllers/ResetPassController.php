<?php

namespace src\controllers;

use \core\Controller;
use src\handlers\MailHandler;
use src\handlers\UserHandler;
use src\handlers\ResetPassHandler;

class ResetPassController extends Controller
{
    public function resetPassword()
    {
        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $this->render('/resetPass', [
            'flash' => $flash
        ]);
    }


}
