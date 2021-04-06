<?php
require('function.php');
//商品idのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品を取得
$getData = getProductInfo($p_id);

//パラメータに不正な値が入っているかどうか
//if(empty($getData)){
//    error_log('エラー:指定ページに不正な値を検出');
//  header("Location:index.php");
//}


if (!empty($_POST['submit'])) {

    require('auth.php');

    try {

        $db = db_connection();

        $sql = 'INSERT INTO board (sale_user, buy_user, product_id, create_date) VALUES (:s_uid, :b_uid, :p_id, :date)';
        $data = array(':s_uid' => $getData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));

        $stmt = queryex($db, $sql, $data);

        if ($stmt) {
            $_SESSION['msg_suc'] = SUC05;
            header("Location:msg.php?m_id=" . $dbh->lastInsertID());
        }
    } catch (Exception $e) {
        error_log('エラー:' . $e->getMessage());
        $err_msg['err_code'] = MSG07;
    }
}
?>
<?php
require('head.php');
?>

<body class="page-productDetail page-1colum">
    <style>
        #main .title {
            font-size: 28px;
            padding: 10px 0;
        }

        .product-img-container {
            overflow: hidden;
        }

        .product-img-container img {
            width: 100%;
        }

        .product-img-container .img-main {
            width: 750px;
            float: left;
            padding-right: 15px;
            box-sizing: border-box;
        }

        .product-img-container .img-sub {
            width: 230px;
            float: left;
            background: #f6f5f4;
            padding: 15px;
            box-sizing: border-box;
        }

        .product-img-container .img-sub:hover {
            cursor: pointer;
        }

        .product-img-container .img-sub img {
            margin-bottom: 15px;
        }

        .product-img-container .img-sub img:last-child {
            margin-bottom: 0;
        }

        .product-detail {
            background: #f6f5f4;
            padding: 15px;
            margin-top: 15px;
            min-height: 150px;
        }

        .product-buy {
            overflow: hidden;
            margin-top: 15px;
            margin-bottom: 50px;
            height: 50px;
            line-height: 50px;
        }

        .product-buy .product-left {
            float: left;
        }

        .product-buy .product-right {
            float: right;
        }

        .product-buy .price {
            font-size: 32px;
            margin-right: 30px;
        }

        .product-buy .btn {
            border: none;
            font-size: 18px;
            padding: 10px 30px;
        }

        .product-buy .btn:hover {
            cursor: pointer;
        }
    </style>

    <?php
    require('header.php');
    ?>

    <div id="contents" class="site-width">

        <section id="main">

            <div class="product-img-container">
                <div class="img-main">
                    <img src="<?php echo showImg(sanitize($getData['pic'])); ?>" alt="メイン画像：<?php echo sanitize($getData['title']); ?>">
                </div>
            </div>
            <div class="product-detail">
                <p><?php echo sanitize($getData['content']); ?></p>
                <p><?php echo sanitize($getData['ask']); ?></p>
                <?= var_dump($getData); ?>

            </div>
            <div class="product-buy">
                <div class="product-left">
                    <a href="index.php<?php echo getPara(array('p_id')); ?>">&lt; 商品一覧に戻る</a>
                </div>
                <form action="" method="post">
                    <div class="product-right">
                        <input type="submit" value="購入する!" name="submit" class="btn btn-buy" style="margin-top:0;">
                    </div>
                </form>
                <div class="product-right">
                    <p class="price">¥<?php echo sanitize(number_format($getData['price'])); ?>-</p>
                </div>
            </div>

        </section>
</body>