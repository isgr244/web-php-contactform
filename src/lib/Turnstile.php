<?php

class Turnstile
{
    private const VERIFY_URL = "https://challenges.cloudflare.com/turnstile/v0/siteverify";

    /**
     * Cloudflare Turnstileのトークンを検証する
     * @return bool 検証成功なら true
     */
    public static function verify(string $token, string $secretKey, string $remoteIp = ""): bool
    {
        if ($token === "" || $secretKey === "") {
            return false;
        }

        $ch = curl_init(self::VERIFY_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                "secret" => $secretKey,
                "response" => $token,
                "remoteip" => $remoteIp,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("Turnstile verify error: " . $error);
            return false;
        }

        $result = json_decode($response, true);
        return !empty($result["success"]);
    }
}
