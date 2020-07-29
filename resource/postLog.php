<?php

require('function.php');

require('auth.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id']: '';

$dbFormData = (!empty($p_id)) ? getPost($id, $p_id): '';

$editFlg = (empty($dbFormData)) ? false: true;

$dbCategoryData = getCategory();
$dbAreaData = getArea();

if(!empty($p_id) && empty($dbFormData)){
    header("Location:mypage.php");
}

if(!empty($_POST)){
    
    $category = $_POST['category'];
    $title = $_POST['title'];
    $area = $_POST['area'];
    $country = $_POST['country'];
    $comment = $_POST['comment'];

    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'],'pic1'): '';
    $pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1']: $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'],'pic2'): '';
    $pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2']: $pic2;

if(empty($dbFormData)){

    validRequired($title, 'title');
    validMaxLen($title, 'title');

    validRequired($country, 'country');
    validMaxLen($country, 'country');

    validRequired($comment, 'comment');
    validMaxLen($comment, 'comment', 500);

}else{
    if($dbFormData['title'] !== $title){
        validRequired($title, 'title');
        validMaxLen($title, 'title');
    }
    if($dbFormData['country'] !== $country){
        validRequired($country, 'country');
        validMaxLen($country, 'country');
    }
    if($dbFormData['comment'] !== $comment){
        validRequired($comment, 'comment');
        validMaxLen($comment, 'comment', 500);
    }
}

if(empty($err_msg)){

  try{

    $dbh = dbConnect();
    if($editFlg){
        $sql = 'UPDATE post SET :category=category, :title=title, :area=area,
        :country=country, :comment=comment, :pic1=pic1, :pic2=pic2 WHERE user_id=:id AND p_id = :p_id';
        $data = array(':category'=>$category, ':title'=>$title, ':area'=>$area,
        ':country'=>$country, 'comment'=>$comment, ':pic1'=>$pic1, ':pic2'=>$pic2, 
        ':id'=>$_SESSION['user_id'], ':p_id'=>$p_id);
    }else{
        $sql = 'INSERT INTO post(category, title, area, country, comment, pic1, pic2, user_id, create_date)
        VALUES(:category, :title, :area, :country, :comment, :pic1, :pic2, :id, :date)';
        $data = array(':category'=>$category, ':title'=>$title, ':area'=>$area,
        ':country'=>$country, 'comment'=>$comment, ':pic1'=>$pic1, ':pic2'=>$pic2, 
        ':id'=>$_SESSION['user_id'], ':date'=>date('Y-m-d H:i:s'));
    }
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
        $_SESSION['msg_success'] = SUC02;
        header("Location: mypage.php");
    }
  }catch(Exception $e){
      error_log('エラー発生:'.$e->getMessage());
  }
}
}
?>

<?php
$siteTitle='ポスト';
require('head.php');
?>

<body id="background-img">
<?php
require('header.php');
?>

<div class="form-container">
    <h2>投稿する</h2>
    <form action="" method="post" class="form" enctype="multipart/form-data">
    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>

        カテゴリー
        <select name="category" class="form-option">
        <option value="0" <?php if(getFormData('category') == 0){echo 'selected';}?>>
        選択をしてください</option>
        <?php foreach($dbCategoryData as $key => $val){
        ?>
        <option value="<?php echo $val['id']?>"
        <?php if(getFormData('category') == $val['id']){echo 'selected';}?>>
        <?php echo $val['name'];?>
        </option>
        <?php
        }
        ?>
        </select>
        <div class="err">
            <?php if(!empty($err_msg['category'])) echo $err_msg['category'];?>
        </div>

        タイトル
        <input type="text" name="title" class="form-send" value="<?php echo getFormData('name');?>">
        <div class="err">
            <?php if(!empty($err_msg['title'])) echo $err_msg['title']?>
        </div>

        エリア
        <select name="area" class="form-option">
        <option value="0" <?php if(getFormData('area') == 0){echo 'selected';}?>>
        選択をしてください</option>
        <?php foreach($dbAreaData as $key => $val){
        ?>
        <option value="<?php echo $val['id'];?>"
        <?php if(getFormData('area') == $val['id']){echo 'selected';}?>>
        <?php echo $val['name'];?>
        </option>
        <?php
        }
        ?>
        </select>
        <div class="err">
            <?php if(!empty($err_msg['area'])) echo $err_msg['area'];?>
        </div>

        国名
        <input type="text" name="country" class="form-send" value="<?php echo getFormData('country');?>">
        <div class="err">
            <?php if(!empty($err_msg['country'])) echo $err_msg['country'];?>
        </div>

        内容
        <textarea name="comment" cols="30" rows="10" class="form-introduce js-count">
            <?php echo getFormData('comment');?>
        </textarea>
        <div class="counter">
        <span class="showCount">0</span>/<span class="maxCount">500</span></div>
        <div class="err">
            <?php if(!empty($err_msg['comment'])) echo $err_msg['comment'];?>
        </div>

        <div class="imgDrop">
        <div class="imgDrop-container">
            画像１
            <label class="area-drop">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic1" class="input-file">
            <img src="<?php echo getFormData('pic1');?>" alt="" class="prev-img
            <?php if(empty(getFormData('pic2'))) echo '-none'?>">
            写真アップロード
            </label>
        <div class="err">
            <?php if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];?>
        </div></div>

        <div class="imgDrop-container">
            画像２
            <label class="area-drop">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic2" class="input-file">
            <img src="<?php echo getFormData('pic2');?>" alt="" class="prev-img
            <?php if(empty(getFormData('pic2'))) echo '-none'?>">
            写真アップロード
            </label>
        <div class="err">
            <?php if(!empty($err_msg['pic2'])) echo $err_msg['pic2'];?>
        </div></div>

        </div>
        <button class="form-button">ポスト</button>
    </form>
</div>

<?php require('footer.php');
?>