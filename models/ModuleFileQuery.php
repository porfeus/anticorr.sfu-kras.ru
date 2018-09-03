<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ModuleFile]].
 *
 * @see ModuleFile
 */
class ModuleFileQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ModuleFile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ModuleFile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
