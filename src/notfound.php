<?php
$title = "ページが見つかりません";
http_response_code(404);
?>
<?php require BASE_PATH . "/views/components/header.php"; ?>

<h1 class="page-title">ページが見つかりません</h1>

<div class="card complete-card">
    <p>お探しのページは存在しないか、移動した可能性があります。</p>
    <a href="/" class="link-home">お問い合わせページへ戻る</a>
</div>

<?php require BASE_PATH . "/views/components/footer.php"; ?>
