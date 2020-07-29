<?php

require('function.php');

if(!empty($_POST)){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    validRequired($name, 'name');
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){

    validMaxLen($name, 'name');
    validMinLen3($name, 'name');
    
    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validEmailDouble($email);

    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen6($pass, 'pass');

    if(empty($err_msg)){
    
    validMatch($pass, $pass_re, 'pass_re');

    try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO users(name, email, pass, login_time, create_date)
        VALUES(:name, :email, :pass, :login_time, :create_date)';
        $data = array(':name'=>$name, ':email'=>$email, ':pass'=>password_hash($pass, PASSWORD_DEFAULT),
        ':login_time'=> date('Y-m-d H:i:s'), 'create_date'=> date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            $sesLimit = 60*60;
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身:'.print_r($_SESSION, true));
            header('Location:mypage.php');
        }else{
            error_log('クエリに失敗しました');
            $err_msg['common'] = MSG02;
        }
        }catch(Exception $e){
            error_log('エラー発生:'.$e->getMessage());
            $err_msg['common'] = MSG02;
    }
  }
}
}


?>

<?php
$siteTitle='signup';
require('head.php');
?>

<body id="background-img">
<?php
require('header.php');
?>

<div class="form-container">
    <h2 class="form-container-title">登録</h2>
    <form action="" method="post" class="form">
    <div class="err">
    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?></div>
        名前
        <input type="text" name="name" class="form-send" 
        value="<?php if(!empty($_POST['name'])) echo $_POST['name'];?>">
        <div class="err">
            <?php if(!empty($err_msg['name'])) echo $err_msg['name'];?>
        </div>
        Eメール
        <input type="text" name="email" class="form-send"
        value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
        <div class="err">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
        </div>
        パスワード
        <input type="password" name="pass" class="form-send"
        value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
        <div class="err">
            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?>
        </div>
        パスワード(確認)
        <input type="password" name="pass_re" class="form-send"
        value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re'];?>">
        <div class="err">
            <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];?>
        </div>  
        <input type="submit" class="form-button" value="登録へ">
    </form>
</div>

<?php require('footer.php');
?>