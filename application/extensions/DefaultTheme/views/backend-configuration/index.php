<?php

/** @var \yii\data\ActiveDataProvider $variationsDataProvider */
/** @var \app\extensions\DefaultTheme\models\ThemeVariation $variationsSearchModel */
/** @var \yii\data\ActiveDataProvider $partsDataProvider */
/** @var \app\extensions\DefaultTheme\models\ThemeParts  $partsSearchModel */

use yii\helpers\Html;
use app\backend\components\ActiveForm;
use kartik\icons\Icon;

$this->title = Yii::t('app', 'Default theme configuration');
$this->params['breadcrumbs'][] = [
    'url' => [
        '/DefaultTheme/backend-configuration/index',
    ],
    'label' => $this->title
];
?>

<?= \yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('app', 'Theme parts'),
            'content' => $this->render(
                '_parts',
                [
                    'partsSearchModel' => $partsSearchModel,
                    'partsDataProvider'=>$partsDataProvider
                ]
            ),
            'active' => true,
        ],
        [
            'label' => Yii::t('app', 'Theme variations'),
            'content' => $this->render(
                '_variations',
                [
                    'variationsSearchModel' => $variationsSearchModel,
                    'variationsDataProvider'=>$variationsDataProvider
                ]
            ),
        ],
    ],
]) ?>