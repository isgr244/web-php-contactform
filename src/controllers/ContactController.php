<?php

class ContactController
{
    private const TYPES = ["ご相談・面談", "見積依頼", "その他"];

    /** ハニーポット用のダミー項目名 */
    private const HONEYPOT_FIELD = "company";

    /**
     * 問い合わせ入力
     * @return void
     */
    public function form()
    {
        $name = "";
        $email = "";
        $type = "";
        $message = "";
        $errors = [];
        $csrfToken = $this->csrfToken();
        $turnstileSiteKey = $this->config()["turnstile"]["site_key"];

        require BASE_PATH . "/views/contact/form.php";
    }

    /**
     * 入力確認
     * @return void
     */
    public function confirm()
    {
        // ハニーポット：人間には見えない項目が埋まっていればbotとみなして即座に拒否
        if (trim($_POST[self::HONEYPOT_FIELD] ?? "") !== "") {
            http_response_code(400);
            exit("Bad Request");
        }

        $csrfToken = $this->csrfToken();
        if (!hash_equals($csrfToken, $_POST["csrf_token"] ?? "")) {
            http_response_code(400);
            exit("Bad Request");
        }

        $turnstileSiteKey = $this->config()["turnstile"]["site_key"];

        // 送信間隔の制限（連投対策）。古い送信を消費しないよう先にチェックする
        if ($this->isTooSoon()) {
            $name = trim($_POST["name"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $type = trim($_POST["type"] ?? "");
            $message = trim($_POST["message"] ?? "");
            $errors = ["送信間隔が短すぎます。しばらく時間をおいて再度お試しください。"];
            require BASE_PATH . "/views/contact/form.php";
            return;
        }
        $_SESSION["last_submit_at"] = time();

        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $type = trim($_POST["type"] ?? "");
        $message = trim($_POST["message"] ?? "");

        $errors = $this->validate($name, $email, $type, $message);

        // Cloudflare Turnstile検証
        $config = $this->config();
        $turnstileToken = $_POST["cf-turnstile-response"] ?? "";
        $verified = Turnstile::verify(
            $turnstileToken,
            $config["turnstile"]["secret_key"],
            $_SERVER["REMOTE_ADDR"] ?? ""
        );
        if (!$verified) {
            $errors[] = "スパム防止のための確認が完了しませんでした。もう一度お試しください。";
        }

        if (!empty($errors)) {
            require BASE_PATH . "/views/contact/form.php";
            return;
        }

        $token = bin2hex(random_bytes(16));
        $_SESSION["contact"] = compact("name", "email", "type", "message");
        $_SESSION["contact_token"] = $token;
        // $token はビューでも使用する

        require BASE_PATH . "/views/contact/confirm.php";
    }

    /**
     * 送信完了
     * @return void
     */
    public function complete()
    {
        $token = $_POST["token"] ?? "";

        if (
            empty($_SESSION["contact"])
            || empty($_SESSION["contact_token"])
            || !hash_equals($_SESSION["contact_token"], $token)
        ) {
            require BASE_PATH . "/notfound.php";
            return;
        }

        $contact = $_SESSION["contact"];

        require_once BASE_PATH . "/vendor/autoload.php";
        require_once BASE_PATH . "/lib/Mailer.php";

        $sent = Mailer::sendContact($contact);

        unset($_SESSION["contact"], $_SESSION["contact_token"]);

        if (!$sent) {
            $errors = ["送信に失敗しました。時間をおいて再度お試しください。"];
            $name = $contact["name"];
            $email = $contact["email"];
            $type = $contact["type"];
            $message = $contact["message"];
            $csrfToken = $this->csrfToken();
            $turnstileSiteKey = $this->config()["turnstile"]["site_key"];
            require BASE_PATH . "/views/contact/form.php";
            return;
        }

        require BASE_PATH . "/views/contact/complete.php";
    }

    /**
     * 入力値のバリデーション
     * @return array<string> エラーメッセージ一覧
     */
    private function validate(string $name, string $email, string $type, string $message): array
    {
        $errors = [];

        if ($name === "") {
            $errors[] = "お名前を入力してください。";
        }

        if ($email === "") {
            $errors[] = "メールアドレスを入力してください。";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "メールアドレスの形式が正しくありません。";
        }

        if (!in_array($type, self::TYPES, true)) {
            $errors[] = "お問い合わせ種別を選択してください。";
        }

        if ($message === "") {
            $errors[] = "お問い合わせ内容を入力してください。";
        }

        return $errors;
    }

    /**
     * セッションのCSRFトークンを取得（未生成なら発行する）
     * @return string
     */
    private function csrfToken(): string
    {
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }

        return $_SESSION["csrf_token"];
    }

    /**
     * 前回送信から一定間隔を空けずに連投していないか判定する
     * @return bool 間隔が短すぎる場合は true
     */
    private function isTooSoon(): bool
    {
        $lastSubmitAt = $_SESSION["last_submit_at"] ?? 0;
        $interval = $this->config()["submit_interval_seconds"];

        return (time() - $lastSubmitAt) < $interval;
    }

    private function config(): array
    {
        static $config = null;
        if ($config === null) {
            $config = require BASE_PATH . "/config.php";
            require_once BASE_PATH . "/lib/Turnstile.php";
        }

        return $config;
    }
}
