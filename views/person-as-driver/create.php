<?php

/* @var $this yii\web\View */
/* @var $model core\models\PersonAsDriver */
/* @var $modelPerson core\models\Person */
/* @var $modelDriverAttachment core\models\DriverAttachment */
/* @var $motorBrand array */
/* @var $motorType array */
/* @var $attachmentType array */

$this->title = 'Create ' . \Yii::t('app', 'Person As Driver');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="person-as-driver-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelPerson' => $modelPerson,
        'modelDriverAttachment' => $modelDriverAttachment,
        'motorBrand' => $motorBrand,
        'motorType' => $motorType,
        'attachmentType' => $attachmentType,
    ]) ?>

</div>