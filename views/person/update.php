<?php

/* @var $this yii\web\View */
/* @var $model core\models\Person */

$this->title = 'Update ' . \Yii::t('app', 'Person') . ' : ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="person-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
