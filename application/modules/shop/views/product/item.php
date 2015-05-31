<?php

/**
 * @var $product \app\modules\shop\models\Product
 * @var $this \yii\web\View
 * @var $url string
 */

use app\modules\image\widgets\ObjectImageWidget;
use kartik\helpers\Html;

?>
<div class="col-md-4">
    <div class="thumbnail">
        <a href="<?=$url?>">
            <?=
            ObjectImageWidget::widget(
                [
                    'limit' => 1,
                    'model' => $product,
                ]
            )
            ?>
        </a>

        <div class="caption">
            <h5><a href="<?=$url?>"><?=Html::encode($product->name)?></a></h5>

            <p>
                <?=$product->announce?>
            </p>
            <h4 style="text-align:center">
                <a class="btn" href="#" data-action="add-to-cart" data-id="<?=$product->id?>"><?=Yii::t(
                        'app',
                        'Add to'
                    )?> <i class="fa fa-shopping-cart"></i></a>
                <button class="btn btn-primary">
                    <?=$product->formattedPrice(null, false, false)?>
                </button>
            </h4>
        </div>
    </div>
</div>