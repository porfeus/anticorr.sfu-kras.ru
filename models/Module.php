<?php

namespace app\models;

use app\modules\admin\models\settings\Main;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%module}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $sort
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $author
 * @property string $test_title
 * @property string $test_comment_after
 *
 * @property string $fullTitle
 *
 * @property Theme[] $themes
 * @property Question[] $questions
 * @property UserAnswer[] $userAnswers
 * @property UserDone $userDone
 * @property UserDone[] $userDones
 * @property ModuleFile[] $files
 * @property ModuleFile[] $filesAttached
 */
class Module extends \yii\db\ActiveRecord
{
    /** Порядок сортировки по умолчания для поля $sort */
    const DEFAULT_SORT_ORDER = SORT_ASC;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['sort', 'created_at', 'updated_at'], 'integer'],
            [['title', 'author', 'test_title'], 'string', 'max' => 255],
            ['sort', 'default', 'value' => 0],
            [['author','button_name'], 'trim'],
            [['test_comment_after'], 'string'],
            ['author', 'default', 'value' => null],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'sort' => 'Sort',
            'created_at' => 'Добавлен',
            'updated_at' => 'Изменен',
            'author' => 'Автор(ы)',
            'test_title' => 'Заголовок теста',
            'test_comment_after' => 'Комментарий после теста',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThemes()
    {
        return $this->hasMany(Theme::className(), ['module_id' => 'id'])
            ->andWhere(['parent_id' => null])
            ->orderBy(['sort' => Theme::DEFAULT_SORT_ORDER]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['module_id' => 'id'])->andWhere(['theme_id' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAnswers()
    {
        return $this->hasMany(UserAnswer::className(), ['module_id' => 'id'])->andWhere(['theme_id' => null, 'user_id' => self::$userId]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDone()
    {
        return $this->hasOne(UserDone::className(), ['module_id' => 'id'])->andWhere(['theme_id' => null, 'user_id' => self::$userId]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDones()
    {
        return $this->hasMany(UserDone::className(), ['module_id' => 'id'])->andWhere(['user_id' => self::$userId]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(ModuleFile::className(), ['module_id' => 'id']);
    }

    /**
     * @param integer $type ThemeFile::TYPE_* constants
     * @return array|ModuleFile[]
     */
    public function filesByType($type)
    {
        return array_filter($this->files, function ($file) use ($type) {
            /** @var ModuleFile $file */
            return $file->type === $type;
        });
    }

    /**
     * @return ModuleFile[]|array
     */
    public function getFilesAttached()
    {
        return $this->filesByType(ModuleFile::TYPE_ATTACHED);
    }

    /**
     * @inheritdoc
     * @return ModuleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModuleQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getFullTitle()
    {
        return implode(' ', array_filter([
            $this->title,
            $this->author(),
        ]));
    }

    /**
     * @return null|string
     */
    public function author()
    {
        $author_array = explode(",", $this->author);
		$author_counts = count($author_array);
		//echo var_dump( explode(",", $this->author));
		//if ($this->author) {
			//return '(автор(ы) ' . $this->author . ')';
		if ($author_counts==1&iconv_strlen($author_array[0],'UTF-8')>0) 
			{
			return '(автор: ' . $this->author . ')';
			}
		if ($author_counts>1) 
			{
			return '(авторы: ' . $this->author . ')';
			}
		if ($author_counts==0) 
			{
			return null;
			}
		
		
		
        return null;
    }

    public static $userId;
    public static $tests = false;
    //public function isCurrent()
    //{
    //    if (count($this->userAnswers) === 0) {
    //        if (is_null(self::$current)) {
    //            self::$current = $this->id;
    //
    //            $current = array_reduce($this->themes, function ($count, $theme) {
    //                /** @var Theme $theme */
    //                if (!$theme->isCurrent()) {
    //                    $count++;
    //                }
    //                return $count;
    //            }, 0);
    //
    //            if (count($this->questions) > 0) {
    //                self::$tests = $current === count($this->themes);
    //            } else {
    //                if ($current === count($this->themes)) {
    //                    self::$current = null;
    //                }
    //            }
    //        }
    //    }
    //    return self::$current === $this->id;
    //}

    public static $current;


    public function isAwaitTesting()
    {
        if ($this->hasQuestions()) {
            if ($this->userDone && $this->userDone->count_try === 0) {
                return true;
            }
        }
        return false;
    }

    public function isChecked()
    {
        return $this->userDone ? true : false;
    }

    public function hasQuestions()
    {
        return count($this->questions) > 0;
    }
    
    public function hasAwait()
    {
        $awaits = array_reduce($this->themes, function($awaits, $theme) {
            /** @var Theme $theme */
            if ($theme->hasAwait()) {
                $awaits++;
            }
            return $awaits;
        }, 0);
        
        if ($this->hasQuestions()) {
            if (!$this->userDone || $this->userDone->count_try === 0) {
                $awaits++;
            } elseif (($result = $this->getResult()) !== null) {
                //Даже если тест модуля пройден на 0 то будем считать что можно идти дальше 
                //if ($result < 51) {
                    //$awaits++;
                //}
            }
        } else {
            if (!$this->userDone) {
                $awaits++;
            }
        }
        return $awaits;
    }

    public function isCurrent()
    {
        if (is_null(self::$current)) {
            if ($this->hasAwait()) {
                self::$current = $this->id;
            }
        }
        return self::$current === $this->id;
    }

    public function getResult()
    {
        if ($this->hasQuestions() && $this->userDone && $this->userDone->count_try > 0) {
            //if (!$this->hasAwait()) {
                $total = 0;
                $count = 0;
                foreach ($this->questions as $question) {
                    foreach ($question->answers as $answer) {
                        if ($answer->is_true) {
                            $total++;
                            foreach ($this->userAnswers as $userAnswer) {
                                if ($userAnswer->answer_id === $answer->id) {
                                    $count++;
                                }
                            }
                        }
                    }
                }
                return floor($count / $total * 100);
            //}
        }
        return null;
    }
    
    public function hasTry()
    {
//        return $this->hasQuestions() && $this->userDone && $this->userDone->count_try < 3;
        return $this->hasQuestions() && $this->userDone && $this->userDone->count_try > 0;
    }

    public function incrTry()
    {
        if ($this->userDone) {
            $this->userDone->updateCounters(['count_try' => 1]);
        }
    }

    public function decrTry()
    {
        if ($this->userDone) {
            $this->userDone->updateCounters(['count_try' => -1]);
            $this->userDone->tests_failed = null;
            $this->userDone->save(false, ['tests_failed']);
        }
    }

    public function getDoneAt()
    {
        if ($this->userDone) {
            return Yii::$app->formatter->asDatetime($this->userDone->updated_at, 'dd.MM.yy в HH:mm');
        }
        return null;
    }

    /**
     * @return bool
     */
    public function canTesting()
    {
        return $this->isCurrent() && $this->isAwaitTesting();
    }

    /**
     * @return bool
     */
    public function canRepeatTesting()
    {
//        return $this->getResult() < 100 && $this->hasTry();
        return $this->getResult() !== null;
    }

    public function canGodPermission()
    {
        return Yii::$app->user->can('admin');
    }

    public function checkTestsResult()
    {
        $result = $this->getResult();
        $userDone = $this->userDone;
        if ($result !== null && $result < 51 && $userDone->count_try >= 3) {
            $userDone->tests_failed = true;
            if ($email = Main::value('adminEmail')) {
                Yii::$app->mailer->compose()
                    ->setTo($email)
                    ->setFrom([Yii::$app->params['adminEmail'] => Main::value('siteName')])
                    ->setSubject(sprintf('%s не прошел тесты по модулю', $userDone->user->username))
                    ->setTextBody(
                        sprintf('%s не прошел тесты по %s', $userDone->user->username, $this->title) .
                        sprintf("\nПодробнее: %s", Url::toRoute(['/admin/user/view', 'id' => $userDone->user_id, 'module_id' => $this->id], true))
                    )
                    ->send();
            }
        } else {
            $userDone->tests_failed = false;
        }
        $userDone->save(false, ['tests_failed']);
    }
}
