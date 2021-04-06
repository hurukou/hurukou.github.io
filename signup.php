<?php

require('function.php');

if (!empty($_POST)) {

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_retype = $_POST['pass_retype'];



    if (empty($err_msg)) {



        if (empty($err_msg)) {

            try {
                $pdo = db_connection();

                $sql = 'INSERT INTO users (email, password, login_time, create_date) VALUES(:email, :pass, :login_time, :create_date)';
                $data = array(
                    ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                    ':login_time' => date('Y-m-d H:i:s'),
                    ':create_date' => date('Y-m-d H:i:s')
                );

                $stmt = queryex($pdo, $sql, $data);

                if ($stmt) {
                    $ex_date = 60 * 60;

                    $_SESSION['login_time'] = time();
                    $_SESSION['login_ex'] = $ex_date;
                    $_SESSION['user_id'] = $pdo->lastInsertId();

                    header("Location:index.php");
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
                    <h2 class="title">無料会員登録</h2>
                    <div class="err_code">
                        <?php
                        if (!empty($err_msg['err_code'])) echo $err_msg['err_code'];
                        ?>
                    </div>
                    <span>メールアドレス <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
                    </span>
                    <label class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email'];  ?> </label>
                    　　　　　　　　　
                    <span class="wordpass">パスワード※英数字６文字以上 　</span>
                    <input type="password" name="pass" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                    <label class="err_msg">
                        <?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
                    </label>
                    <span class="wordpass">パスワード（再入力） </span>
                    <input type="password" name="pass_retype" value="<?php if (!empty($_POST['pass_retype'])) echo $_POST['pass_retype']; ?>">
                    <label class="err_msg">
                        <?php if (!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?>
                    </label>

                    <div class="btn-container">
                        <input type="submit" class="btn signup" value="登録">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>