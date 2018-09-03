<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Module]].
 *
 * @see TrainingModule
 */
class ModuleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Module[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Module|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Сортирует по полю SORT
     * @return $this
     */
    public function sort()
    {
        return $this->orderBy(['sort' => Module::DEFAULT_SORT_ORDER]);
    }
}
