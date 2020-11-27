<?php

require('function.php');
    
    
require('auth.php');

$dbUserData = getUserdata($_SESSION['user_id']);

if(!empty($_POST)){
    
    $username = $_POST['username'];
    $pic = (!empty($_FILES['pic']['name'])) ?
    uploadImg($_FILES['pic'],'pic') : '';
    $pic = (empty($pic) && !empty($dbUserData['pic'])) ? $dbUserData['pic'] : $pic;
    $twitter = $_POST['twitter'];
    $youtube = $_POST['youtube'];
    $email = $_POST['email'];
    $results = $_POST['results'];
    $introduction = $_POST['introduction'];
    
    if($dbUserData['username'] !== $username){
        validMax($username, 'username');
    }
  if($dbUserData['twitter'] !== $twitter){
      validMax($twitter, 'twitter');
  }
  if($dbUserData['youtube' !== $youtube]){
      validMax($youtube, 'youtube');
  }
  if($dbUserData['email'] !== $email){
      validMax($email, 'email');
      
      if(empty(err_msg['email'])){
          validMaildup($email);
      }
  } 
    
    if(empty($err_msg)){
        
        try{
            $pdo = db_connection();
            $sql = 'UPDATE users SET username = :u_name, twitter = :twitter, youtube = :youtube, email = :email, results = :results, introduction = :introduction,  pic = :pic WHERE id = :u_id';
            $data = array(':u_name' => $username, ':twitter' => $twitter, ':youtube' => $youtube, ':email' => $email, ':results' => $results, ':introduction' => $introduction, ':pic' => $pic, ':u_id' => $dbUserData['id']);
            
            $stmt = queryex($pdo, $sql, $data);
            
            if($stmt){
                $_SESSION['msg_update'] = UPD02;
                header("Location:mypage.php");
            }
        } catch(Exception $e) {
            error_log('エラー:' . $e->getMessage());
            $err_msg['err_code'] = MSG07;
        }
    }
}
?>
<?php
require('head.php');
?>


<body class="page-1colum">

    <?php
    require('header.php');
    ?>

    <!--メインコンテンツ-->
    <div　id="contents" class="site-width">
        <!-- メイン -->
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form_prof" enctype="multipart/form-data">
                    <h2 class="title">プロフィール編集</h2>

                    <div class="err_code">
                        <?php
                    if(!empty($err_msg['err_code'])) echo $err_msg['err_code'];
                    ?>
                    </div>
                    <span>名前<input type="text" name="username" value="<?php echo $getData('username'); ?>"></span>
                    <label class="err_msg">
                        <?php
                    if(!empty($err_msg['username'])) echo $err_msg['username'];
                    ?>
                    </label>
                    <span class="profImg">プロフィール画像<input type="hidden" name="MAX_FILE_SIZE" value="3145728"><input type="file" name="pic" class="input-pic">
                        <img src="<?php echo getData('pic'); ?>" alt="" class="input-img" style="<?php if(empty(getData('pic'))) echo 'display:none;' ?>">
                        ドラッグ＆ドロップ
                    </span>
                    <label class="err_msg">
                        <?php
                    if(!empty($err_msg['pic'])) echo $err_msg['pic'];
                    ?>
                    </label>
                    <span>Twitter<input type="text" name="twitter" value="<?php echo $getData('twitter'); ?>"></span>
                    <label class="err_msg">
                        <?php
                    if(!empty($err_msg['twitter'])) echo $err_msg['twitter'];
                    ?>
                    </label>
                    <span>Youtubeチャンネル<input type="text" name="youtube" value="<?php echo $getData('youtube'); ?>"></span>
                    <label class="err_msg">
                        <?php
                    if(!empty($err_msg['youtube'])) echo $err_msg['youtube'];
                    ?>
                    </label>
                    <span>実績<textarea name="results" cols="50" rows="5"><?php echo $getData('results'); ?></textarea></span>
                    <label class="err_msg">
                        <?php
                    if(!empty($err_msg['results'])) echo $err_msg['results'];
                    ?>
                    </label>
                    <span>自己紹介<textarea name="introduction" cols="50" rows="5"><?php echo $getData('introduction'); ?>"></textarea></span>
                    <label class="err_msg">
                        <?php
                    if(!empty($err_msg['introduction'])) echo $err_msg['introduction'];
                    ?>
                    </label>
                    <div class="btn-container">
                        <input type="submit" class="btn prof" value="変更する">
                    </div>

                </form>
            </div>
        </section>

        <!-- サイドバー -->
        <?php
        require('sidebar.php');
        ?>
    </div>
</body>
