<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();

$err_msg = array();

define('MSG01', 'メールアドレスまたはパスワードが違います');
define('MSG02', 'エラーが発生しました。時間をおいて再度お試しください');
define('MSG03', '入力必須項目です');
define('MSG04', 'Eメール形式ではありません');
define('MSG05', '255文字以内で入力ください');
define('MSG06', '半角英数字で入力ください');
define('MSG07', '6文字以上で入力ください');
define('MSG08', '3文字以上で入力ください');
define('MSG09', '既に登録済みのメールアドレスです');
define('MSG10', 'パスワードが一致しません');
define('MSG11', 'パスワードに誤りがあります');
define('MSG12', '現在のパスワードとは違うパスワードを入力ください');

define('SUC01', 'パスワードを変更しました！');
define('SUC02', '登録しました');


function validRequired($str, $key){
    if(empty($str)){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

function validMaxLen($str, $key, $max=255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

function validMinLen6($str, $key, $min=6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG07;
    }
}

function validMinLen3($str, $key, $min=3){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG08;
    }
}

function validEmailDouble($email){

    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE :email = email';
        $data = array(':email'=> $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($result['count(*)'])){
            $err_msg['email'] = MSG09;
        }
    }catch(Exception $e){
        error_log('エラー発生:' .$e->getMessage());
        $err_msg['common'] = MSG02;
    }
}

function validMatch($str, $str2, $key){
    if($str !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
}

function validPass($str, $key){
    validHalf($str, $key);
    validMaxLen($str, $key);
    validMinLen6($str, $key);
}

function getUser($id){
    debug('ユーザー情報を取得する');
    
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE :id = id';
        $data = array(':id'=> $id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            debug('クエリ成功');
        }else{
            debug('クエリ失敗');
        }
    }catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getFormData($str){
    global $dbFormData;
    global $err_msg;

    if(!empty($dbFormData)){
        if(!empty($err_msg[$str])){
            if(isset($_POST[$str])){
                return $_POST[$str];
            }else{
                return $dbFormData[$str];
            }
        }else{
            if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
                return $_POST[$str];
            }else{
                return $dbFormData[$str];
            }
        }
    }else{
        if(isset($_POST['$str'])){
            return $_POST[$str];
        }
    }
}

function getPost($id, $p_id){

    debug('記事情報を取得します');

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM post WHERE id = :id AND p_id = :p_id
        AND delete_flg = 0';
        $data = array(':id'=> $id, 'p_id'=> $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}

function getPostList($currentMinNum = 1, $category, $area, $span = 20){

    debug('記事情報を取得します');

    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM post WHERE 1';
        if($category){
            $sql .= ' AND category = ' .$category;
        }
        if($area){
            $sql .= ' AND area = ' . $area;
        }
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
            debug('クエリ成功');
        }
        $rst['total'] = $stmt->rowCount();
        $rst['total_page'] = ceil($rst['total']/$span);

        if(!$stmt){
            return false;
        }

        $sql = 'SELECT * FROM post WHERE 1';
        if($category){
            $sql .= ' AND category = ' .$category;
        }
        if($area){
            $sql .= ' AND area = ' . $area;
        }
        $sql .= ' LIMIT ' .$span. ' OFFSET ' .$currentMinNum;

        $data = array();
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            debug('クエリ成功');
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生'.$e->getMessage());
    }
}

function getCategory(){

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log(('エラー発生:'.$e->getMessage()));
    }
}

function getArea(){

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM area';
        $data =array();
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log(('エラー発生:'.$e->getMessage()));
    }
}

function getCategoryOne($p_id){

    debug('記事情報を取得する');
    debug('記事情報:'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT p.id, p.category, p.title, p.area, p.country, p.comment, p.pic1, p.pic2, p.user_id,
        p.create_date, p.update_date, c.name AS category
        FROM post AS p LEFT JOIN category AS c ON p.category = c.id
        WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
        $data = array(':p_id'=> $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}

function getAreaOne($p_id){

    debug('記事情報を取得する');
    debug('記事情報:'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT p.id, p.category, p.title, p.area, p.country, p.comment, p.pic1, p.pic2, p.user_id,
        p.create_date, p.update_date, a.name AS area
        FROM post AS p LEFT JOIN area AS a ON p.area = a.id
        WHERE p.id = :p_id AND p.delete_flg = 0 AND a.delete_flg = 0';
        $data = array(':p_id'=> $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}


function dbConnect(){
    $dsn = 'mysql:dbname=visitLog_php;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
    );
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

$debug_flg = true;

function debug($str){
      global $debug_flg;
      if(!empty($debug_flg)){
          error_log('デバッグ:' .$str);
    }
}

function debugLogStart(){
    debug('画面表示処理開始');

    debug('セッションID:'.session_id());
    debug('セッション変数の中身:'.print_r($_SESSION, true));
    debug('現在日時タイムスタンプ:'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date']+$_SESSION['login_limit']));
    }
}


  function queryPost($dbh, $sql, $data){
      $stmt = $dbh->prepare($sql);
      $stmt -> execute($data);
      return $stmt;
  }

  
function pagination($currentPageNum, $totalPageNum, $link='', $pageColNum = 5){

    if($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum -4;
        $maxPageNum = $currentPageNum;
    }elseif($currentPageNum = ($totalPageNum -1) && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum -3;
        $maxPageNum = $currentPageNum +1;
    }elseif($currentPageNum = 2 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum -1;
        $maxPageNum = $currentPageNum +3;
    }elseif($currentPageNum = 1 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum;
        $maxPageNum = 5;
    }elseif($currentPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    }else{
        $minPageNum = $currentPageNum -2;
        $maxPageNum = $currentPageNum +2;
    }

    echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
    if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">先頭へ戻る</a></li>';
    }
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item';
        if($currentPageNum == $i){echo 'active';}
        echo '"><a href = "?p='.$i.$link.'">'.$i.'</a></li>';
    }
    if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
    }
    echo '</ul>';
    echo '</div>';
  }

function uploadImg($file, $key){

    debug('画像アップロード開始');
    debug('ファイル情報:'.print_r($file, true));

    if(isset($file['error']) && is_int($file['error'])){

        try{
            switch($file['error']){

                case UPLOAD_ERR_OK:
                break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルがまだ選択されておりません');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きいです');
                default://
                    throw new RuntimeException('その他のエラーが発生しました');
            }
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG],true)){
                throw new RuntimeException('画像形式が未対応です');
            }
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'], $path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス:'.$path);
            return $path;
        }catch(RuntimeException $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

function showImg($path){
    if(empty($path)){
        return 'img/sample.png';
    }else{
        return $path;
    }
}

function sanitize($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key, $arr_del_key, true)){
                $str .= $key .'=' .$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "utf-8");
        return $str;
    }
}

?>