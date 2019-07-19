<?php

/* @var $this yii\web\View */
/* @var $model core\models\PersonAsDriver */

$this->title = 'Update ' . \Yii::t('app', 'Person As Driver') . ' : ' . $model->person_id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->person_id, 'url' => ['view', 'id' => $model->person_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="person-as-driver-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
