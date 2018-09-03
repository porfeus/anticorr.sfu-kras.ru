<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UserAnswer]].
 *
 * @see UserAnswer
 */
class UserAnswerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserAnswer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAnswer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
