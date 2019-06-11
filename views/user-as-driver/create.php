<?php

/* @var $this yii\web\View */
/* @var $model core\models\UserAsDriver */
/* @var $modelUser core\models\User */
/* @var $modelPerson core\models\Person */

$this->title = 'Create ' . \Yii::t('app', 'Driver');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="user-as-driver-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
        'modelPerson' => $modelPerson
    ]) ?>

</div>