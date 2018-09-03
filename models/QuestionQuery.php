<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Question]].
 *
 * @see Question
 */
class QuestionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Question[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Question|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $id
     * @return QuestionQuery
     */
    public function module($id)
    {
        return $this->andWhere(['module_id' => $id]);
    }

    /**
     * @param $id
     * @return QuestionQuery
     */
    public function theme($id)
    {
        return $this->andWhere(['theme_id' => $id]);
    }
}
