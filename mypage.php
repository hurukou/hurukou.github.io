<?php

require('function.php');
require('auth.php');
require('head.php');

$u_id = $_SESSION['user_id'];

// DBから商品データを取得
$productData = getMyProductsData($u_id);

// DBから取引チャットデータを取得
$boardData = getMesgBoard($u_id);


?>


<body class="page-1colum">

    
       <body class="page-mypage page-3colum page-logined">
        <style>
            #main{
                border: none !important;
            }
        </style>
    
     <!-- メニュー -->
     <?php
    require('header.php');
     ?>

    <!--メインコンテンツ-->
    <div　id="contents" class="site-width">
       
       <h1 class="page-title">MYPAGE</h1>
        <!-- メイン -->
        <section id="main">
        <section class="list page-list">
         <h2 class="title" style="margin-bottom:15px;">
             登録コーチ一覧
         </h2> 
         <?php
          if(!empty($productData)): foreach($productData as $key => $val):   
         ?>
         <a href="newProduct.php<?php echo (!empty(getPara())) ? getPara().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="page">
            <div class="page-head">
                <img src="<?php echo  showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['name']); ?>">
            </div>
            <div class="page-body">
                <p class="page-title"><?php echo sanitize($val['name']); ?> <span class="price">>¥<?php echo sanitize(number_format($val['price'])); ?></span>
                </p>
            </div>
             
             
         </a>
         <?php
           endforeach;
           endif;
         ?>
        </section>
        
            <style>
                .list{
                    margin-bottom: 30px;
                }
            </style>

        <section class="list list-table">
           <h2 class="title">
               取引チャット
           </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>最新送信日時</th>
                    <th>取引相手</th>
                    <th>メッセージ</th>
                </tr>
            </thead>
            <tbody>
             <?php
                if(!empty($boardData)){
                    foreach($boardData as $key => $val){
                        if(!empty($val['message'])){
                            $msg = array_shift($val['message']);
             ?>
                
                <tr>
                    <td><?php echo sanitize(date('Y.m.d H:i:s',strtotime($msg['send_date']))); ?></td>
                    <td>〇〇　〇〇</td>
                    <td><a href="msgboard.php?m_id=<?php echo sanitize($val['id']); ?>"><?php mb_substr(sanitize($msg['	message']),0,40); ?>...</a></td>
                </tr>
                <?php 
                        }else{
                ?>
                    <tr>
                        <td>--</td>
                        <td>〇〇　〇〇</td>
                        <td><a href="msgboard.php?m_id=<?php echo sanitize($val['id']); ?>">メッセージはまだありません</a></td>
                    </tr>
                    <?php
                        }
                    }
                }
                ?>
            </tbody>
        </table>
            
        </section>
        
        <section></section>
        
        
        </section>
        <!-- サイドバー -->
        <?php
        require('sidebar.php');
        ?>
    </div>
</body>