<?php

namespace backoffice\modules\driver\controllers;

use core\models\ApplicationDriver;
use core\models\DriverAttachment;
use core\models\LogStatusApprovalDriver;
use core\models\Person;
use core\models\PersonAsDriver;
use core\models\RegistryDriver;
use yii\filters\VerbFilter;

/**
 * StatusApprovalDriverController implements the CRUD actions for RegistryDriver model.
 */
class StatusApprovalDriverController extends \backoffice\controllers\BaseController
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
            $modelApplicationDriver->counter += $modelApplicationDriver->counter;

            if (($flag = $modelApplicationDriver->save())) {

                $modelLogStatusApprovalDriver = new LogStatusApprovalDriver();
                $modelLogStatusApprovalDriver->application_driver_id = $appDriverId;
                $modelLogStatusApprovalDriver->status_approval_driver_id = 'PNDG';
                $modelLogStatusApprovalDriver->application_driver_counter = $modelApplicationDriver->counter;
                $modelLogStatusApprovalDriver->is_actual = true;

                if (($flag = $modelLogStatusApprovalDriver->save())) {

                    $modelRegistryDriver = RegistryDriver::findOne(['id' => $regDriverId]);
                    $modelRegistryDriver->application_driver_counter = $modelApplicationDriver->counter;

                    $flag = $modelRegistryDriver->save();
                }
            }
        }

        return $flag;
    }

    public function actionApprove($regDriverId)
    {
        $flag = false;

        $modelRegistryDriver = RegistryDriver::find()
            ->joinWith(['registryDriverAttachments'])
            ->andWhere(['registry_driver.id' => $regDriverId])
            ->asArray()->one();

        $modelPerson = new Person();
        $modelPerson->first_name = $modelRegistryDriver['first_name'];
        $modelPerson->last_name = $modelRegistryDriver['last_name'];
        $modelPerson->email = $modelRegistryDriver['email'];
        $modelPerson->phone = $modelRegistryDriver['phone'];

        if (($flag = $modelPerson->save())) {

            $modelPersonAsDriver = new PersonAsDriver();
            $modelPersonAsDriver->person_id = $modelPerson->id;
            $modelPersonAsDriver->district_id = $modelRegistryDriver['district_id'];
            $modelPersonAsDriver->no_ktp = $modelRegistryDriver['no_ktp'];
            $modelPersonAsDriver->no_sim = $modelRegistryDriver['no_sim'];
            $modelPersonAsDriver->date_birth = $modelRegistryDriver['date_birth'];
            $modelPersonAsDriver->motor_brand = $modelRegistryDriver['motor_brand'];
            $modelPersonAsDriver->motor_type = $modelRegistryDriver['motor_type'];
            $modelPersonAsDriver->emergency_contact_name = $modelRegistryDriver['emergency_contact_name'];
            $modelPersonAsDriver->emergency_contact_phone = $modelRegistryDriver['emergency_contact_phone'];
            $modelPersonAsDriver->emergency_contact_address = $modelRegistryDriver['emergency_contact_address'];
            $modelPersonAsDriver->number_plate = $modelRegistryDriver['number_plate'];
            $modelPersonAsDriver->stnk_expired = $modelRegistryDriver['stnk_expired'];
            $modelPersonAsDriver->other_driver = $modelRegistryDriver['other_driver'];
            $modelPersonAsDriver->is_criteria_passed = $modelRegistryDriver['is_criteria_passed'];
        }

        if (($flag = $modelPersonAsDriver->save())) {

            foreach ($modelRegistryDriver['registryDriverAttachments'] as $dataRegistryDriverAttachment) {

                $modelDriverAttachment = new DriverAttachment();
                $modelDriverAttachment->person_as_driver_id = $modelPersonAsDriver->person_id;
                $modelDriverAttachment->file_name = $dataRegistryDriverAttachment['file_name'];
                $modelDriverAttachment->type = $dataRegistryDriverAttachment['type'];

                if (!($flag = $modelDriverAttachment->save())) {

                    break;
                }
            }
        }

        return $flag;
    }
}
