<?php

require('function.php');

require('auth.php');

$dbFormData = getUser($_SESSION['user_id']);

if(!empty($_POST)){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $liveCountry = $_POST['liveCountry'];
    $tripCountry = $_POST['tripCountry'];
    $comment = $_POST['comment'];

    if($dbFormData['name'] !== $name){

        validRequired($name, 'name');
        validMaxLen($name, 'name');
        validMinLen3($name, 'name');
    }
    if($dbFormData['email'] !== $email){

        validRequired($email, 'email');
        validEmail($email, 'email');
        validMaxLen($email, 'email');
        validEmailDouble($email);
    }
    if($dbFormData['liveCountry'] !== $liveCountry){

        validMaxLen($liveCountry, 'liveCountry');
    }
    if($dbFormData['tripCountry'] !== $tripCountry){

        validMaxLen($tripCountry, 'tripCountry');
    }
    if($dbFormData['comment'] !== $comment){

        validMaxLen($comment, 'comment');
    }

    if(empty($err_msg)){

        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET name=:name, email=:email, liveCountry=:liveCountry,
            tripCountry=:tripCountry, comment=:comment';
            $data = array(':name'=>$name, ':email'=>$email, ':liveCountry'=>$liveCountry,
            ':tripCountry'=>$tripCountry, ':comment'=>$comment, ':user_id'=>$dbFormData['id']);
            $stmt = queryPost($dbh, $sql, $data);
            
            if($stmt){
                debug('クエリ成功');
                header("Location: mypage.php");
            }else{
                debug('クエリ失敗');
                $err_msg['common'] = MSG02;
            }
        }catch(Exception $e){
            error_log('エラー発生:'.$e->getMessage());
            $err_msg['common'] = MSG02;
        }
    }
}


?>

<?php
$siteTitle='プロフィール編集';
require('head.php');
?>

<body id="background-img">
<?php
require('header.php');
?>

<div class="form-container">
    <h2>プロフィール編集</h2>
    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
    <form action="" method="post" class="form">
        名前
        <input type="text" name="name" class="form-send" value="<?php echo getFormData('name');?>">
        <div class="err">
            <?php if(!empty($err_msg['name'])) echo $err_msg['name'];?>
        </div>
        Eメール
        <input type="text" name="email" class="form-send" value="<?php echo getFormData('email');?>">
        <div class="err">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
        </div>
        住んでいる国
        <input type="text" name="liveCountry" class="form-send" value="<?php echo getFormData('liveCountry');?>">
        <div class="err">
            <?php if(!empty($err_msg['liveCounty'])) echo $err_msg['liveCountry'];?>
        </div>
        行ったことがある国
        <input type="text" name="tripCountry" class="form-send" value="<?php echo getFormData('tripCountry')?>">
        <div class="err">
            <?php if(!empty($err_msg['tripCountry'])) echo $err_msg['tripCountry'];?>
        </div>
        自己紹介
        <textarea name="comment" cols="30" rows="10" class="form-introduce" value="<?php echo getFormData('comment')?>">
        </textarea>
        <div class="err">
            <?php if(!empty($err_msg['comment'])) echo $err_msg['comment'];?>
        </div>
        <input type="submit" class="form-button" value="編集する">
    </form>
</div>

<?php require('footer.php');
?>