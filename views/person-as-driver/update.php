<?php

/* @var $this yii\web\View */
/* @var $model core\models\PersonAsDriver */
/* @var $modelPerson core\models\Person */
/* @var $motorBrand array */
/* @var $motorType array */


$this->title = 'Update ' . \Yii::t('app', 'Person As Driver') . ' : ' . $modelPerson->first_name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $modelPerson->first_name, 'url' => ['view', 'id' => $model->person_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="person-as-driver-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelPerson' => $modelPerson,
        'motorBrand' => $motorBrand,
        'motorType' => $motorType,
    ]) ?>

</div>
