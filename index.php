<?php
//共通変数・関数ファイルを読込み
require('function.php');
//ページネーション
$paginationNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;

//商品
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// ソート
$sort = (!empty($_GET['sort'])) ? ($_GET['sort']) : '';

//パラメータに不正な値があるかチェック
if (!is_int($paginationNum)) {
    error_log('エラー:不正な値が検出されました');
    header("Location:index.php");
}
//表示リスト
$listCount = 20;
//現在の表示リストの先頭を検出
$paginationMinNum = (($paginationNum - 1) * $listCount);
//DBから商品データを取得
$dbProduct = getProductlist($paginationMinNum, $category, $sort);
//DBからカテゴリーデータを取得
$dbCategory = getCategory();


require('head.php');

?>

<body class="page-2colum">

    <!--　ヘッダー -->
    <?php
    require('header.php');
    ?>

    <div class="figure">
        <img src="img/poker4.jpg" width="2000" height="0" id="top-baner">
        <div class="text1">Poker Coach</div>
        <div class="text2">やってみたけど、よくわからない・・・基礎からみっちり教えてもらいたい・・・</div>
        <div class="text3">ポーカーで稼げるようになりたい・・・という方必見。</div>
        <div class="text4">上達したいプレイヤーとコーチを繋ぐサイト</div>
        <a href="" class="btn-beginner">初めての方へ</a>
        <a href="signup.php" class="btn-user">無料会員登録</a>
    </div>
    <!--メインコンテンツ-->
    <section id="main">
        <p>コーチ一覧</p>
        <div class="page-title">
            <div class="page-left">
                <span class="total-num">
                    <?php echo sanitize($dbProduct['total']); ?>
                </span>件のコーチが見つかりました
            </div>
            <div class="page-right">
                <span class="num"><?php echo (!empty($dbProduct['data'])) ? $paginationMinNum + 1 : 0; ?></span> - <span class="num"><?php echo $paginationMinNum + count($dbProduct['data']); ?></span>件 /
                <span class="num"><?php echo sanitize($dbProduct['total']); ?></span>件中
            </div>
        </div>
        <div class="page-list">
            <?php
            foreach ($dbProduct['data'] as $key => $val) :
            ?>
                <a href="product.php<?php echo (!empty(getPara())) ? getPara() . '&p_id=' . $val['id'] : '?p_id=' . $val['id']; ?>" class="page">
                    <div class="page-head">
                        <img src="<?php echo sanitize($val['pic']); ?>" alt="<?php echo sanitize($val['title']); ?>">
                    </div>
                    <div class="page-body">
                        <p class="page-title"><?php echo sanitize($val['title']); ?> <span class="price">¥<?php echo sanitize(number_format($val['price'])); ?></span>
                        </p>
                    </div>
                </a>
            <?php
            endforeach;
            ?>

        </div>

        <?php currentPagination($paginationNum, $dbProduct['total_page']); ?>
    </section>

</body>