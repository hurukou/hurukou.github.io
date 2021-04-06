<?php
require('function.php');
$paginationMinNum = (($paginationNum - 1) * $listCount);
//DBから商品データを取得
$dbProduct = getProductlist($paginationMinNum, $category, $sort);
//DBからカテゴリーデータを取得
$dbCategory = getCategory();

?>

<div class="page-list">
    <?php
    foreach ($dbProduct['data'] as $key => $val) :
    ?>
        <a href="product.php><?php echo (!empty(getPara())) ?
                                    getPara() . '&p_id=' . $val['id'] : '?p_id=' . $val['id']; ?>" class="page">
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