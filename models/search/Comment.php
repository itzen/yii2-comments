<?php

namespace itzen\comments\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use itzen\comments\models\Comment as CommentModel;

/**
 * Comment represents the model behind the search form about `common\models\core\Comment`.
 */
class Comment extends CommentModel
{
    public function rules()
    {
        return [
            [['id', 'sortorder', 'status_id', 'user_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'rating', 'object_id', 'object_key'], 'integer'],
            [['username', 'email', 'website', 'body'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CommentModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sortorder' => $this->sortorder,
            'status_id' => $this->status_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'rating' => $this->rating,
            'object_id' => $this->object_id,
            'object_key' => $this->object_key,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
