<?php

require('function.php');

require('auth.php');

if(!empty($_POST)){

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $login_save = (!empty($_POST['save'])) ? true : false;

    //未入力チェック//
    validInput($email,'email');
    validInput($pass,'pass');

    if(empty($err_msg)){

        //メールアドレスの形式チェック//
        validMail($email, 'email');
        //メールアドレスの最大文字数チェック
        validMax($email, 'email');

        //パスワードの半角英数字チェック//
        validHalfWid($pass, 'pass');
        //パスワードの最大文字数チェック//
        validMax($pass, 'pass');
        //パスワードの最小文字数チェック//
        validMin($pass, 'pass');

     
        if(empty($err_msg)){

            try {
                $pdo = db_connection();

                $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email' => $email);
                          
                $stmt = queryex($pdo, $sql, $data);
                
                $query_result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!empty($query_result) && password_verify($pass,array_shift($query_result))){
                    
                
                
                $ex_date = 60*60;
                
                $_SESSION['login_time'] = time();
                
                if($login_save){
                    $_SESSION['login_ex'] = $ex_date * 24 * 30;
                }else{
                    $_SESSION['login_ex'] = $ex_date;
                }
                
                $_SESSION['user_id'] = $query_result['id'];
          
                header("Location:index.php");
                }else{
                    $err_msg['err_code'] = MSG09;
                }
            } catch (Exception $e) {
                error_log('エラー:' . $e->getMessage());
                $err_msg['err_code'] = MSG07;
            }

        }
    }
}
?>
<?php
require('head.php');
?>

<body class="page-1colum">

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

        <div id="main">

            <div class="form-container">

                <form action="" method="post" class="form">
                    <h2 class="title">ユーザーログイン</h2>
                    <div class="err_msg">
                        <?php
                        if(!empty($err_msg['err_code'])) echo $err_msg['err_code'];
                        ?>
                    </div>
                    <span>メールアドレス <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                    </span>
                    <label class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email'];  ?> </label>

                    <span class="wordpass">パスワード                        　</span>
                    <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                    <label class="err_msg">
                        <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
                    </label>
                    
                    <label>
                    <input type="checkbox" name="login_save">次回ログインを省略する
                    </label>
                   
                    <div class="btn-container">
                        <input type="submit" class="btn signup" value="ログイン">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
