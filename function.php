<?php

ini_set('log_errors','on');
ini_set('error_log','php.log');


// セッション\
//define("DS",DIRECTORY_SEPARATOR);
//session_save_path("D:".DS."var".DS."tmp");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime ', 60*60*24*30);
session_start();
session_regenerate_id();

//定数//
//エラーメッセージ用定数//
define('ERR01','入力必須項目です。');
define('ERR02','メールアドレスの形式でご入力してください');
define('ERR03','パスワード(再入力)が間違っております');
define('ERR04','半角英数字でご入力ください');
define('ERR05','6文字以上でご入力ください');
define('ERR06','256文字以内でご入力ください');
define('ERR07','エラーが発生しました。しばらくしてからもう一度お試しください。');
define('ERR08','そのメールアドレスは既に登録されています');
define('ERR09','メールアドレスまたはパスワードが違います');
define('ERR10','正しくありません');
define('SUC01', 'パスワードの変更が完了しました');
define('SUC02', 'プロフィールの変更が完了しました');
define('SUC03', 'メールを送信が完了しました');
define('SUC04', '登録が完了しました');
define('SUC05', '購入が完了しました!相手と連絡を取りましょう！');


//エラーメッセージ格納用
$err_msg = array();

//バリデーション関数//

//未入力チェック//
function validInput($str,$key) {
    if(empty($str)){
        global $err_msg;
        $err_msg[$key] = ERR01;
    }
}

//メールアドレス形式チェック//
function validMail($str,$key) {
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = ERR02;
    }
}

//メールアドレス重複チェック//
function validMaildup($email){
    global $err_msg;
    
    try {
        $db = db_connection();
        
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryex($db, $sql, $data);
        $query_result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($query_result))){
            $err_msg['email'] = ERR08;
        }
    } catch(exception $e) {
        error_log('エラー:' . $e->getMessage());
        $err_msg['err_code'] = ERR07;
    }
}
//同値チェック//
function validSameValue($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = ERR03;
     }
}

//最小文字数チェック//
function validMin($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = ERR05;
    }
}

//最大文字数チェック//
function validMax($str, $key, $max = 255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = ERR06;
    }
}

//半角チェック//
function validHalfWid($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = ERR04;
    }
}

//selectbox(category)チェック
function validCategory($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = ERR10;
    }
}

//youtube埋め込みコード//


//DB//

//DB接続//
function db_connection(){
    $db = 'mysql:dbname=pokercoach;host=localhost;charset=utf8';
    $user = 'root';
    $password = '';
    $options = array(
       
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,

        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $pdo = new PDO($db, $user, $password, $options);
    return $pdo;
}



//sql実行//
function queryex($pdo, $sql, $data){
    $stmt = $pdo->prepare($sql);
    
    if(!$stmt->execute($data)){
        $err_msg['err_code'] = ERR07;
        return 0;
    }
    return $stmt;
}

function getUserData($u_id){
    try {
        $db = db_connection();
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryex($db, $sql, $data);
            
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    } catch(Exception $e) {
      error_log('エラー:' . $e->getMessage());
    }
}
//その他//

//サニタイズ//
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}

//フォーム入力保持//
function formData($str, $flg = false){
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbUserData;
    if(!empty($dbUserData)){
        if(!empty($err_msg[$str])){
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbUserData[$str]);
            }
        }else{
            if(isset($method[$str]) && $method[$str] !== $dbUserData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbUserData[$str]);
            }
        }
    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}

function receiveProduct($u_id, $p_id){
    try {
        $db = db_connection();
        
        $sql = 'SELECT * FROM prodcut WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
        
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        
        $stmt = queryex($db, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e) {
        error_log('エラー:' . $e->getMessage());
    }
}

function getProductList($paginationMinNum =1, $category, $sort, $span = 20){
    try {
        $db = db_connection();
        
        $sql = 'SELECT id FROM product';
        if(!empty($category)) $sql .= 'WHERE category_id = '.$category;
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY price ASC';
                    break;
                case 2:
                    $sql .= ' ORDER BY price DESC';
                    break;
            }
        }
        $data = array();
        
        $stmt = queryex($db, $sql, $data);
        $rst['total'] = $stmt->rowCount(); //総レコード数
        $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
        if(!$stmt){
            return false;
        }
        //ページ用のsql文
        $sql = 'SELECT * FROM product';
        if(!empty($category)) $sql .= 'WHERE category_id = '.$category;
        if(!empty($sort)){
            switch($sort){
            case 1:
            $sql .= ' ORDER BY price ASC';
            break;
            case 2:
            $sql .= ' ORDER BY price DESC';
            break;
        
        }
        }
        $sql .= ' LIMIT '.$span.' OFFSET '.$paginationMinNum;
        $data = array();
        
        $stmt = queryex($db, $sql, $data);
        if($stmt){
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー:' . $e->getMessage());
    }
}

