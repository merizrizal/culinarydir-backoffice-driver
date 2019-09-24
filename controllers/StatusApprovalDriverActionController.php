<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\LogStatusApprovalDriverAction;
use core\models\RegistryDriver;
use sycomponent\AjaxRequest;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * StatusApprovalDriverActionController implements the CRUD actions for RegistryDriver model.
 */
class StatusApprovalDriverActionController extends BaseController
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

    /**
     * Lists all RegistryDriver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => RegistryDriver::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFixIncorrect($id, $appDriverId, $logsaid, $actid)
    {
        $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
        $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
        $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

        if ($modelLogStatusApprovalDriverAction->save()) {

            \Yii::$app->session->setFlash('status', 'success');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));
        } else {

            \Yii::$app->session->setFlash('status', 'danger');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
        }

        return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId]));
    }

    public function actionCheckSetDriverInfo($id, $appDriverId, $logsaid, $actid)
    {
        $model = RegistryDriver::find()
            ->joinWith([
                'district',
            ])
            ->andWhere(['registry_driver.id' => $id])
            ->asArray()->one();

        if ((\Yii::$app->request->post()) && !empty(\Yii::$app->request->post('check_set_driver_info'))) {

            $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
            $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
            $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

            if ($modelLogStatusApprovalDriverAction->save()) {

                \Yii::$app->session->setFlash('status', 'success');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId]));
            } else {

                \Yii::$app->session->setFlash('status', 'danger');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
            }
        }

        return $this->render('check_set_driver_info', [
            'model' => $model,
            'id' => $id,
            'appDriverId' => $appDriverId,
            'actid' => $actid,
            'logsaid' => $logsaid,
        ]);
    }

    /**
     * Creates a new RegistryDriver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null)
    {
        $render = 'create';

        $model = new RegistryDriver();

        if ($model->load(\Yii::$app->request->post())) {

            if (empty($save)) {

                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                if ($model->save()) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                    $render = 'view';
                } else {

                    $model->setIsNewRecord(true);

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));
                }
            }
        }

        return $this->render($render, [
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
}