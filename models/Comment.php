<?php

namespace itzen\comments\models;

use itzen\comments\Module;
use kartik\grid\GridView;
use Yii;
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
class Comment extends ActiveRecord
{
    public $expandalbe = GridView::ROW_COLLAPSED;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%core_comment}}';
    }
 
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sortorder', 'status_id', 'user_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'rating', 'object_id'], 'integer'],
            [['status_id', 'created_at', 'created_by', 'body', 'object_id', 'object_key'], 'required'],
            [['body'], 'string'],
            [['username', 'email'], 'string', 'max' => 45],
            [['website', 'object_key'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
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
        return parent::find()->orderBy('sortorder asc');
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

    

}
