<?php

namespace app\controllers;

use app\models\Province;
use Yii;
use app\models\Amphur;
use app\models\search\AmphurSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AmphurController implements the CRUD actions for Amphur model.
 */
class AmphurController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Amphur models.
     * @return mixed
     *
     * $id = provinceId
     */
    public function actionIndex($provinceId = null)
    {
        $searchModel = new AmphurSearch();

        $provinceId = isset($_GET['AmphurSearch']['provinceId']) ? $_GET['AmphurSearch']['provinceId'] : $provinceId;

        $searchModel->provinceId = $provinceId;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $provinceModel = Province::find()->where(['provinceId' => $provinceId])->one();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'provinceModel' => $provinceModel,
            'provinceName' => $provinceModel->provinceName
        ]);
    }

    /**
     * Displays a single Amphur model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Amphur model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Amphur();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->amphurId]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Amphur model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->amphurId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Amphur model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Amphur model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Amphur the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Amphur::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAmphur()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parent = $_POST['depdrop_parents'];
            $provinceId = $parent[0];

            $amphurs = Amphur::find()->where(['provinceId' => $provinceId])->all();

            $output = [];
            foreach ($amphurs as $amphur) {
                $output[] = ['id'=>$amphur->amphurId, 'name'=>$amphur->amphurName];
            }

            echo Json::encode(['output' => $output, 'selected' => '']);
        } else
            echo Json::encode(['output' => '', 'selected' => '']);
    }
}
