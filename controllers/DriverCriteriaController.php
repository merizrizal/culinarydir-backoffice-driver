<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\DriverCriteria;
use core\models\Settings;
use core\models\search\DriverCriteriaSearch;
use yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DriverCriteriaController implements the CRUD actions for DriverCriteria model.
 */
class DriverCriteriaController extends BaseController
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
     * Lists all DriverCriteria models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DriverCriteriaSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DriverCriteria model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = DriverCriteria::find()
        ->joinWith([
            'personAsDriver.person'
        ])
        ->andWhere(['driver_criteria.id' => $id])
        ->asArray()->one();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new DriverCriteria model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null)
    {
        $render = 'create';

        $model = new DriverCriteria();
        $dataDriverCriteria = [];

        if (!empty(($post = \Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = \Yii::$app->db->beginTransaction();
                $flag = false;

                if ($post['DriverCriteria']['criteria_name']['driver']) {

                    foreach ($post['DriverCriteria']['criteria_name']['driver'] as $categoryDriver) {

                        $newModelDriverCriteria= new DriverCriteria();
                        $newModelDriverCriteria->person_as_driver_id = 'e6113a31399e46cf0e1a9d0554405816';
                        $newModelDriverCriteria->type = 'Driver';
                        $newModelDriverCriteria->criteria_name = $categoryDriver;

                        if (!($flag = $newModelDriverCriteria->save())) {

                            break;
                        } else {

                            array_push($dataDriverCriteria, $newModelDriverCriteria->toArray());
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['DriverCriteria']['criteria_name']['motor'])) {

                        foreach ($post['DriverCriteria']['criteria_name']['motor'] as $categoryMotor) {

                            $newModelMotorCriteria= new DriverCriteria();
                            $newModelMotorCriteria->person_as_driver_id = 'e6113a31399e46cf0e1a9d0554405816';
                            $newModelMotorCriteria->type = 'Motor';
                            $newModelMotorCriteria->criteria_name = $categoryMotor;

                            if (!($flag = $newModelMotorCriteria->save())) {

                                break;
                            } else {

                                array_push($dataDriverCriteria, $newModelMotorCriteria->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                    $transaction->commit();
                    $render = 'view';
                } else {

                    $model->setIsNewRecord(true);

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        $dataJsonCriteria = Settings::find()
            ->andWhere(['setting_name' => [
                'driver_criteria',
                'motor_criteria'
            ]])
            ->asArray()->all();

        $arrayJsonCriteria = [];

        foreach ($dataJsonCriteria as $dc) {

            $arrayJsonCriteria[$dc['setting_name']] = $dc['setting_value'];
        }

        $driverCriteria = json_decode($arrayJsonCriteria['driver_criteria'], true);
        $motorCriteria = json_decode($arrayJsonCriteria['motor_criteria'], true);

        return $this->render($render, [
            'model' => $model,
            'driverCriteria' => $driverCriteria,
            'motorCriteria' => $motorCriteria,
        ]);
    }

    /**
     * Updates an existing DriverCriteria model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $model = DriverCriteria::find()
            ->joinWith([
                'personAsDriver.person'
            ])
            ->andWhere(['driver_criteria.id' => $id])
            ->asArray()->one();

        if ($model->load(\Yii::$app->request->post())) {

            if (!empty($save)) {

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

        $dataJsonCriteria = Settings::find()
            ->andWhere(['setting_name' => [
                'driver_criteria',
            'motor_criteria'
            ]])
            ->asArray()->all();

        $arrayJsonCriteria = [];

        foreach ($dataJsonCriteria as $dc) {

            $arrayJsonCriteria[$dc['setting_name']] = $dc['setting_value'];
        }

        $driverCriteria = json_decode($arrayJsonCriteria['driver_criteria'], true);
        $motorCriteria = json_decode($arrayJsonCriteria['motor_criteria'], true);

        return $this->render('update', [
            'model' => $model,
            'driverCriteria' => $driverCriteria,
            'motorCriteria' => $motorCriteria,
        ]);
    }

    /**
     * Deletes an existing DriverCriteria model.
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
            } catch (yii\db\Exception $exc) {
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

        $return['url'] = \Yii::$app->urlManager->createUrl(['driver-criteria/index']);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    /**
     * Finds the DriverCriteria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return DriverCriteria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DriverCriteria::findOne($id)) !== null) {

            return $model;
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
