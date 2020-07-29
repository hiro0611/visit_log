<?php

require('function.php');

require('auth.php');

$userData = getUser($_SESSION['user_id']);

if(!empty($_POST)){

    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];

    validRequired($pass_old, 'pass_old');
    validRequired($pass_new, 'pass_new');
    validRequired($pass_new_re, 'pass_new_re');

    if(!empty($err_msg)){

        validPass($pass_old, 'pass_old');
        validPass($pass_new, 'pass_new');

        if(!password_verify($pass_old, $userData['pass'])){
            $err_msg['pass_old'] = MSG11;
        }
        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG12;
        }

        validMatch($pass_new, $pass_new_re, 'pass_new_re');

        if(!empty($err_msg)){

            try{
                $dbh = dbConnect();
                $sql = 'UPDATE users SET pass=:pass WHERE id=:id';
                $data = array(':id'=>$id,
                ':pass'=>password_hash($pass_new, PASSWORD_DEFAULT));
                $stmt = queryPost($dbh, $sql, $data);

                if($stmt){
                    $_SESSION['msg_success'] = SUC01;

                    $username = ($userData['name']) ? $userData['username']:'名無し';
                    $from = 'info@visitlog.com';
                    $to = $userData['email'];
                    $subject = 'パスワードの変更通知｜visitLog';
                    $comment = <<<EOT
                    {$username}様
                    パスワードの変更がされました。
                    visitLog team より
                    EOT;

                    sendMail($username, $from, $to, $subject, $comment);
                    header("Location: mypage.php");
                }
            }catch(Exception $e){
                error_log('エラー発生'.$e->getMessage());
                $err_msg['common'] = MSG02;
            }
        }
    }
}
?>

<?php
require('head.php');
$siteTitle='signup';
?>

<body id="background-img">
<?php
require('header.php');
?>

<div class="form-container">
    <h2>パスワード変更</h2>
    <form action="" method="post" class="form">
    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
        現在のパスワード
        <input type="text" name="pass_old" class="form-send" 
        value="<?php if(!empty($_POST['pass_old'])) echo $_POST['pass_old'];?>">
        <div class="err">
            <?php if(!empty($err_msg['pass_old'])) echo $err_msg['pass_old'];?>
        </div>
        新しいパスワード
        <input type="text" name="pass_new" class="form-send"
        value="<?php if(!empty($_POST['pass_new'])) echo $_POST['pass_new'];?>">
        <div class="err">
            <?php if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new'];?>
        </div>
        新しいパスワード(確認)
        <input type="password" name="pass_new_re" class="form-send"
        value="<?php if(!empty($_POST['pass_new_re'])) echo $_POST['pass_new_re'];?>">
        <div class="err">
            <?php if(!empty($err_msg['pass_new_re'])) echo $err_msg['pass_new_re'];?>
        </div> 
        <input type="submit" class="form-button" value="変更へ">
    </form>
</div>

<?php require('footer.php');
?>