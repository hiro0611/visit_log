<header class="header">
    <h1 class="header-title">visit LOG</h1>
     <ul class="nav">
         <?php if(empty($_SESSION['user_id'])){?>
             <li class="nav-menu"><a href="aboutService.php">サービスについて</a></li>
             <li class="nav-menu"><a href="login.php">ログイン</a></li>
             <li class="nav-menu"><a href="signup.php">登録</a></li>
         <?php }else{ ?>
         <li class="nav-menu"><a href="mypage.php">マイページ</a></li>
         <li class="nav-menu"><a href="postLog.php">投稿する</a></li>
         <li class="nav-menu"><a href="profileEdit.php">プロフィール編集</a></li>
         <li class="nav-menu"><a href="passEdit.php">パスワード変更</a></li>
         <li class="nav-menu"><a href="withdraw.php">退会</a></li>
         <li class="nav-menu"><a href="logout.php">ログアウト</a></li>
         <?php } ?>
     </ul>
</header>