<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\catalog\models\ProductCategory */

$this->title = 'Каталог';
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<?= $this->render('-form', ['model' => $model]) ?>
