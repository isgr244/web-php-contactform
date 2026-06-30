<?php

/**
 * .envファイルを読み込み、未設定の環境変数として登録する
 * @return void
 */
function load_env(string $path): void
{
    static $loaded = false;
    if ($loaded || !is_file($path)) {
        return;
    }
    $loaded = true;

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === "" || str_starts_with($line, "#")) {
            continue;
        }

        [$key, $value] = array_pad(explode("=", $line, 2), 2, "");
        $key = trim($key);
        $value = trim($value, " \t\"'");

        if ($key !== "" && getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}

/**
 * 環境変数を取得する
 * @return string|null
 */
function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}
