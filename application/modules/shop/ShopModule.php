<?php

namespace app\modules\shop;

use app;
use app\components\BaseModule;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\UserEvent;

/**
 * Shop module is the base core module of DotPlant2 CMS handling all common e-commerce features
 * @package app\modules\shop
 */
class ShopModule extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public $controllerMap = [
        'backend-filter-sets' => 'app\modules\shop\backend\FilterSetsController',
    ];

    /**
     * @var int How much products per page to show
     */
    public $productsPerPage = 15;

    /**
     * @var int How much products allow to compare at once
     */
    public $maxProductsToCompare = 3;

    /**
     * @var bool Should we show and query for products of subcategories
     */
    public $showProductsOfChildCategories = 1;

    /**
     * @var int How much products to show on search results page
     */
    public $searchResultsLimit = 9;

    /**
     * @var bool Show delete order in backend
     */
    public $deleteOrdersAbility = 0;

    /**
     * @var bool Filtration works only on parent products but not their children
     */
    public $filterOnlyByParentProduct = 1;

    /**
     * @var int How much last viewed products ID's to store in session
     */
    public $maxLastViewedProducts = 9;

    /**
     * @var bool Allow to add same product in the order
     */
    public $allowToAddSameProduct = 0;

    /**
     * @var bool Count only unique products in the order
     */
    public $countUniqueProductsOnly = 1;

    /**
     * @var bool Count children products in the order
     */
    public $countChildrenProducts = 1;

    /**
     * @var int Default measure ID
     */
    public $defaultMeasureId = 1;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'configurableModule' => [
                'class' => 'app\modules\config\behaviors\ConfigurableModuleBehavior',
                'configurationView' => '@app/modules/shop/views/configurable/_config',
                'configurableModel' => 'app\modules\shop\models\ConfigConfigurationModel',
            ]
        ];
    }

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        Event::on(
            \yii\web\User::className(),
            \yii\web\User::EVENT_AFTER_LOGIN,
            function ($event) {
                /** @var UserEvent $event */
                $orders = \Yii::$app->session->get('orders', []);
                foreach ($orders as $k => $id) {
                    /** @var app\modules\shop\models\Order $order */
                    $order = app\modules\shop\models\Order::findOne(['id' => $id]);
                    if (!empty($order) && 0 === intval($order->user_id)) {
                        $order->user_id = $event->identity->id;
                        $order->save();
                    }
                }
            }
        );
    }
}