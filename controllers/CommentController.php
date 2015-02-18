<?php

namespace itzen\comments\controllers;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use itzen\comments\models\search\Comment;
use common\components\JsonResponse;
use yii\helpers\Html;
use itzen\comments\widgets\CommentsWidget;

/**
 * CommentController implements the CRUD actions for Comment model.
 */
class CommentController extends Controller {
    public function behaviors() {
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
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new Comment;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Comment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }


    /**
     * Displays or updates a single Comment model.
     * @param integer $id
     * @return mixed
     */
    public function actionPartialView($id = null) {
        if ($id === null) {
            if (isset($_POST['expandRowKey'])) {
                $id = (int)$_POST['expandRowKey'];
            }
            if (isset($_POST['Comment']['id'])) {
                $id = (int)$_POST['Comment']['id'];
            }
        }

        $model = $this->findModel($id);

        if (isset($_POST['Comment'])) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return "success";
            } else {
                $this->renderPartial('_view', ['model' => $model]);
            }
        }

        return $this->renderPartial('_view', ['model' => $model]);
    }

    /**
     * Creates a new Comment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return jsonResponse
     */
    public function actionCreate() {
        /** @var JsonResponse $response */
        $response = Yii::$app->get('jsonResponse');

        if (!Yii::$app->user->can('/comments/comment/create')) {
            $response->setResponse(JsonResponse::STATUS_ERROR, JsonResponse::TYPE_DANGER, \Yii::t('common', 'You do not have the proper credential to perform this action'), 404);
        }
        $model = new Comment;
echo '<pre>';
print_r(Yii::$app->request->post());
echo '</pre>';
exit();
        if ($model->load(Yii::$app->request->post())) {
            $status = Yii::$app->getModule('status');
            $model->status_id = $status->defaultIds['comment'];
            if (!Yii::$app->user->isGuest) {
                $model->username = Yii::$app->user->getIdentity()->publicIdentity;
                $model->email = Yii::$app->user->getIdentity()->email;
            }
            if ($model->save()) {
                $renderedLastComment = $this->prepareLastCommentView($model);
                $response->setResponse(JsonResponse::STATUS_SUCCESS, JsonResponse::TYPE_SUCCESS, \Yii::t('common', 'Comment added successfully.'), 200, $renderedLastComment);
            } else {
                $response->setResponse(JsonResponse::STATUS_ERROR, JsonResponse::TYPE_DANGER, \Yii::t('common', 'Comment cannot be added.'), -1);
            }
        } else {
            $response->setResponse(JsonResponse::STATUS_ERROR, JsonResponse::TYPE_DANGER, \Yii::t('common', 'form error.'), 404);
        }

        return $response;
    }

    /**
     * Updates an existing Comment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Comment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function prepareLastCommentView($model) {
        $renderedLastComment = '';
        $commentsWidget = new CommentsWidget(array(
            'model' => $model
        ));

        $commentsWidget->setOptions();

        $renderedLastComment .= (count($commentsWidget->getCommentsAsTree()) == 1) ? Html::beginTag($commentsWidget->printOptions['tag'], $commentsWidget->printOptions['tagOptions']) : '';
        $renderedLastComment .= $commentsWidget->prepareSingleCommentView(array(
            'model' => $model,
            'children' => null
        ));
        $renderedLastComment .= (count($commentsWidget->getCommentsAsTree()) == 1) ? Html::beginTag($commentsWidget->printOptions['tag']) : '';

        $result = array(
            'renderedLastComment' => $renderedLastComment,
            'firstElement' => (count($commentsWidget->getCommentsAsTree()) == 1) ? true : false,
            'commentsCount' => $commentsWidget->commentsCount
        );

        return $result;
    }
}
