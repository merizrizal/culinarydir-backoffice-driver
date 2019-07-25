<?php

/* @var $this yii\web\View */
/* @var $model core\models\DriverCriteria */

$this->title = 'Update ' . \Yii::t('app', 'Driver Criteria') . ' : ' . $model['person_as_driver_id'];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Driver Criteria'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model['person_as_driver_id'], 'url' => ['view', 'id' => $model['model_as_person_id']]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="driver-criteria-update">

    <?= $this->render('_form', [
        'model' => $model,
        'driverCriteria' => $driverCriteria,
        'motorCriteria' => $motorCriteria,
    ]) ?>

</div>
