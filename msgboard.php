<?php

require('function.php');

//画面処理//

$boardUserId = '';
$boardUserInfo = '';
$myUserInfo = '';
$productInfo = '';

//GETパラメータ
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
// DBから掲示板とメッセージデータを取得
$getData = getMesgBoard($m_id);

//パラメータに不正な値が入ってるか
//if(empty($getData)){
   // error_log('エラー:指定ページに不正な値を検出');
   // header("Location:mypage.php");
//}
//商品情報を取得
$productInfo = getProductInfo($getData[0]['product_id']);

//商品情報が入っているか
//if(empty($productInfo)){
   // error_log('エラー:商品ページが取得できない');
    //header("Location:mypage.php");
//}
// getDataから相手のユーザーidを取得

$bothUserId[] = $getData[0]['sale_user'];
$bothUserId[] = $getData[0]['buy_user'];
if(($key = array_search($_SESSION['user_id'], $bothUserId)) !== false) {
    unset($bothUserId[$key]);
}
$boardUserId = array_shift($bothUserId);

//DBから相手のユーザー情報を取得
if(isset($boardUserId)){
    $boardUserInfo = getUserData($boardUserId);
}
//相手のユーザー情報が取得できたかチェック
//if(empty($boardUserInfo)){
  //  error_log('エラー:相手のユーザー情報が取得できませんでした');
   // header("Location:mypage.php");
//}
//　DBからユーザー情報を取得
$myUserInfo = getUserData($_SESSION['user_id']);
//　自分のユーザー情報が取得できたかチェック
//if(empty($myUserInfo)){
  //  error_log('エラー:自分のユーザー情報が取得できませんでした');
      //  header("Location:mypage.php");
  //  }
    if(!empty($_POST)){
        
        //認証
        require('auth.php');
    
    
    //バリデーション
    $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
    //最大文字数チェック
    validMax($msg, 'msg', 500);
    //未入力チェック
    validInput($msg, 'msg');
    
    if(empty($err_msg)){
        //例外処理
        try {
            //DBへ接続
            $db = db_connection();
            //SQL文
            $sql = 'INSERT INTO message (board_id, send_date, to_user, from_user, msg, create_date) VALUES (:b_id, :send_date, :to_user, :from_user, :msg, :date)';
            $data = array(':b_id' => $m_id, ':send_date' => date('Y-m-d H:i:s'), ':to_user' => $boardUserId, ':from_user' => $_SESSION['user_id'], ':msg' => $msg, ':date' => date('Y-m-d H:i:s'));
            //クエリ実行
            $stmt = queryex($db, $sql, $data);
            
           //クエリ成功
            if($stmt){
                $_POST = array(); 
                header("Location: " . $_SERVER['PHP_SELF'] .'?m_id='.$m_id); //自分自身に遷移する

            }
        } catch(Exception $e){
            error_log('エラー:' . $e->getMessage());
            $err_msg['err_code'] = MSG07;
        }  
    }
    }
?>
<?php    
require('head.php');
?>