function getProductInfo($p_id){
    
    try {
        $db = db_connection();
        $sql = 'SELECT p.id , p.title , p.content, p.ask, p.place, p.price, p.pic, p.user_id, p.create_date, p.update_date, c.name AS category  FROM product AS p LEFT JOIN category AS c ON p.category_id = c.id WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
        $data = array(':p_id' => $p_id);
        $stmt = queryex($db, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
} catch (Exception $e) {
        error_log('エラー:' . $e->getMessage());
    }
}
    
function getMesgBoard($id){
    try {
        // DBへ接続
        $db = db_connection();
        // SQL文
        $sql = 'SELECT m.id AS m_id, product_id, board_id, send_date, to_user, from_user, sale_user, buy_user, msg, b.create_date FROM message AS m RIGHT JOIN board AS b ON b.id = m.board_id WHERE b.id = :id AND m.delete_flg = 0 ORDER BY send_date ASC';
        $data = array(':id' => $id);
        // クエリ実行
        $stmt = queryex($db, $sql, $data);

        if($stmt){
            // クエリ結果の全データを返却
            return $stmt->fetchAll();
        }else{
            return false;
        }

    } catch (Exception $e) {
        error_log('エラー:' . $e->getMessage());
    }
}

function getCategory(){
   try{
       $db = db_connection();
        
       $sql = 'SELECT * FROM category';
       $data = array();
        
       $stmt = queryex($db, $sql, $data);
       
       if($stmt){
           return $stmt->fetchAll();
       }else{
           return false;
       }
   }catch (Exception $e) {
       error_log('エラー:' . $e->getMessage());
   }
}
    
//画像処理
function upImg($file, $key){
    if(isset($file['error']) && is_int($file['error'])) {
        try {
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                    throw new RuntimeException('その他のエラーが発生しました');
            }
            
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG,IMAGETYPE_PNG], true)) {
                throw new RuntimeException('画像形式が未対応です');
            }
            
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'], $path)) {
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            chmod($path,0644);
            
            return $path;
        } catch (RuntimeException $e) {
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

//ページネーション//

function currentPagination($paginationNum, $totalPageNum, $link = '', $pageNum = 5){
    //　現在のページが、すべてのページと同じですべてのページが表示項目数以上なら、左にリンクを４個出す
    if($paginationNum == $totalPageNum && $totalPageNum > $pageNum){
        $pageMinNum = $paginationNum -4;
        $pageMaxNum = $paginationNum;
    //現在のページが、すべてのページの１ページ前なら、左に３個、右に１個出す
    }elseif($paginationNum == ($totalPageNum-1) && $totalPageNum > $pageNum){
        $pageMinNum = $paginationNum -3;
        $pageMaxNum = $paginationNum +1;
        //　現在のページが２の場合は左に１個、右に３個出す
    }elseif($paginationNum == 2 && $totalPageNum > $pageNum){
        $pageMinNum = $paginationNum -1;
        $pageMaxNum = $paginationNum +3;
    //現在のページが１の場合は左に何も表示させない。右に５個だす。
    }elseif($paginationNum == 1 && $totalPageNum > $pageNum){
        $pageMinNum = $paginationNum;
        $pageMaxNum = 5;
    //すべてのページが表示項目数より少ない場合は、すべてのページをループのmax,ループのminを１に設定
    }elseif($totalPageNum < $pageNum){
        $pageMinNum = 1;
        $pageMaxNum = $totalPageNum;
    // それ以外は左に２個出す。
    }else{
        $pageMinNum = $paginationNum -2;
        $pageMaxNum = $paginationNum +2;
    }
    
    echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
    if($paginationNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
    }
    for($i = $pageMinNum; $i <=  $pageMaxNum; $i++){
        echo '<li class="list-item ';
        if($paginationNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
    }
    if($paginationNum != $pageMaxNum && $pageMaxNum > 1){
        echo '<li class="list-item"><a href="?p='.$pageMaxNum.$link.'">&gt;</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}
//画像表示用関数
function showImg($path){
    if(empty($path)){
        return 'img/sample-img.png';
    }else{
        return $path;
    }
}

//GETパラメータ付与
function getPara($del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$del_key,true)){
                $str .= $key. '='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        return $str;
    }
}
