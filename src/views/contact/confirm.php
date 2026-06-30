<?php $title = "入力内容の確認"; ?>
<?php require BASE_PATH . "/views/components/header.php"; ?>

<h1 class="page-title">入力内容の確認</h1>
<p class="page-lead">以下の内容でよろしければ送信してください。</p>

<div class="card">
    <dl class="confirm-list">
        <div>
            <dt>お名前</dt>
            <dd><?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?></dd>
        </div>

        <div>
            <dt>メールアドレス</dt>
            <dd><?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?></dd>
        </div>

        <div>
            <dt>お問い合わせ種別</dt>
            <dd><?= htmlspecialchars($type, ENT_QUOTES, "UTF-8") ?></dd>
        </div>

        <div>
            <dt>お問い合わせ内容</dt>
            <dd><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></dd>
        </div>
    </dl>

    <div class="form-actions">
        <form method="post" action="/contact/complete">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, "UTF-8") ?>">
            <button class="btn btn-primary">この内容で送信する</button>
        </form>

        <form method="get" action="/contact">
            <button type="submit" class="btn btn-secondary">入力内容を修正する</button>
        </form>
    </div>
</div>

<?php require BASE_PATH . "/views/components/footer.php"; ?>
