<?php

/* @var $this yii\web\View */
/* @var $model core\models\DriverCriteria */

$this->title = 'Create ' . \Yii::t('app', 'Driver Criteria');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Driver Criteria'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-criteria-create">

    <?= $this->render('_form', [
        'model' => $model,
        'driverCriteria' => $driverCriteria,
        'motorCriteria' => $motorCriteria,
    ]) ?>

</div>