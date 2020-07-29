<?php

require('function.php');

require('auth.php');

if(!empty($_POST)){

 $email = $_POST['email'];
 $pass = $_POST['pass'];
 $pass_save = (!empty($_POST['pass_save'])) ? true : false;

 validRequired($email, 'email');
 validRequired($pass, 'pass');

if(empty($err_msg)){
 validEmail($email, 'email');
 validMaxLen($email, 'email');

 validHalf($pass, 'pass');
 validMaxLen($pass, 'pass');
 validMinLen6($pass, 'pass');

 if(empty($err_msg)){

    try{
        $dbh = dbConnect();
        $sql = 'SELECT pass, id FROM users WHERE email = :email';
        $data = array(':email'=>$email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        debug('クエリの結果:'.print_r($result, true));

        if(!empty($result) && password_verify($pass, array_shift($result))){

            debug('クエリ成功');
            $sesLimit = 60 * 60;
            $_SESSION['login_date'] = time();

            if($pass_save){
                debug('ログイン保持にチェックあります');
                $_SESSION['login_limit'] = $sesLimit * 24 * 30;
            }else{
                debug('ログイン保持にチェックありません');
                $_SESSION['login_limit'] = $sesLimit;
            }
            $_SESSION['user_id'] = $result['id'];
            debug('セッション変数の中身:'.print_r($_SESSION, true));
            debug('マイページへ遷移します');
            header("Location:mypage.php");
        }else{
            debug('パスワードがアンマッチです');
            $err_msg['common'] = MSG01;
        }
    }catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG02;
    }
 }
}
}
debug('画面表示処理終了');
?>


<?php
$siteTitle='ログイン';
require('head.php');
?>

<body id="background-img">
<?php
require('header.php');
?>

<div class="form-container">
    <h2 class="form-container-title">ログイン</h2>
    <form action="" method="post" class="form">
    <div class="err">
    <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
        メールアドレス
        <input type="text" name="email" class="form-send" 
        value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
    <div class="err">
    <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?><br>
    </div>
        パスワード
        <input type="password" name="pass" class="form-send"
        value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']?>">
    <div class="err">
    <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?><br>
    </div>
    <label>
        <input type="checkbox" name="pass_save">次回ログインを省略する
    </label>
        <input type="submit" class="form-button" value="ログイン">
    </form>
</div>

<?php require('footer.php');
?>