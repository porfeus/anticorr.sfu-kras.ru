<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%theme}}".
 *
 * @property integer $id
 * @property integer $module_id
 * @property string $title
 * @property string $description
 * @property string $video_url
 * @property integer $sort
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $parent_id
 * @property string $test_comment_after
 * @property string $test_title
 * @property string $button_name
 *
 * @property Theme $parent
 * @property Theme[] $themes
 * @property Module $module
 * @property Question[] $questions
 * @property UserAnswer[] $userAnswers
 * @property UserDone $userDone
 * @property ThemeFile[] $files
 */
class Theme extends \yii\db\ActiveRecord
{
    /** Порядок сортировки по умолчания для поля $sort */
    const DEFAULT_SORT_ORDER = SORT_ASC;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%theme}}';
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
            [['module_id', 'title', 'description'], 'required'],
            [['module_id', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['description', 'test_comment_after','button_name'], 'string'],
            [['title', 'video_url', 'test_title'], 'string', 'max' => 255],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
            ['video_url', 'url'],
            ['sort', 'default', 'value' => 0],

            ['parent_id', 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Theme::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => 'Module ID',
            'title' => 'Название темы',
            'description' => 'Текст по теме',
            'video_url' => 'Видео к теме',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'parent_id' => 'Parent ID',
            'test_title' => 'Заголовок теста',
            'test_comment_after' => 'Комментарий после теста',
            'button_name' => 'Название кнопки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Theme::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThemes()
    {
        return $this->hasMany(Theme::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['theme_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAnswers()
    {
        return $this->hasMany(UserAnswer::className(), ['theme_id' => 'id', 'module_id' => 'module_id'])->andWhere(['user_id' => self::$userId]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDone()
    {
        return $this->hasOne(UserDone::className(), ['theme_id' => 'id', 'module_id' => 'module_id'])->andWhere(['user_id' => self::$userId]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(ThemeFile::className(), ['theme_id' => 'id']);
    }

    /**
     * @param integer $type ThemeFile::TYPE_* constants
     * @return array|ThemeFile
     */
    public function filesByType($type)
    {
        return array_filter($this->files, function ($file) use ($type) {
            /** @var ThemeFile $file */
            return $file->type === $type;
        });
    }

    /**
     * @inheritdoc
     * @return ThemeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ThemeQuery(get_called_class());
    }

    public static $userId;
    public static $current;
    public static $currentLvl2;

    public function hasAwaitingStudy()
    {
        return !$this->userDone && array_reduce($this->themes, function($hasAwait, $theme) {
            /** @var Theme $theme */
            if ($theme->hasAwait()) {
                return $hasAwait &&false;
            }
            return $hasAwait;
        }, true);
    }

    public function hasAwaitingTesting()
    {
        if (count($this->questions) > 0) { // если есть вопросы
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
        $awaits = 0;
        if ($this->hasQuestions()) {
            if (!$this->userDone || $this->userDone->count_try === 0) {
                $awaits++;
            }
        } else {
            if (!$this->userDone) {
                $awaits++;
            }
        }
        $awaits += array_reduce($this->themes, function($awaits, $theme) {
            /** @var Theme $theme */
            if ($theme->hasAwait()) {
                $awaits++;
            }
            return $awaits;
        }, 0);
        return $awaits > 0;
    }

    public function isCurrent()
    {
        if (!$this->parent_id) {
            if (is_null(self::$currentLvl2)) {
                if ($this->hasAwait()) {
                    self::$currentLvl2 = $this->id;
                }
            }
            return self::$currentLvl2 === $this->id;
        } else {
            if (is_null(self::$current)) {
                if ($this->hasAwait()) {
                    self::$current = $this->id;
                }
            }
            return self::$current === $this->id;
        }

    }

    public function getResult()
    {
        if ($this->hasQuestions()) {
            if (!$this->hasAwait()) {
                $total = 0;
                $count = 0;
                foreach ($this->questions as $question) {
                    foreach ($question->answers as $answer) {
                        if ($answer->is_true === 1) {
                            $total++;
                            foreach ($this->userAnswers as $userAnswer) {
                                if ($userAnswer->answer_id === $answer->id) {
                                    $count++;
                                }
                            }
                        } elseif ($answer->is_true == 0 && $question->multipleTrueAnswers()) {
                            foreach ($this->userAnswers as $userAnswer) {
                                if ($userAnswer->answer_id === $answer->id) {
                                    $count--;
                                }
                            }
                        }
                    }
                }
                return floor($count / $total * 100);
            }
        }
        return null;
    }

    public function hasTry()
    {
//        return $this->hasQuestions() && $this->userDone && $this->userDone->count_try < 2;
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
        }
    }

    /**
     * @param array $options
     * @return null|string
     */
    public function getVideo($options = [])
    {
        if (empty($this->video_url)) {
            return null;
        }

        parse_str(parse_url($this->video_url, PHP_URL_QUERY), $url);
        if (!isset($url['v'])) {
            return null;
        }
        $default = [
            'src' => 'https://www.youtube.com/embed/' . $url['v'] . '?ecver=1',
            'width' => '100%',
            'height' => '500',
            'frameborder' => 0,
            'allowfullscreen' => true
        ];
        return Html::tag('iframe', '', ArrayHelper::merge($default, $options));
    }

    public function getDoneAt()
    {
        if ($this->userDone) {
            return Yii::$app->formatter->asDatetime($this->userDone->updated_at, 'dd.MM.yy в HH:mm');
        }
        return null;
    }

    public function canGodPermission()
    {
        return Yii::$app->user->can('admin');
    }
}
