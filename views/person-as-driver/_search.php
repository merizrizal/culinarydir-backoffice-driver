<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model core\models\search\PersonAsDriverSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="person-as-driver-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'person_id') ?>

    <?= $form->field($model, 'district_id') ?>

    <?= $form->field($model, 'no_ktp') ?>

    <?= $form->field($model, 'no_sim') ?>

    <?= $form->field($model, 'date_birth') ?>

    <?php // echo $form->field($model, 'motor_brand') ?>

    <?php // echo $form->field($model, 'motor_type') ?>

    <?php // echo $form->field($model, 'emergency_contact_name') ?>

    <?php // echo $form->field($model, 'emergency_contact_phone') ?>

    <?php // echo $form->field($model, 'emergency_contact_address') ?>

    <?php // echo $form->field($model, 'number_plate') ?>

    <?php // echo $form->field($model, 'stnk_expired') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'user_created') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'user_updated') ?>

    <?php // echo $form->field($model, 'other_driver') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
