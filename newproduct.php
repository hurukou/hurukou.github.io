<?php

require('function.php');

require('auth.php');

//画面表示用データ//
$p_id = (!empty($_GET['p_id'])) ? $_GET[p_id] : '';

//DBから商品データを取得
$dbUserData = (!empty($p_id)) ? receiveProduct($_SESSION['user_id'], $p_id) : '';

$edit_flg = (empty($dbUserData)) ? false : true;


//　パラメータ改ざん用//
if(!empty($p_id) && empty($dbUserData)){
    header("Location:mypage.php");
}

//POST送信時//

if(!empty($_POST)){
    
    $title = $_POST['title'];
    $price = (!empty($_POST['price'])) ? $_POST['price'] : 0;
    $place = $_POST['place'];
    $content = $_POST['content'];
    $ask = $_POST['ask'];
    $pic = ( !empty($_FILES['pic']['title']) ) ? upImg($_FILES['pic'],'pic') : '';
    $pic = (empty($pic) && !empty($dbUserData['pic'])) ? $dbUserData['pic'] : $pic;
    
    if(empty($dbUserData)){
        validInput($title, 'title');
        validMax($title, 'title');
        validInput($content, 'content');
        validMax($content, 'content', 5000);
        validInput($ask, 'ask');
        validMax($ask, 'ask', 5000);
        validInput($price, 'price');
        validHalfWid($price, 'price');
    }else{
        if($dbUserData['title'] !== $title){
            validInput($title, 'title');
            validMax($title, 'title');
        }
        if($dbUserData['content'] !== $content){
            validInput($content, 'content');
            validMax($content, 'content', 5000);
        }
        if($dbUserData['ask'] !== $ask){
            validInput($ask, 'ask');
            validMax($ask, 'ask', 5000);
        }
        if($dbUserData['price'] != $price){
            validInput($price, 'price');
            validHalfWid($price, 'price');
        }
    }
    
    if(empty($err_msg)){
        
        try{
            $db = db_connection();
            
            if($edit_flg){
                $sql = 'UPDATE product SET title = :title,  price = :price, place = :place, content = :content, ask = :ask, pic = :pic WHERE user_id = :u_id AND id = :p_id';
                
                $data = array(':title' => $title ,  ':price' => $price, ':place' => $place, ':content' => $contet, ':ask' => $ask, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
            }else{
                $sql = 'insert into product (title,  price, place, content, ask, pic, user_id, create_date ) values (:title,  :price, :place, :content, :ask, :pic, :u_id, :date)';
                $data = array(':title' => $title ,  ':price' => $price, ':place' => $place, ':content' => $content, ':ask' => $ask, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            }
            $stmt = queryex($db, $sql, $data);
            
            if($stmt){
                $_SESSION['msg_update'] = UPD02;
                header("Location:mypage.php");
            }
        }catch(Exception $e) {
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


<div id="contents" class="site-width">
    <h1 class="page-title"><?php echo (!$edit_flg) ? '募集する' : '編集する'; ?></h1>
    
   <section id="main">
      <div class="form-container">
          <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%,box-sizing:border-box">
              <div class="err_code">
                  <?php
                  if(!empty($err_msg['err_code'])) echo $err_msg['err_code'];
                  ?>
             </div>
              <div>タイトル<span class="Required">必須</span>
              <input type="text" name="title" value="<?php echo formData('title') ?>">
              </div>
              <div class="err_msg">
                  <?php
                   if(!empty($err_msg['title'])) echo $err_msg['title'];
                  ?>
              </div>
              <div>開催場所</div>
              <select name="place" id="">
                  <option value="0">オンライン</option>
              </select>
              <select name="place" id="">
                  <option value="相談して決定">相談して決定</option>
                  <option value="Discord">Discord</option>
                  <option value="Skype">Skype</option>
                  <option value="その他">その他</option>
              </select>
              <div class="err_msg">
                  <?php
                  if(!empty($err_msg['place'])) echo $err_msg['place'];
                  ?>
              </div>
              <div>コーチングの内容
              <span class="Required">必須</span>
              <textarea name="content" id="" cols="54" rows="20"><?php echo formData('content')?></textarea>
                  <div class="err_msg">
                      <?php
                      if(!empty($err_msg['content'])) echo $err_msg['content'];
                      ?>
                  </div>
              </div>
              <div>
                  コーチングに際してのお願い
                  <textarea name="ask" id="" cols="54" rows="20">
                      <?php echo formData('ask')?>
                  </textarea>
                  <div class="err_msg">
                      <?php
                      if(!empty($err_msg['ask'])) echo $err_msg['ask'];
                      ?>
                  </div>
              </div>
             
              <div class="img-container">
                  画像
                  <div class="area-pic">
                      <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                      <input type="file" name="pic" class="input-pic">
                      <img src="<?php echo formData('pic'); ?>" alt="" class="prev-pic" style="<?php if(empty(formData('pic'))) echo 'display:none;' ?>">                   

                  </div>
                  <div class="err_msg">
                      <?php
                  if(!empty($err_msg['pic'])) echo $err_msg['pic'];
                      ?>
                  </div>
              </div>
              <div style="text-align:left;">
                  金額<span class="Required">必須</span>
                  <div class="form-price">
                      <input type="text" name="price" style="width:150px" value="<?php echo (!empty(formData('price'))) ? formData('price') : 0; ?>"><span class="option">円</span>
                  </div>
              </div>
              <div class="btn-container">
                  <input type="submit" class="btn-product" value="<?php echo (!$edit_flg) ? '募集する' : '更新する'; ?>">
              </div>
          </form>
      </div>
       
   </section>
   
  <!--サイドバー--!>
  <?php
require('sidebar.php');
?>
