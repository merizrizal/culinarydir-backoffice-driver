<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\RegistryDriver;
use core\models\search\RegistryDriverSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

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
                        'delete' => ['POST'],
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

    /**
     * Updates an existing RegistryDriver model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post())) {

            if (empty($save)) {

                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                if ($model->save()) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));
                } else {

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RegistryDriver model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (($model = $this->findModel($id)) !== false) {

            $flag = false;
            $error = '';

            try {
                $flag = $model->delete();
            } catch (\yii\db\Exception $exc) {
                $error = \Yii::$app->params['errMysql'][$exc->errorInfo[1]];
            }
        }

        if ($flag) {

            \Yii::$app->session->setFlash('status', 'success');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Delete Is Success'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Delete process is success. Data has been deleted'));
        } else {

            \Yii::$app->session->setFlash('status', 'danger');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Delete Is Fail'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Delete process is fail. Data fail to delete' . $error));
        }

        $return = [];

        $return['url'] = \Yii::$app->urlManager->createUrl(['registry-driver/index']);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    /**
     * Finds the RegistryDriver model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RegistryDriver the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegistryDriver::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function indexDriver($statusApproval, $title)
    {
        $searchModel = new RegistryDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query
        ->andWhere(['log_status_approval_driver.status_approval_driver_id' => $statusApproval])
        ->andWhere(['log_status_approval_driver.is_actual' => 1])
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
