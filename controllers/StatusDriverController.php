<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\ApplicationDriver;
use core\models\LogStatusApprovalDriver;
use core\models\RegistryDriver;
use core\models\StatusApprovalDriver;
use core\models\StatusApprovalDriverRequire;
use core\models\search\RegistryDriverSearch;
use sycomponent\AjaxRequest;
use yii\filters\VerbFilter;


/**
 * StatusDriverController implements the CRUD actions for RegistryDriver model.
 */
class StatusDriverController extends BaseController
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
                        'update-status-driver' => ['POST'],
                    ],
                ],
            ]);
    }

    public function actionPndgDriver()
    {
        return $this->indexDriver('PNDG', \Yii::t('app', 'Pending'));
    }

    public function actionIcorctDriver()
    {
        return $this->indexDriver('ICORCT', \Yii::t('app', 'Incorrect'));
    }

    public function actionViewDriver($id, $appDriverId)
    {
        $model = RegistryDriver::find()
            ->joinWith([
                'applicationDriver',
                'userInCharge',
                'applicationDriver.logStatusApprovalDrivers',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver.statusApprovalDriverRequires0',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver.statusApprovalDriverRequires0.statusApprovalDriver status_approval_driver_req',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver.statusApprovalDriverRequires0.statusApprovalDriver.logStatusApprovalDrivers log_status_approval_driver_req' => function ($query) use ($appDriverId) {

                    $query->andOnCondition(['log_status_approval_driver_req.application_driver_id' => $appDriverId]);
                },
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver.statusApprovalDriverActions',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver.statusApprovalDriverActions.logStatusApprovalDriverActions log_status_approval_driver_action_act',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver.statusApprovalDriverActions.logStatusApprovalDriverActions.logStatusApprovalDriver log_status_approval_driver_act' => function ($query) use ($appDriverId) {

                    $query->andOnCondition(['log_status_approval_driver_act.application_driver_id' => $appDriverId]);
                },
            ])
            ->andWhere(['registry_driver.id' => $id])
            ->asArray()->one();

        return $this->render('view_driver', [
            'model' => $model,
        ]);
    }

    public function actionUpdateStatusDriver($id, $rdid)
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $modelApplicationDriver = ApplicationDriver::find()
                ->joinWith([
                    'logStatusApprovalDrivers',
                    'logStatusApprovalDrivers.logStatusApprovalDriverActions',
                    'logStatusApprovalDrivers.logStatusApprovalDriverActions.logStatusApprovalDriver log_status_approval_driver_act',
                ])
                ->andWhere(['application_driver.id' => $id])
                ->asArray()->one();

            $modelStatusApprovalDriver = StatusApprovalDriver::find()
                ->joinWith([
                    'statusApprovalDriverRequires',
                    'statusApprovalDriverRequireActions',
                    'statusApprovalDriverRequireActions.statusApprovalDriverAction',
                ])
                ->andWhere(['status_approval_driver.id' => $post['status_approval_driver_id']])
                ->asArray()->one();

            $require = [];
            $err1 = '';

            foreach ($modelStatusApprovalDriver['statusApprovalDriverRequires'] as $key => $dataStatusApprovalDriverRequire) {

                $require[$key] = false;

                foreach ($modelApplicationDriver['logStatusApprovalDrivers'] as $dataLogStatusApprovalDriver) {

                    if ($dataStatusApprovalDriverRequire['require_status_approval_driver_id'] == $dataLogStatusApprovalDriver['status_approval_driver_id'] && $dataLogStatusApprovalDriver['is_actual']) {

                        $require[$key] = true;
                        break;
                    }
                }

                if (!$require[$key])
                    $err1 .= $dataStatusApprovalDriverRequire['require_status_approval_driver_id'] . ' ';
            }

            $result = true;

            foreach ($require as $value) {

                $result = $result && $value;
            }

            $require = [];
            $err2 = '';

            foreach ($modelStatusApprovalDriver['statusApprovalDriverRequireActions'] as $key => $dataStatusApprovalDriverRequireAction) {

                $require[$key] = false;

                foreach ($modelApplicationDriver['logStatusApprovalDrivers'] as $dataLogStatusApprovalDriver) {

                    foreach ($dataLogStatusApprovalDriver['logStatusApprovalDriverActions'] as $dataLogStatusApprovalDriverAction) {

                        if ($dataStatusApprovalDriverRequireAction['status_approval_driver_action_id'] == $dataLogStatusApprovalDriverAction['status_approval_driver_action_id'] && $dataLogStatusApprovalDriverAction['logStatusApprovalDriver']['application_driver_counter'] == $modelApplicationDriver['counter']) {

                            $require[$key] = true;
                            break;
                        }
                    }
                }

                if (!$require[$key])
                    $err2 .= $dataStatusApprovalDriverRequireAction['statusApprovalDriverAction']['name'] . ", ";
            }

            foreach ($require as $value) {

                $result = $result && $value;
            }

            if ($result) {

                $transaction = \Yii::$app->db->beginTransaction();
                $flag = false;

                $modelLogStatusApprovalDriver = new LogStatusApprovalDriver();
                $modelLogStatusApprovalDriver->application_driver_id = $modelApplicationDriver['id'];
                $modelLogStatusApprovalDriver->status_approval_driver_id = $post['status_approval_driver_id'];
                $modelLogStatusApprovalDriver->is_actual = true;
                $modelLogStatusApprovalDriver->application_driver_counter = $modelApplicationDriver['counter'];

                if (($flag = $modelLogStatusApprovalDriver->save())) {

                    $statusActual = $post['status_approval_driver_actual-' . $post['status_approval_driver_id']];

                    $modelStatusApprovalDriverActual = StatusApprovalDriver::find()
                        ->andWhere(['id' => $statusActual])
                        ->asArray()->one();

                    if (($flag = !empty($modelStatusApprovalDriverActual))) {

                        $result = true;

                        if ($modelStatusApprovalDriverActual['branch'] > 1) {

                            $checkLogStatusApprovalDriver = LogStatusApprovalDriver::find()
                                ->andWhere(['application_driver_id' => $modelApplicationDriver['id']])
                                ->andWhere(['!=', 'status_approval_driver_id', $statusActual])
                                ->andWhere(['application_driver_counter' => $modelApplicationDriver['counter']])
                                ->asArray()->all();

                            $modelStatusApprovalDriverRequire = StatusApprovalDriverRequire::find()
                                ->andWhere(['require_status_approval_driver_id' => $statusActual])
                                ->asArray()->all();

                            $require = [];

                            foreach ($modelStatusApprovalDriverRequire as $key => $$dataStatusApprovalDriverRequire) {

                                $require[$key] = false;

                                foreach ($checkLogStatusApprovalDriver as $dataCheckLogStatusApprovalDriver) {

                                    if ($$dataStatusApprovalDriverRequire['status_approval_driver_id'] == $dataCheckLogStatusApprovalDriver['status_approval_driver_id']) {

                                        $require[$key] = true;
                                        break;
                                    }
                                }
                            }

                            foreach ($result as $value) {

                                $result = $result && $value;
                            }
                        }

                        if (($flag = $result) && $modelStatusApprovalDriver['branch'] != 0) {

                            $modelLogStatusApprovalDriver = LogStatusApprovalDriver::find()
                                ->andWhere(['status_approval_driver_id' => $statusActual])
                                ->andWhere(['application_driver_id' => $modelApplicationDriver['id']])
                                ->andWhere(['application_driver_counter' => $modelApplicationDriver['counter']])
                                ->one();

                            $modelLogStatusApprovalDriver->is_actual = false;

                            $flag = $modelLogStatusApprovalDriver->save();
                        }

                        if ($modelStatusApprovalDriver['branch'] == 0) {

                            if ($modelStatusApprovalDriver['status'] != 'Finished-Fail') {

                                $requireStatusApprovalDriverId = [];

                                foreach ($modelStatusApprovalDriver['statusApprovalDriverRequires'] as $dataStatusApprovalDriverRequire) {

                                    $requireStatusApprovalDriverId[] = $dataStatusApprovalDriverRequire['require_status_approval_driver_id'];
                                }

                                $checkLogStatusApprovalDriver = LogStatusApprovalDriver::find()
                                    ->andWhere(['application_driver_id' => $modelApplicationDriver['id']])
                                    ->andWhere(['status_approval_driver_id' => $requireStatusApprovalDriverId])
                                    ->asArray()->all();

                                $result = true;

                                foreach ($checkLogStatusApprovalDriver as $dataCheckLogStatusApprovalDriver) {

                                    $result = $result && $dataCheckLogStatusApprovalDriver['is_actual'];

                                }

                                if ($result) {

                                    $flag = LogStatusApprovalDriver::updateAll(['is_actual' => false], ['AND', ['application_driver_id' => $modelApplicationDriver['id'], 'status_approval_driver_id' => $requireStatusApprovalDriverId]]) > 0;
                                }
                            } else {

                                $flag = LogStatusApprovalDriver::updateAll(['is_actual' => false], 'is_actual = TRUE AND status_approval_driver_id != :said AND application_driver_id = :appdriverid', ['said' => $post['status_approval_driver_id'], 'appdriverid' => $modelApplicationDriver['id']]) > 0;
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($modelStatusApprovalDriver['execute_action'])) {

                        $flag = $this->run($modelStatusApprovalDriver['execute_action'], ['appDriverId' => $modelApplicationDriver['id'], 'regDriverId' => $rdid]);
                    }
                }

                if ($flag) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', 'Update Status Sukses');
                    \Yii::$app->session->setFlash('message2', 'Proses update status sukses. Data telah berhasil disimpan.');

                    $transaction->commit();
                } else {

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', 'Update Status Gagal');
                    \Yii::$app->session->setFlash('message2', 'Proses update status gagal. Data gagal disimpan.');

                    $transaction->rollBack();
                }
            } else {

                $msg = '';

                if (!empty($err1)) {

                    $msg = 'Data ini belum melewati status: (<b>' . $err1 . '</b>)';

                    if (!empty($err2))
                        $msg .= ' dan ';
                }

                if (!empty($err2)) {

                    $msg .= 'Data ini belum melewati action: (<b>' . trim($err2, ', ') . '</b>)';
                }

                \Yii::$app->session->setFlash('status', 'danger');
                \Yii::$app->session->setFlash('message1', 'Update ' . $post['status_approval_driver_id'] . ' Gagal');
                \Yii::$app->session->setFlash('message2', 'Proses update status gagal. ' . $msg);
            }

            return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $rdid, 'appDriverId' => $modelApplicationDriver['id']]));
        }
    }

    private function indexDriver($statusApproval, $title)
    {
        $searchModel = new RegistryDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['log_status_approval_driver.status_approval_driver_id' => $statusApproval])
            ->andWhere(['log_status_approval_driver.is_actual' => true])
            ->andWhere('registry_driver.application_driver_counter = application_driver.counter')
            ->distinct();

        \Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('list_driver', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => $title,
            'statusApproval' => $statusApproval,
        ]);
    }
}
