<?php

/* @var $this yii\web\View */
/* @var $model core\models\Person */
/* @var $modelPersonAsDriver core\models\PersonAsDriver */

$this->title = 'Create ' . \Yii::t('app', 'Person');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelPersonAsDriver' => $modelPersonAsDriver,
    ]) ?>

</div>