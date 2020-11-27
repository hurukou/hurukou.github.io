<!-- ヘッダー -->
<header>
    <div class="site-width">
        <h1><a href="index.php">Poker Coach</a></h1>
        <nav id="top-nav">
            <ul>
                <?php
                if(empty($_SESSION['user_id'])){
                ?>
                <li><a href="login_form.php" class="btn btn-main">ログイン</a></li>
                <li><a href="signup.php" class="btn btn-sub">会員登録</a></li>
                <?php
                }else{
                ?>
                <li><a href="mypage.php" class="btn btn-main">マイページ</a></li>
                <li><a href="logout.php" class="btn btn-sub">ログアウト</a></li>
                <?php
                }
                ?>
            </ul>
        </nav>
    </div>   
</header>
