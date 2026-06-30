<?php $title = "お問い合わせ"; ?>
<?php require BASE_PATH . "/views/components/header.php"; ?>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<h1 class="page-title">お問い合わせ</h1>
<p class="page-lead">ご相談・見積依頼など、お気軽にご相談ください。</p>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="card">
    <form method="post" action="/contact/confirm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, "UTF-8") ?>">

        <div class="honeypot-field" aria-hidden="true">
            <label>
                会社名（入力しないでください）
                <input type="text" name="company" tabindex="-1" autocomplete="off">
            </label>
        </div>

        <div class="form-group">
            <label for="name">お名前<span class="required">必須</span></label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>" placeholder="石黒 太郎">
        </div>

        <div class="form-group">
            <label for="email">メールアドレス<span class="required">必須</span></label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>" placeholder="test@example.com">
        </div>

        <div class="form-group">
            <label for="type">お問い合わせ種別<span class="required">必須</span></label>
            <select id="type" name="type">
                <?php foreach (["ご相談・面談", "見積依頼", "その他"] as $option): ?>
                    <option value="<?= $option ?>" <?= $type === $option ? "selected" : "ご相談" ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="message">お問い合わせ内容<span class="required">必須</span></label>
            <textarea id="message" name="message"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></textarea>
        </div>

        <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($turnstileSiteKey, ENT_QUOTES, "UTF-8") ?>"></div>

        <div class="form-actions">
            <button class="btn btn-primary">入力内容を確認する</button>
        </div>
    </form>
</div>

<?php require BASE_PATH . "/views/components/footer.php"; ?>
