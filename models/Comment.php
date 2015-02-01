<?php

namespace itzen\comments\models;

use common\models\User;
use itzen\comments\behaviors\CommentableBehavior;
use itzen\comments\Module;
use kartik\grid\GridView;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%core_comment}}".
 *
 * @property integer $id
 * @property integer $sortorder
 * @property integer $status_id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $username
 * @property string $email
 * @property string $website
 * @property string $body
 * @property integer $rating
 * @property integer $object_id
 * @property string $object_key
 */
class Comment extends ActiveRecord {

    public $expandalbe = GridView::ROW_COLLAPSED;

    public $children;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%core_comment}}';
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
            [
                'class' => BlameableBehavior::className(),
                'value' => function () {
                    $user = Yii::$app->get('user', false);
                    if ($user) {
                        return !$user->isGuest ? $user->identity->username : Yii::t('common', 'Guest');
                    } else {
                        return 'WEB APP';
                    }
                }
            ],
            [
                'class' => CommentableBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sortorder', 'status_id', 'user_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'rating', 'object_id', 'parent_id'], 'integer'],
            [['status_id', 'body', 'object_id', 'object_key'], 'required'],
            [['body'], 'string'],
            [['username', 'email'], 'string', 'max' => 45],
            [['website', 'object_key'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('common', 'ID'),
            'parent_id' => Yii::t('common', 'Parent comment'),
            'sortorder' => Yii::t('common', 'Sortorder'),
            'status_id' => Yii::t('common', 'Status ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'Email'),
            'website' => Yii::t('common', 'Website'),
            'body' => Yii::t('common', 'Body'),
            'rating' => Yii::t('common', 'Rating'),
            'object_id' => Yii::t('common', 'Object ID'),
            'object_key' => Yii::t('common', 'Object Key'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find($q = null) {
        return parent::find();
    }

    /**
     * @return []
     */
    public function getAvailableStatuses() {
        return Module::getStatuses();
    }

    /**
     * @return []
     */
    public function getAvailableUsers() {
        return Module::$users;
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id'])->inverseOf('children');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])->inverseOf('parent');
    }

}
