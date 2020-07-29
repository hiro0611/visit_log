<?php

require('function.php');

debug('ログアウト');
debugLogStart();

debug('ログアウトします');
session_destroy();
debug('ログインページへ遷移します');
header("Location:login.php");
?>
