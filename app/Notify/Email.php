<?php

namespace App\Notify;

use PHPMailer\PHPMailer\PHPMailer;

class Email extends NotifyProcess implements Notifiable
{
    /**
     * Email of receiver
     *
     * @var string
     */
    public $email;

    /**
     * Assign value to properties
     *
     * @return void
     */
    public function __construct()
    {
        $this->statusField = 'email_status';
        $this->body = 'email_body';
        $this->globalTemplate = 'email_template';
        $this->notifyConfig = 'mail_config';
    }

    /**
     * Send notification
     *
     * @return void|bool
     */
    public function send()
    {

        //get message from parent
        $message = $this->getMessage();
        if ($this->setting->en && $message) {
            //Send mail
            $methodName = $this->setting->mail_config->name;
            $method = $this->mailMethods($methodName);
            try {
                $this->$method();
//                $this->createLog('email');
            } catch (\Exception $e) {
                dd($e->getMessage());
//                $this->createErrorLog($e->getMessage());
//                session()->flash('mail_error', $e->getMessage());
            }
        }
    }

    /**
     * Get the method name
     *
     * @return string
     */
    protected function mailMethods($name)
    {
        $methods = [
            'php' => 'sendPhpMail',
            'smtp' => 'sendSmtpMail',
        ];
        return $methods[$name];
    }

    protected function sendPhpMail()
    {
        $general = $this->setting;
        $headers = "From: $general->site_name <$general->email_from> \r\n";
        $headers .= "Reply-To: $general->site_name <$general->email_from> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        @mail($this->email, $this->subject, $this->finalMessage, $headers);
    }

    protected function sendSmtpMail()
    {
        $mail = new PHPMailer(true);
        $general = $this->setting;
        $config = $general->mail_config;
        //Server settings
        $mail->isSMTP();
        $mail->Host = $config->host;
        $mail->SMTPAuth = true;
        $mail->Username = "info@ndloo.com";
        $mail->Password = "8;cn&IHryFK{";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($general->email_from, $general->site_name);
        $mail->addAddress($this->email, $this->receiverName);
        $mail->addReplyTo($general->email_from, $general->site_name);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $this->subject;
        $mail->Body = $this->finalMessage;
        $mail->send();
    }

    /**
     * Configure some properties
     *
     * @return void
     */
    public function prevConfiguration()
    {
        if ($this->user) {
            $this->email = $this->user->email;
            $this->receiverName = $this->user->fullname;
        }
        $this->toAddress = $this->email;
    }
}
