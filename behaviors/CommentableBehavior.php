<?php

namespace itzen\comments\behaviors;

use Closure;
use itzen\comments\models\Comment;
use itzen\status\models\Status;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class CommentableBehavior
 * @property ActiveRecord $owner
 * @package itzen\comments\behaviors
 */
class CommentableBehavior extends Behavior
{


    /**
     * @var int|Closure
     * Id of the model or Closure. Default to primary key.
     */
    public $object_id;

    /**
     * @var string
     * Key that uniquely identifies the model. Default to fully qualified class name of the model.
     */
    public $object_key;


    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind'
        ];
    }

    /**
     * Adds properties to model which are required by this behavior
     * @throws InvalidConfigException
     */
    public function afterFind()
    {
        if (!$this->object_id) {
            $primaryKey = $this->owner->primaryKey();
            if (isset($primaryKey[0])) {
                $this->object_id = $this->owner->$primaryKey[0];
            } else {
                throw new InvalidConfigException('"' . get_class($this->owner) . '" must have a primary key.');
            }
        } elseif ($this->object_id instanceof Closure) {
            $this->object_id = call_user_func($this->object_id, $this->owner);
        }

        if (!$this->object_key) {
            $this->object_key = get_class($this->owner);
        }
    }

    public function afterDelete()
    {

    }

    /**
     * @param Comment $comment
     * @return bool
     * @throws InvalidConfigException
     */
    public function addComment(Comment $comment)
    {
        $comment->object_id = $this->object_id;
        $comment->object_key = $this->object_key;
        $commentModule = Yii::$app->getModule('comments');
        $status = Status::find()->where(['and', 'id=:id',  ['or', 'object_key IS NULL', 'object_key=:object_key']]
        )->params([
            ':id'=>$commentModule->defaultStatusId,
            ':object_key' => $this->object_key])->one();
        if ($status === null) {
            throw new InvalidConfigException("Default status id must be valid id from status table.");
        }
        $comment->status_id = $commentModule->defaultStatusId;

        if ($comment->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function getComments()
    {
        $modelParams = [];
        $modelParams['Comment']['object_id'] = $this->object_id;
        $modelParams['Comment']['object_key'] = $this->object_key;

        $params = ArrayHelper::merge(Yii::$app->request->getQueryParams(), $modelParams);
        $searchModel = new \itzen\comments\models\search\Comment();

        $dataProvider = $searchModel->search($params);

        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ];
    }


}
