<?php
/**
 * @var $model \app\models\Form
 * @var $reviews \app\modules\review\models\Review[]
 * @var $review \app\modules\review\models\Review
 * @var $useCaptcha boolean
 * @var $groups \app\models\PropertyGroup[]
 * @var $view \yii\web\View
 * @var $objectModel \yii\db\ActiveRecord
 * @var $ratingGroupName string
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Property;

$allowRate = !empty($ratingGroupName);

?>
<div class="row">
    <?php if ($allowRate): ?>
    <?= \app\modules\review\widgets\rating\RatingShowWidget::widget(['objectModel' => $objectModel]) ?>
    <?php endif; ?>
    <div class="col-md-6">
        <div class = "widget-reviews">
            <?php \yii\widgets\Pjax::begin() ?>
            <?=
            \yii\widgets\ListView::widget([
                'dataProvider' => $reviews,
                'itemView' => 'item',
                'viewParams' => ['allowRate' => $allowRate, 'groups' => $groups],
            ])
            ?>
            <?php \yii\widgets\Pjax::end() ?>
            <?php
            $form = ActiveForm::begin(
                [
                    'action'=>[
                        '/review/process/process',
                        'objectId' => $objectModel->object->id,
                        'objectModelId' => $objectModel->id,
                        'id' => $model->id,
                        'returnUrl' => Yii::$app->request->url,
                    ],
                    'id' => 'review-form'
                ]
            );
            ?>
            <h2>
                <?= Yii::t('app', 'Your review') ?>
                <?php if (Yii::$app->getUser()->isGuest): ?>
                    <small>[<?=
                        Html::a(
                            Yii::t('app', 'Login'),
                            ['/default/login', 'returnUrl' => Yii::$app->request->absoluteUrl]
                        )
                        ?>]</small>
                <?php else: ?>
                    <small>[<?= Yii::$app->getUser()->getIdentity()->getDisplayName() ?>]</small>
                <?php endif; ?>
            </h2>
            <?php if ($allowRate): ?>
                <?=
                \app\modules\review\widgets\rating\RatingWidget::widget(
                    [
                        'groupName' => $ratingGroupName,
                    ]
                )
                ?>
            <?php endif; ?>
            <?php if (Yii::$app->getUser()->isGuest): ?>
                <?= $form->field($review, 'author_email') ?>
            <?php endif; ?>
            <?= $form->field($review, 'review_text')->textarea() ?>
            <?php foreach ($groups as $group): ?>
                <?php $properties = Property::getForGroupId($group->id); ?>
                <?php foreach ($properties as $property): ?>
                    <?= $property->handler($form, $model->abstractModel, [], 'frontend_edit_view'); ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <?php
            if ($useCaptcha) {
                echo $form->field($review, 'captcha')->widget(yii\captcha\Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-4">{image}</div><div class="col-lg-8">{input}</div></div>',
                    'captchaAction' => '/default/captcha',
                ]);
            }
            ?>
            <div class = "form-group no-margin">
                <?=
                Html::submitButton(
                    Yii::t('app', 'You review'),
                    ['class' => 'btn btn-success']
                )
                ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>