<?php

namespace modules\catalog\backend;

use Yii;

/**
 * catalog module definition class
 */
class Module extends \yii\base\Module
{

    public $controllerNamespace = 'modules\catalog\backend\controllers';


    public function init()
    {
        parent::init();
        Yii::$app->view->params['breadcrumbs'][] = [
            'url' => ['/site/catalog/default/index'],
            'label' => 'Каталог',
        ];
    }
}
