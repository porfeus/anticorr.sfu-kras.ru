<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ThemeFile]].
 *
 * @see ThemeFile
 */
class ThemeFileQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ThemeFile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ThemeFile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
