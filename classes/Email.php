<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;
    protected $dominio;
    protected $mailHost;
    protected $mailPort;
    protected $mailUsername;
    protected $mailPass;

    public function __construct($email, $nombre, $token)
    {
        $this->dominio = $_ENV['APP_HOST'];
        $this->mailHost = $_ENV['MAIL_HOST'];
        $this->mailPort = $_ENV['MAIL_PORT'];
        $this->mailUsername = $_ENV['MAIL_USERNAME'];
        $this->mailPass = $_ENV['MAIL_PASS'];

        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASS'];

        $mail->setFrom($_ENV['MAIL_USERNAME'], 'UpTask');
        $mail->addAddress($this->email, $this->nombre);

        $mail->Subject = 'Confirma tu Cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en UpTask solo debes confirmarla en el siguiente enlace:</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $this->dominio . "/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tú no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        $mail->send();
    }

    public function enviarInstrucciones()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASS'];

        $mail->setFrom($_ENV['MAIL_USERNAME'], 'UpTask');
        $mail->addAddress($this->email, $this->nombre);

        $mail->Subject = 'Restablece tu Password';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Parece que has olvidado tu passoword, sigue el siguiente enlace para recuperarlo:</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $this->dominio . "/reestablecer?token=" . $this->token . "'>Restablecer Password</a></p>";
        $contenido .= "<p>Si tú no pediste esto, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        $mail->send();
    }

}