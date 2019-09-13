<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\RegistryDriver;
use core\models\RegistryDriverAttachment;
use core\models\Settings;
use core\models\search\RegistryDriverSearch;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * RegistryDriverController implements the CRUD actions for RegistryDriver model.
 */
class RegistryDriverController extends BaseController
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
        $searchModel = new RegistryDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RegistryDriver model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        $modelRegistryDriverAttachment = new RegistryDriverAttachment();
        $modelRegistryDriverAttachment->setScenario(RegistryDriverAttachment::SCENARIO_CREATE);

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($model->load($post) && $modelRegistryDriverAttachment->load($post)) {

                if (!empty($save)) {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = false;

                    if (($flag = $model->save())) {

                        $images = Tools::uploadFiles('/img/driver_attachment/', $modelRegistryDriverAttachment, 'file_name', 'registry_driver_id', '', true);

                        if (($flag = count($post['RegistryDriverAttachment']['type']) == count($images))) {

                            foreach ($images as $i => $image) {

                                $newModelRegistryDriverAttachment = new RegistryDriverAttachment();
                                $newModelRegistryDriverAttachment->registry_driver_id = $model->id;
                                $newModelRegistryDriverAttachment->file_name = $image;
                                $newModelRegistryDriverAttachment->type = $post['RegistryDriverAttachment']['type'][$i];

                                if (!($flag = $newModelRegistryDriverAttachment->save())) {

                                    break;
                                }
                            }
                        } else {

                            $modelRegistryDriverAttachment->addError('type', \Yii::t('app', 'Number of files and number of photos does not match.'));
                        }
                    }

                    if ($flag) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                        $transaction->commit();

                    } else {

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                        $transaction->rollback();
                    }
                }
            }
        }

        $modelSettings = Settings::find()
        ->andWhere(['setting_name' => ['motor_brand', 'motor_type', 'attachment_type']])
        ->asArray()->all();

        $dataArray = [];

        foreach ($modelSettings as $dataSettings) {

            $dataArray[$dataSettings['setting_name']] = $dataSettings['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);
        $attachmentType = json_decode($dataArray['attachment_type'], true);

        return $this->render($render, [
            'model' => $model,
            'modelRegistryDriverAttachment' => $modelRegistryDriverAttachment,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
            'attachmentType' => $attachmentType,
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
