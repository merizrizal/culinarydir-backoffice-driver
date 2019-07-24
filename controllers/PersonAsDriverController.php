<?php

namespace backoffice\modules\driver\controllers;

use core\models\DriverAttachment;
use core\models\DriverCriteria;
use core\models\Person;
use core\models\PersonAsDriver;
use core\models\Settings;
use core\models\search\PersonAsDriverSearch;
use yii\filters\VerbFilter;

/**
 * PersonAsDriverController implements the CRUD actions for PersonAsDriver model.
 */
class PersonAsDriverController extends \backoffice\controllers\BaseController
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
     * Lists all PersonAsDriver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PersonAsDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionView($id)
    {
        $model = PersonAsDriver::find()
            ->joinWith([
                'person',
                'district'
            ])
            ->andWhere(['person_as_driver.person_id' => $id])
            ->asArray()->one();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new PersonAsDriver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null)
    {
        $render = 'create';

        $model = new PersonAsDriver();
        $modelPerson = new Person();
        $modelDriverCriteria = new DriverCriteria();
        $modelDriverAttachment = new DriverAttachment();


        if ($model->load(\Yii::$app->request->post()) && $modelPerson->load(\Yii::$app->request->post())) {

            if (!empty($save)) {

                $flag = false;
                $transaction = \Yii::$app->db->beginTransaction();

                if (($flag = $modelPerson->save())) {

                    $model->person_id = $modelPerson->id;
                    $model->save();
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

        $dataJson = Settings::find()
        ->andWhere(['setting_name' => ['motor_brand', 'motor_type']])
        ->asArray()->all();

        $dataArray =[];

        foreach ($dataJson as $a) {

            $dataArray[$a['setting_name']] = $a['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);

        return $this->render($render, [
            'model' => $model,
            'modelPerson' => $modelPerson,
            'modelDriverCriteria' => $modelDriverCriteria,
            'modelDriverAttachment' => $modelDriverAttachment,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
        ]);
    }

    /**
     * Updates an existing PersonAsDriver model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $render = 'update';

        $model = PersonAsDriver::find()
            ->joinWith([
                'person',
            ])
            ->andWhere(['person_as_driver.person_id' => $id])
            ->one();

        $modelPerson = $model->person;

        if ($model->load(\Yii::$app->request->post()) && $modelPerson->load(\Yii::$app->request->post())) {

            $flag = false;
            $transaction = \Yii::$app->db->beginTransaction();

            if (!empty($save)) {

                if (($flag = $model->save())) {

                    $modelPerson->save();
                }

                if ($flag) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                    $transaction->commit();

                    $render = 'view';
                } else {

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        $dataJson = Settings::find()
        ->andWhere(['setting_name' => ['motor_brand', 'motor_type']])
        ->asArray()->all();

        $dataArray =[];

        foreach ($dataJson as $a) {

            $dataArray[$a['setting_name']] = $a['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);

        return $this->render($render, [
            'model' => $model,
            'modelPerson' => $modelPerson,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
        ]);
    }
}
