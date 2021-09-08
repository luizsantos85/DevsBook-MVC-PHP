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

    public function resetPasswordAction(){
        $email = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);

        if($email){
            //Verifica se o email existe no banco de dados.
            if(!UserHandler::emailExists($email)){
                $_SESSION['flash'] = 'E-mail inválido.';
                $this->redirect('/resetPass');

            }else{
                //gera um tokem e salva no bd para o email solicitado.
                $hash = ResetPassHandler::createResetPassToken($email);
                if($hash){
                    $link = "<a href='http://localhost/Aulas-Projetos/B7WEB/Projetos/PHP/PHPN1/DevsBook-MVC/public/resetPass/$hash'>Clique aqui</a>"; //colocar link da sua hospedagem

                    //Envia o link para o email
                    $emailReset = $email;
                    $assunto = 'Redefinir senha';
                    $body = "
                        <h2>Você solicitou uma nova senha?</h2>
                        <hr>
                        <p>Clique abaixo para redefinir sua senha.</p>
                        <p>Esse token irá exprirar em 30 minutos.</p>
                        <p>" . $link . "</p>
                        <hr>
                        <h5>Não foi você quem solicitou? Ignore este email, porém alguém tentou acessar seus dados.</h5>
                        <hr>";
                    $info = array('assunto' => $assunto, 'body' => $body);

                    $mail = new MailHandler();
                    $mail->addAdress($emailReset);
                    $mail->formatEmail($info);

                    if($mail->sendEmail()){
                        $_SESSION['flash'] = 'E-mail enviado com sucesso';
                        $this->redirect('/login');
                    }
                        $_SESSION['flash'] = 'Ops, ocorreu um erro e o e-mail não pode ser enviado.';
                        $this->redirect('/resetPass');
                }
                    $_SESSION['flash'] = 'Ops, ocorreu um erro ao gerar um token.';
                    $this->redirect('/resetPass');
            }
        }
            $_SESSION['flash'] = 'Informe o seu E-MAIL para redefinir a senha.';
            $this->redirect('/resetPass');
    }

    public function pageResetPass($atts){
        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $token = $atts['token'];
        $hash = ResetPassHandler::verifyResetPassToken($token);
        if($hash === false){
            $_SESSION['flash'] = 'Token inválido.';
            $this->redirect('/resetPass');
        }
        $this->render('/resetPassToken',[
           'flash'=> $flash,
           'hash' => $hash
        ]);
    }

    public function pageResetPassAction($atts){
        $password = filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
        $confirmPass = filter_input(INPUT_POST,'confirmPass',FILTER_SANITIZE_STRING);
        $token = $atts['token'];
        $hash = ResetPassHandler::verifyResetPassToken($token);

        if($hash === false){
            $_SESSION['flash'] = 'Token inválido.';
            $this->redirect('/resetPass');
        }

        if($password){
            $updateFields = [];

            if($password === $confirmPass){
                $updateFields['password'] = $password;
            }else{
                $_SESSION['flash'] = 'Senhas não batem.';
                $this->redirect('/resetPass/'.$hash['hash']);
            }

            //Update da senha do usuario
            ResetPassHandler::updateResetPass($updateFields,$hash['id_user'],$hash['hash']);
            $_SESSION['flash'] = 'Efetue o login';
                $this->redirect('/login');
        }
            $_SESSION['flash'] = 'Campos em branco não são permitidos.';
            $this->redirect('/resetPass/'.$hash['hash']);
    }

}
