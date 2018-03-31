<?php
namespace app\common\helper;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use app\index\model\System;
class MailHelper
{
    /**
     * 邮件发送：默认系统通知
     * @param string $title 邮件标题
     * @param string $content 邮件内容
     * @param array $email email 支持多个
     */
    public static function send($title = '', $content = '', $email = [], $config = [])
    {
        $config = $config ? : config('mail.no-reply');
        $mail = new Message();
        $mail->setFrom("{$config['showname']} <{$config['username']}>")
            ->setSubject($title)
            ->setHtmlBody($content);
        foreach ($email as $value) {
            $mail->addTo($value);
        }
        $mailer = new SmtpMailer($config);
        $mailer->send($mail);
    }
    /**
     * 邮件发送：系统异常
     * @param string $title
     * @param string $content
     * @param array $email
     */
    public static function errorSend($title = '', $content = '', $email = [])
    {
        $config = config('mail.system-error');
        $title= $email ? : System::getName('sitename').'错误日志';
        $email = $email ? : config('mail.email');
        self::send($title, $content, $email, $config);
    }
}