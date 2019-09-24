<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\ApplicationDriver;
use core\models\LogStatusApprovalDriver;
use core\models\RegistryDriver;
use yii\filters\VerbFilter;

/**
 * StatusApprovalDriverController implements the CRUD actions for RegistryDriver model.
 */
class StatusApprovalDriverController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [

                    ],
                ],
            ]);
    }

    public function actionResubmit($appDriverId, $regDriverId)
    {
        $flag = false;

        if (($flag = LogStatusApprovalDriver::updateAll(['is_actual' => 0], ['application_driver_id' => $appDriverId]) > 0)) {

            $modelApplicationDriver = ApplicationDriver::findOne($appDriverId);
            $modelApplicationDriver->counter = $modelApplicationDriver->counter + 1;

            if (($flag = $modelApplicationDriver->save())) {

                $modelLogStatusApprovalDriver = new LogStatusApprovalDriver();
                $modelLogStatusApprovalDriver->application_driver_id = $appDriverId;
                $modelLogStatusApprovalDriver->status_approval_driver_id = 'PNDG';
                $modelLogStatusApprovalDriver->application_driver_counter = $modelApplicationDriver->counter;
                $modelLogStatusApprovalDriver->is_actual = true;

                if (($flag = $modelLogStatusApprovalDriver->save())) {

                    $modelRegistryDriver =  RegistryDriver::findOne(['id' => $regDriverId]);
                    $modelRegistryDriver->application_driver_counter = $modelApplicationDriver->counter;

                    $flag = $modelRegistryDriver->save();
                }
            }
        }

        return $flag;
    }
}