<body class="page-msg page-1colum">
    <style>
        .msg-info{
            background: #f6f5f4;
            padding: 15px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .msg-info .avatar{
            width: 80px;
            height: 80px;
            border-radius: 40px;
        }
        .msg-info .avatar-img{
            text-align: center;
            width: 100px;
            float: left;
        }
        .msg-info .avatar-info{
            float: left;
            padding-left: 15px;
            width: 500px;
        }
        .msg-info .product-info{
            float: left;
            padding-left: 15px;
            width: 315px;
        }
        .msg-info .product-info .left,
        .msg-info .product-info .right{
            float: left;
        }
        .msg-info .product-info .right{
            padding-left: 15px;
        }
        .msg-info .product-info .price{
            display: inline-block;
        }
        .area-bord{
            height: 500px;
            overflow-y: scroll;
            background: #f6f5f4;
            padding: 15px;
        }
        .area-send-msg{
            background: #f6f5f4;
            padding: 15px;
            overflow: hidden;
        }
        .area-send-msg textarea{
            width:100%;
            background: white;
            height: 100px;
            padding: 15px;
        }
        .area-send-msg .btn-send{
            width: 150px;
            float: right;
            margin-top: 0;
        }
        .area-bord .msg-cnt{
            width: 80%;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .area-bord .msg-cnt .avatar{
            width: 5.2%;
            overflow: hidden;
            float: left;
        }
        .area-bord .msg-cnt .avatar img{
            width: 40px;
            height: 40px;
            border-radius: 20px;
            float: left;
        }
        .area-bord .msg-cnt .msg-inrTxt{
            width: 85%;
            float: left;
            border-radius: 5px;
            padding: 10px;
            margin: 0 0 0 25px;
            position: relative;
        }
        .area-bord .msg-cnt.msg-left .msg-inrTxt{
            background: #f6e2df;
        }
        .area-bord .msg-cnt.msg-left .msg-inrTxt > .triangle{
            position: absolute;
            left: -20px;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-right: 15px solid #f6e2df;
            border-left: 10px solid transparent;
            border-bottom: 10px solid transparent;
        }
        .area-bord .msg-cnt.msg-right{
            float: right;
        }
        .area-bord .msg-cnt.msg-right .msg-inrTxt{
            background: #d2eaf0;
            margin: 0 25px 0 0;
        }
        .area-bord .msg-cnt.msg-right .msg-inrTxt > .triangle{
            position: absolute;
            right: -20px;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-left: 15px solid #d2eaf0;
            border-right: 10px solid transparent;
            border-bottom: 10px solid transparent;
        }
        .area-bord .msg-cnt.msg-right .msg-inrTxt{
            float: right;
        }
        .area-bord .msg-cnt.msg-right .avatar{
            float: right;
        }
    </style>
    <!-- メニュー-->
    <?php 
      require('header.php');
    ?>
    
    <!-- メイン -->
    <div id="contents" class="site-width">
     <section id="main">
        <div class="msg-info">
            <div class="avatar-img">
                <img src="<?php echo showImg(sanitize($boardUserInfo['pic'])); ?>" alt="" class="avatar"><br>
            </div>
            <div class="avatar-info">
                <?php echo sanitize($boardUserInfo['username']).''?><br>
         
            </div>
            <div class="product-info">
                <div class="left">
                    コーチ<br>
                    <img src="<?php echo showImg(sanitize($productInfo['pic'])); ?>" alt="" height="70px" width="auto">
                </div>
                <div class="right">
                    <?php echo sanitize($productInfo['title']); ?><br>
                    取引金額：<span class="price">¥<?php echo number_format(sanitize($productInfo['price'])); ?></span><br>
                    取引開始日：<?php echo date('Y/m/d', strtotime(sanitize($viewData[0]['create_date']))); ?>
                </div>
            </div>
     
        </div>
        <div class="area-board">
         <?php
         if(!empty($getData)){
             foreach($getData as $key => $val){
                 if(!empty($val['from_user']) && $val['from_user'] == $boardUserId){
         ?>
         <div class="msg-cnt msg-left">
             <div class="avatar">
                 <img src="<?php echo sanitize(showImg($boardUserInfo['pic'])); ?>" alt="" class="avatar">
             </div>
             <p class="msg-inrTxt">
                 <span class="triangle"></span>
                 <?php echo sanitize($val['msg']); ?>
             </p>
             <div style="font-size:.5em;"><?php echo sanitize($val['send_date']); ?></div>
         </div>
         <?php
                 }else{
         ?>
         <div class="msg-cnt msg-right">
             <div class="avatar">
                 <img src="<?php echo sanitize(showImg($myUserInfo['pic'])); ?>" alt="" class="avatar">
             </div>
             <p class="msg-inrTxt">
                 <span class="triangle"></span>
                 <?php echo sanitize($val['msg']); ?>
             </p>
             <div style="font-size:.5em;text-align:right;"><?php echo sanitize($val['send_date']); ?></div>
         </div>
         <?php
                 }
             }
         }else{
         ?>
         <p style="text-align:center;line-height:20;">メッセージ投稿はまだありません</p>
         <?php
         }
         ?>
         </div>

         <div class="area-send-msg">
             <form action="" method="post">
                 <textarea name="msg" cols="30" rows="3"></textarea>
                 <input type="submit" value="送信" class="btn btn-send">
             </form>
         </div>
         
     </section>
    </div>
