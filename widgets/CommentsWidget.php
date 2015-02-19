<?php

namespace itzen\comments\widgets;

use itzen\comments\models\Comment;
use Yii;
use yii\base\Widget;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use itzen\comments\CommentsAsset;

/**
 * Comment widget
 * @author Paweł Kania
 */
class CommentsWidget extends Widget {
    public $model;
    public $printOptions = [];
    public $commentsCount;
    public $view = 'comments';

    public function getCommentsAsTree() {
        $modelParams = [];
        $modelParams['object_id'] = $this->model->object_id;
        $modelParams['object_key'] = $this->model->object_key;

        $dependencyParams = [];
        $dependencyParams[':object_id'] = $this->model->object_id;
        $dependencyParams[':object_key'] = $this->model->object_key;

        $db = Yii::$app->db;
        $data = $db->cache(function ($db) use ($modelParams) {
            return Comment::find()->where($modelParams)->with('user.profile')->all();
        }, 3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM {{%core_comment}} WHERE object_id=:object_id AND object_key=:object_key', 'params' => $dependencyParams, 'reusable' => true])
        );
        $this->commentsCount = count($data);
        $tree = $this->parseCommentTree($data);
        return $tree;
    }

    function parseCommentTree($tree, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach ($tree as $key => $element) {
            # A direct child is found
            if ($element->parent_id == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$key]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'model' => $element,
                    'children' => $this->parseCommentTree($tree, $element->id)
                );
            }
        }
        return empty($return) ? null : $return;
    }

    public function htmlCommentTree($tree) {
        if (!is_null($tree) && count($tree) > 0) {
            $comments = Html::beginTag($this->printOptions['tag'], $this->printOptions['tagOptions']);
            foreach ($tree as $node) {
                $comments .= $this->prepareSingleCommentView($node);
            }
            $comments .= Html::endTag($this->printOptions['tag']);
            return $comments;
        }
    }


    public function run() {
        $this->setOptions();
        $comments = $this->getCommentsAsTree();

        $comment = new Comment();
        $comment->object_id = $this->model->id;
        $comment->object_key = $this->model->object_key;
        $assets = CommentsAsset::register($this->getView());
        if (Yii::$app->getModule('comments')->defaultAvatar === null) {
            Yii::$app->getModule('comments')->defaultAvatar = $assets->baseUrl . '/avatar.png';
        }

        return $this->view
            ? $this->render($this->view, [
                'model' => $comment,
                'comments' => $this->htmlCommentTree($comments),
                'commentsCount' => $this->commentsCount,
            ])
            : $this->htmlCommentTree($comments);
    }

    /**
     * Generate single comment view.
     * This view will be added to the comments tree.
     * @param array $node
     * @return string
     */
    public function prepareSingleCommentView($node) {
        $comment = Html::beginTag($this->printOptions['elementTag'], $this->printOptions['elementOptions']);
        $parts = [];
        if ($node['model']->user !== null) {
            $avatar = Html::img($node['model']->user->profile->getAvatar(), ['alt' => $node['model']->username, 'class' => 'media-object avatar']);
            $parts['{userurl}'] = Url::toRoute(['/user/default/profile', 'id' => $node['model']['user_id']]);
        } else {
            $avatar = Html::img(Yii::$app->getModule('comments')->defaultAvatar, ['alt' => $node['model']->username, 'class' => 'media-object avatar']);
            $parts['{userurl}'] = "#";
        }
        $username = Html::encode($node['model']['username']);

        if ($node['model']->user !== null) {
            $username = Html::a($username, $parts['{userurl}']);
        }

        $comment .= $this->render('comment', [
            'avatar' => $avatar,
            'username' => $username,
            'rating' => $node['model']->rating,
            'userurl' => $node['model']['user_id'] === null ? null : Url::toRoute(['/user/default/profile', 'id' => $node['model']['user_id']]),
            'date' => $node['model']['created_at'],
            'body' => $node['model']['body']
        ]);

        $comment .= $this->htmlCommentTree($node['children']);
        $comment .= Html::endTag($this->printOptions['elementTag']);

        return $comment;
    }

    /**
     * Setting comments tree options
     */
    public function setOptions() {
        $this->printOptions['tag'] = ArrayHelper::getValue($this->printOptions, 'tag', 'ul');
        $this->printOptions['tagOptions'] = ArrayHelper::getValue($this->printOptions, 'tagOptions', ['class' => 'media-list']);
        $this->printOptions['elementTag'] = ArrayHelper::getValue($this->printOptions, 'elementTag', 'li');
        $this->printOptions['elementOptions'] = ArrayHelper::getValue($this->printOptions, 'elementOptions', ['class' => 'media']);
        $this->printOptions['template'] = ArrayHelper::getValue($this->printOptions, 'template', "<div class=\"media-left\">\n<a href=\"{userurl}\">\n{avatar}\n</a>\n</div>\n<div class=\"media-body\"><div class=\"well\">{username}{date}\n{actions}\n{body}</div></div>");
        $this->printOptions['avatarOptions'] = ArrayHelper::getValue($this->printOptions, 'avatarOptions', ['class' => 'thumbnail']);
        $this->printOptions['usernameOptions'] = ArrayHelper::getValue($this->printOptions, 'usernameOptions', ['class' => 'comment-username']);
        $this->printOptions['dateOptions'] = ArrayHelper::getValue($this->printOptions, 'dateOptions', ['class' => 'comment-date']);
        $this->printOptions['bodyOptions'] = ArrayHelper::getValue($this->printOptions, 'bodyOptions', ['class' => 'comment-body']);
        $this->printOptions['actionsOptions'] = ArrayHelper::getValue($this->printOptions, 'actionsOptions', ['class' => 'comment-actions']);
    }
}