<?php

require('function.php');

debug('マイページ');
debugLogStart();

require('auth.php');


$currentPageNum = (!empty($_GET['p'])) ? $_GET['p']: 1;

$category = (!empty($_GET['c_id'])) ? $_GET['c_id']: '';

$area = (!empty($_GET['a_id'])) ? $_GET['a_id']: '';

if(!is_int($currentPageNum)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:mypage.php");
}

$listSpan = 10;

$currentMinNum = (($currentPageNum-1)*$listSpan);

$dbPostData = getPostList($currentMinNum, $category, $area);


$dbCategoryData = getCategory();

$dbAreaData = getArea();

?>

<?php
$siteTitle='HOME';
require('head.php');
?>

<body>
<?php
require('header.php');
?>
<div class="search">
<form name="" method="get" class="searchForm">
    <h1 class="searchForm-category">カテゴリー</h1>
    <div class="searchForm-selectboxA">
        <select name="c_id" id="">
            <option value="0"
            <?php if(getFormData('c_id', true) == 0){echo 'selected';}?>>
            選択してください</option>
            <?php
            foreach($dbCategoryData as $key => $val){
                ?>
                <option value="<?php echo $val['id']?>"
                <?php if(getFormData('c_id', true) == $val['id'])
                {echo 'selected';}?>>
                <?php echo $val['name'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <h1 class="searchForm-category">エリア</h1>
    <div class="searchForm-selectboxB">
        <select name="a_id" id="">
            <option value="0"
            <?php if(getFormData('a_id', true) == 0){echo 'selected';}?>>
            選択してください</option>
            <?php
            foreach($dbAreaData as $key => $val){
            ?>
            <option value="<?php echo $val['id']?>"
            <?php if(getFormData('a_id', true) == $val['id'])
            {echo 'selected';}?>>
            <?php echo $val['name'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <input type="submit" class="search-button" value="検索">
</form>
</div>

    <div class="search-title">
        <div class="search-left"><span class="total-num">
            <?php echo sanitize($dbPostData['total']);?>
        </span>件の記事が見つかりました</div>
        <div class="search-right"><span class="num"><?php echo (!empty($dbPostData['data']))?
        $currentMinNum+1: 0;?></span>-<span class="num"><?php echo $currentMinNum+count(array($dbPostData['data']));?></span>件
        /<span><?php echo sanitize($dbPostData['total']);?></span>件中
        </div>
    </div>
    
<section id="results">
    <div class="list">
        <?php foreach((array)$dbPostData['data'] as $key => $val):?>
        <a href="postDetail.php<?php echo (!empty(appendGetParam())) ?
        appendGetParam().'&p_id='.$val['id']: '?p_id='.$val['id'];?>" class="post">
        
        <div class="post-head">
            <img src="<?php echo sanitize($val['pic1']);?>"
            alt="<?php echo sanitize($val['title']);?>" class="post-head-pics">
        </div>
        <div class="post-body">
            <p class="post-title"><?php echo sanitize($val['title']);?></p>
        </div>
        <div class="post-foot">
            <p class="post-title"><?php echo sanitize($val['country']);?></p>
        </div>
        </a>
        <?php endforeach;?>
    </div>

    <?php pagination($currentPageNum, $dbPostData['total_page'],'&c_id='.$category.'&a_id='.$area);?>
</section>

<?php require('footer.php');
?>