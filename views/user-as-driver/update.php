<?php

/* @var $this yii\web\View */
/* @var $model core\models\UserAsDriver */
/* @var $modelUser core\models\User */
/* @var $modelPerson core\models\Person */

$this->title = 'Update ' . \Yii::t('app', 'Driver') . ' : ' . $model->user->username;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user->username, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update'; ?>

<div class="user-as-driver-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
        'modelPerson' => $modelPerson
    ]) ?>

</div>
