<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\user\models\User */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('-form', ['model' => $model]) ?>