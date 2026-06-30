<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * 問い合わせ内容をメール送信する
     *
     * @param array $contact ["name" => string, "email" => string, "type" => string, "message" => string]
     * @return bool 送信成功なら true
     */
    public static function sendContact(array $contact): bool
    {
        $config = require BASE_PATH . "/config.php";
        $smtp = $config["smtp"];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $smtp["host"];
            $mail->Port = $smtp["port"];
            $mail->SMTPAuth = true;
            $mail->Username = $smtp["username"];
            $mail->Password = $smtp["password"];
            $mail->SMTPSecure = $smtp["encryption"];
            $mail->CharSet = "UTF-8";

            // ヘッダーのFromは表示用、封筒の送信者(Sender)はSMTP認証アカウントにする
            // （smtp.gol.comは認証アカウントと異なるMAIL FROMを拒否するため）
            $mail->setFrom($smtp["from_email"], $smtp["from_name"]);
            $mail->Sender = $smtp["envelope_from"];
            $mail->addAddress($config["mail_to"]);
            // 返信先はお客様が入力したメールアドレス
            $mail->addReplyTo($contact["email"], $contact["name"]);

            $mail->Subject = "お問い合わせが届きました";
            $mail->Body = self::buildBody($contact);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer error: " . $mail->ErrorInfo);
            return false;
        }
    }

    private static function buildBody(array $contact): string
    {
        $separator = str_repeat("-", 32);

        return <<<EOT
{$separator}
名前：{$contact["name"]}
メール：{$contact["email"]}
件名：{$contact["type"]}
本文：
{$contact["message"]}
{$separator}
EOT;
    }
}
