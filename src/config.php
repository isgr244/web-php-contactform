<?php

require_once BASE_PATH . "/lib/env.php";

load_env(BASE_PATH . "/.env");

return [
    "smtp" => [
        "host" => env("SMTP_HOST", "smtp.gol.com"),
        "port" => (int) env("SMTP_PORT", "465"),
        "encryption" => env("SMTP_ENCRYPTION", "ssl"),
        "username" => env("SMTP_USERNAME"),
        "password" => env("SMTP_PASSWORD"),
        "from_email" => env("MAIL_FROM_EMAIL", "no-reply@isgrtech.com"),
        "from_name" => env("MAIL_FROM_NAME", "お問い合わせフォーム"),
        // SMTPの封筒送信者（MAIL FROM）。gol.comは認証アカウントと異なる送信者を拒否するため固定
        "envelope_from" => env("SMTP_ENVELOPE_FROM", "isgrtech-info@gol.com"),
    ],
    "mail_to" => env("MAIL_TO", "isgrtech-info@gol.com"),
    "turnstile" => [
        "site_key" => env("TURNSTILE_SITE_KEY", "1x00000000000000000000AA"),
        "secret_key" => env("TURNSTILE_SECRET_KEY", "1x0000000000000000000000000000000AA"),
    ],
    // 連投とみなす間隔（秒）
    "submit_interval_seconds" => (int) env("SUBMIT_INTERVAL_SECONDS", "10"),
];
