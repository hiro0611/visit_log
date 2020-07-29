<?php

require('function.php');

debug('ポスト詳細ページ');
debugLogStart();

require('auth.php');

$p_id = (!empty($_GET['p_id']))? $_GET['p_id'] : '';

$dbFormData = (!empty($_GET['p_id'])) ? getPost($_SESSION['user_id'], $p_id):'';

$viewData = getCategoryOne($p_id);
$viewData2 = getAreaOne($p_id);

if(empty($viewData)){

    debug('エラー発生：不正な値が入りました');
    header("Location: mypage.php");
}
debug('取得したデータ:'.print_r($viewData, true));

?>

<?php
$siteTitle = '記事詳細';
require('head.php');
?>

<body>

<?php
require('header.php');
?>

<section class="detail">
<div class="post-img">
        <div class="post-img-panel">
            <img src="<?php echo showImg(sanitize($viewData['pic1']));?>"
            alt="">
        </div>
        <div class="post-img-panel">
            <img src="<?php echo showImg(sanitize($viewData['pic2']));?>"
            alt="">
        </div>
    </div>
    <h1 class="detail-title">タイトル</h1>
    <p class="article"><?php echo sanitize($viewData['title']);?></p>
    <h1 class="detail-title">国</h1>
    <p class="article"><?php echo sanitize($viewData['country']);?></p>

    <h1 class="detail-title">本文</h1>
    <p class="article"><?php echo sanitize($viewData['comment']);?></p>

    <div class="return-button">
        <a href="mypage.php<?php echo appendGetParam(array('p_id'));?>">
        記事一覧へ戻る</a>
    </div>
</section>

<?php
require('footer.php');
?>



