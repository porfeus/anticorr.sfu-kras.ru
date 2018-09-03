<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Theme]].
 *
 * @see TrainingModuleTheme
 */
class ThemeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TrainingModuleTheme[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TrainingModuleTheme|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
