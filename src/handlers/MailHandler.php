<?php

namespace src\handlers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

class MailHandler
{
  private $email = 'naoresponda@lhscode.com.br'; //Trocar e-mail aqui! email de teste
  private $password = 'Teste1234'; //Trocar senha aqui!
  private $host = 'mail.lhscode.com.br';  //Seu host
  private $port = 465;  //Porta do seu Host
  private $mail;

  public function __construct()
  {
    // Configurações do servidor 
    $this->mail = new PHPMailer(true);
    $this->serverSettings();
  }

  private function serverSettings()
  {
    //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $this->mail->isSMTP();                                            //Send using SMTP
    $this->mail->Host = $this->host;                  //Set the SMTP server to send through
    $this->mail->SMTPAuth = true;                                   //Enable SMTP authentication
    $this->mail->Username = $this->email;                     //SMTP username
    $this->mail->Password = $this->password;                               //SMTP password
    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $this->mail->Port = $this->port;              // Porta TCP para conectar, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` acima
    $this->mail->isHTML(true);                                  // Defina o formato do e-mail para HTML 
    $this->mail->CharSet = "utf-8";
    $this->mail->setFrom($this->email, 'Contato');
  }

  public function addAdress($email)
  {
    $this->mail->addAddress($email);
  }

  public function addReplyTo($email, $name)
  {
    $this->mail->addReplyTo($email, $name);
  }

  public function formatEmail($info)
  {
    $this->mail->Subject = $info['assunto'];
    $this->mail->Body    = $info['body'];
    $this->mail->AltBody = strip_tags($info['body']);
  }

  public function sendEmail()
  {
    if ($this->mail->send()) {
      return true;
    } else {
      return false;
    }
  }
}
