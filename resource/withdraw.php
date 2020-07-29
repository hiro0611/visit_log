<?php

require('function.php');

debug('退会');
debugLogStart();

require('auth.php');

if(!empty($_POST)){

    try{

        $dbh = dbConnect();
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
        $sql2 = 'UPDATE post SET delete_flg = 1 WHERE user_id = :id';
        $data = array(':id'=>$_SESSION['user_id']);
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);

        if($stmt1){
            session_destroy();
            header("Location:login.php");
        }else{
            $err_msg['common'] = MSG02;
        }
    }catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG02;
    }
}
?>

<?php
$siteTitle = '退会';
require('head.php');
?>

<body>
<?php
require('header.php');
?>

<form class="withdrawMsg">
    退会を希望しますか？
<div>
    <button class="withdraw-button"><a href="mypage.php">いいえ</a></button>
    <button name="submit" class="withdraw-button">はい</button>
</div>
</form>

<?php
require('footer.php');
?>

</body>
</html>