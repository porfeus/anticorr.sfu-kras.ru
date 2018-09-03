<?php

use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\assets\CourseAsset;
use app\modules\api\models\Module;
use app\modules\api\models\Theme;
use app\modules\api\models\Question;
use app\modules\api\models\Answer;
use app\modules\api\assets\TinymceUploadHendlersAsset;
use dosamigos\tinymce\TinyMceAsset;
use dosamigos\tinymce\TinyMceLangAsset;
/* @var $this yii\web\View */

CourseAsset::register($this);
TinymceUploadHendlersAsset::register($this);
TinyMceAsset::register($this);
$lang = Yii::$app->language;
$langFile = "langs/{$lang}.js";
$langAssetBundle = TinyMceLangAsset::register($this);
$langAssetBundle->js[] = $langFile;

$config = [
    'tinymce' => [
        'language_url' => $langAssetBundle->baseUrl . "/{$langFile}",
    ],
    'api' => [
        'url' => [
            'module' => Url::to(['/api/modules']),
            'theme' => Url::to(['/api/themes']),
            'file' => Url::to(['/api/files']),
            'qa' => Url::to(['/api/qa']),
        ],
        'csrfToken' => Yii::$app->request->csrfToken
    ],
    'module' => [
        'emptyModel' => new Module(),
    ],
    'theme' => [
        'emptyModel' => new Theme(),
    ],
    'qa' => [
        'question' => ArrayHelper::merge(
            ArrayHelper::toArray(new Question()),
            [
                'answers' => [new Answer(), new Answer()],
            ]
        ),
        'answer' => new Answer(),
    ],
];
$configJson = Json::encode($config);
$this->registerJs("initCourse('#course', $configJson)");

?>

<div id="course">
    <div class="row">
        <div class="col-md-4">
            <draggable class="list-group list-group-root well" v-model="module.list" :options="{group:'modules', draggable:'.item', handle:'.item-title'}" @update="moduleSort">
                <div v-for="(model, index) in module.list" class="list-group-item-wrapper item">
                    <div class="list-group-item list-group-item-info">
                        <!--<span class="drag-handle glyphicon glyphicon-align-justify text-muted" aria-hidden="true"></span>-->
                        <div class="pull-left">
                            <i v-if="!moduleIsCollapsed(model.id)" class="glyphicon glyphicon-chevron-right drag-handle"></i>
                            <i v-if="moduleIsCollapsed(model.id)" class="glyphicon glyphicon-chevron-down drag-handle"></i>
                        </div>
                        <div class="pull-right dropdown-controls">
                            <div class="dropdown">
                                <button class="btn btn-info btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a @click="qaModalUpdate(model)" href="#"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Тесты</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a @click="moduleModalUpdate(index)" href="#"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Изменить</a></li>
                                    <li><a @click="moduleModalDelete(index)" href="#"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Удалить</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="item-title" @click="moduleCollapseToggle(model.id)">{{ model.fullTitle }}</div>
                    </div>
                    <draggable class="list-group" :class="{hidden: !moduleIsCollapsed(model.id)}" v-model="model.themes" :options="{group:'themes_' + model.id, draggable:'.sub-item', handle:'.item-title'}" @update="themeSort(model.themes)">
                        <div v-for="(theme, i) in model.themes" class="list-group-item-wrapper2 sub-item">
                            <div class="list-group-item list-group-item-warning">
                                <div class="pull-left">
                                    <i class="glyphicon glyphicon-record drag-handle"></i>
                                </div>
                                <div class="pull-right dropdown-controls">
                                    <div class="dropdown">
                                        <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                            <li><a @click="qaModalUpdate(theme)" href="#"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Тесты</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a @click="themeModalUpdate(theme)" href="#"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Изменить</a></li>
                                            <li><a @click="themeModalDelete(theme)" href="#"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Удалить</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="item-title" @click="themeCollapseToggle(theme.id)">{{ theme.title }}</div>
                            </div>
                            <draggable class="list-group" :class="{hidden: !themeIsCollapsed(theme.id)}" v-model="theme.themes" :options="{group:'sub_  themes_' + model.id, draggable:'.sub-item2', handle:'.item-title'}" @update="themeSort(theme.themes)">
                                <div v-for="(subTheme, subi) in theme.themes" class="sub-item2">
                                    <div class="list-group-item">
                                        <div class="pull-left">
                                            <i class="glyphicon glyphicon-asterisk drag-handle"></i>
                                        </div>
                                        <div class="pull-right dropdown-controls">
                                            <div class="dropdown">
                                                <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                    <li><a @click="qaModalUpdate(subTheme)" href="#"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Тесты</a></li>
                                                    <li role="separator" class="divider"></li>
                                                    <li><a @click="themeModalUpdate(subTheme)" href="#"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Изменить</a></li>
                                                    <li><a @click="themeModalDelete(subTheme)" href="#"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Удалить</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-title">{{ subTheme.title }}</div>
                                    </div>
                                </div>
                                <button slot="footer" @click="themeModalCreate(index, theme, i)" type="button" class="list-group-item"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Добавить сабтему</button>
                            </draggable>
                        </div>
                        <button slot="footer" @click="themeModalCreate(index)" type="button" class="list-group-item list-group-item-warning"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Добавить тему</button>
                    </draggable>
                </div>
                <button slot="footer" @click="moduleModalCreate" type="button" class="list-group-item list-group-item-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить модуль</button>
            </draggable>
        </div>
    </div>
    <div id="modal-module" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Модуль</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group" :class="{'has-error': moduleHasError('title')}">
                            <label for="module-title">Название</label>
                            <input v-model="module.model.title" type="text" class="form-control" id="module-title" autocomplete="off">
                            <span v-if="moduleHasError('title')" class="help-block">{{ moduleGetError('title') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': moduleHasError('author')}">
                            <label for="module-author">Автор(ы)</label>
                            <input v-model="module.model.author" type="text" class="form-control" id="module-author" autocomplete="off">
                            <span v-if="moduleHasError('author')" class="help-block">{{ moduleGetError('author') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': moduleHasError('button_name')}">
                            <label for="module-author">Название кнопки</label>
                            <input v-model="module.model.button_name" type="text" class="form-control" id="module-button_name" autocomplete="off">
                            <span v-if="moduleHasError('button_name')" class="help-block">{{ moduleGetError('author') }}</span>
                        </div>
                        <div>
                            <label for="module-files-attach">Дополнительные файлы</label>
                            <div v-for="(fileAttached, fileIndex) in module.model.filesAttached" class="row">
                                <div class="col-md-10">
                                    <div class="form-group" :class="{'has-error': moduleHasError('filesAttached-' + fileAttached.unique + '-name')}">
                                        <input v-model="fileAttached.name" type="text" class="form-control">
                                        <span v-if="moduleHasError('filesAttached-' + fileAttached.unique + '-name')" class="help-block">{{moduleGetError('filesAttached-' + fileAttached.unique + '-name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button @click="moduleFileModalDelete('filesAttached', fileIndex)" type="button" class="btn btn-danger btn-block">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="btn btn-info btn-sm">
                                    Загрузить файлы<input @change="moduleFileInputChange($event, $event.target.files, 'filesAttached')" type="file" id="module-files-attach" multiple style="display: none;">
                                </label>
                            </div>
                            <div v-if="filesIsUploading" class="progress">
                                <div :style="{width: fileUploadProgressPct + '%'}" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                    <span>{{ fileUploadProgressPct }}%</span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <!--<div class="form-group" :class="{'has-error': moduleHasError('test_title')}">
                            <label for="module-test_title">Заголовок теста</label>
                            <input v-model="module.model.test_title" rows="9" class="form-control" id="module-test_title">
                            <span v-if="moduleHasError('test_title')" class="help-block">{{ moduleGetError('test_title') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': moduleHasError('test_comment_after')}">
                            <label for="module-test_comment_after">1Комментарий после теста</label>
                            <textarea v-model="module.model.test_comment_after" rows="9" class="form-control" id="module-test_comment_after"></textarea>
                            <span v-if="moduleHasError('test_comment_after')" class="help-block">{{ moduleGetError('test_comment_after') }}</span>
                        </div>-->
                    </form>
                </div>
                <div class="modal-footer">
                    <button @click="modalHide('module')" type="button" class="btn btn-default">Отмена</button>
                    <button v-if="isNewModule" @click="moduleModalSubmit" type="button" class="btn btn-success">Добавить</button>
                    <button v-if="!isNewModule" @click="moduleModalSubmit" type="button" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-theme" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Тема</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group" :class="{'has-error': themeHasError('title')}">
                            <label for="theme-title">Название темы</label>
                            <input v-model="theme.model.title" type="text" class="form-control" id="theme-title">
                            <span v-if="themeHasError('title')" class="help-block">{{ themeGetError('title') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': themeHasError('button_name')}">
                            <label for="theme-button-name">Название кнопки</label>
                            <input v-model="theme.model.button_name" type="text" class="form-control" id="theme-button-name">
                            <span v-if="themeHasError('button_name')" class="help-block">{{ themeGetError('button_name') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': themeHasError('description')}">
                            <label for="theme-description">Текст по теме</label>
                            <!--<textarea v-model="theme.model.description" class="form-control" id="theme-description" rows="4"></textarea>-->
                            <tinymce v-model="theme.model.description" class="form-control" id="theme-description" rows="4"></tinymce>
                            <span v-if="themeHasError('description')" class="help-block">{{ themeGetError('description') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': themeHasError('video_url')}">
                            <label for="theme-video_url">Видео к теме</label>
                            <input v-model="theme.model.video_url" type="text" class="form-control" id="theme-video_url">
                            <span v-if="themeHasError('video_url')" class="help-block">{{ themeGetError('video_url') }}</span>
                        </div>
                        <div>
                            <label for="theme-files-attach">Дополнительные файлы</label>
                            <div v-for="(fileAttached, fileIndex) in theme.model.filesAttached" class="row">
                                <div class="col-md-10">
                                    <div class="form-group" :class="{'has-error': themeHasError('filesAttached-' + fileAttached.unique + '-name')}">
                                        <input v-model="fileAttached.name" type="text" class="form-control">
                                        <span v-if="themeHasError('filesAttached-' + fileAttached.unique + '-name')" class="help-block">{{ themeGetError('filesAttached-' + fileAttached.unique + '-name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button @click="themeFileModalDelete('filesAttached', fileIndex)" type="button" class="btn btn-danger btn-block">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="btn btn-info btn-sm">
                                    Загрузить файлы<input @change="themeFileInputChange($event, $event.target.files, 'filesAttached')" type="file" id="theme-files-attach" multiple style="display: none;">
                                </label>
                            </div>
                            <div v-if="filesIsUploading" class="progress">
                                <div :style="{width: fileUploadProgressPct + '%'}" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                    <span>{{ fileUploadProgressPct }}%</span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div>
                            <label for="theme-files-attach">Рекомендуемая литература</label>
                            <div v-for="(fileAttached, fileIndex) in theme.model.filesBook" class="row">
                                <div class="col-md-10">
                                    <div class="form-group" :class="{'has-error': themeHasError('filesBook-' + fileAttached.unique + '-name')}">
                                        <input v-model="fileAttached.name" type="text" class="form-control">
                                        <span v-if="themeHasError('filesBook-' + fileAttached.unique + '-name')" class="help-block">{{ themeGetError('filesBook-' + fileAttached.unique + '-name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button @click="themeFileModalDelete('filesBook', fileIndex)" type="button" class="btn btn-danger btn-block">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="btn btn-info btn-sm">
                                    Загрузить файлы<input @change="themeFileInputChange($event, $event.target.files, 'filesBook')" type="file" id="theme-files-attach" multiple style="display: none;">
                                </label>
                            </div>
                            <div v-if="filesIsUploading" class="progress">
                                <div :style="{width: fileUploadProgressPct + '%'}" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                    <span>{{ fileUploadProgressPct }}%</span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <!--<div class="form-group" :class="{'has-error': themeHasError('test_title')}">
                            <label for="theme-test_title">Заголовок теста</label>
                            <input v-model="theme.model.test_title" rows="9" class="form-control" id="theme-test_title">
                            <span v-if="themeHasError('test_title')" class="help-block">{{ themeGetError('test_title') }}</span>
                        </div>
                        <div class="form-group" :class="{'has-error': themeHasError('test_comment_after')}">
                            <label for="theme-test_comment_after">Комментарий после теста</label>

                             <textarea v-model="theme.model.test_comment_after" rows="9" class="form-control" id="theme-test_comment_after"></textarea>
                            <span v-if="themeHasError('test_comment_after')" class="help-block">{{ themeGetError('test_comment_after') }}</span>
                        </div>-->
                    </form>
                </div>
                <div class="modal-footer">
                    <button @click="modalHide('theme');" type="button" class="btn btn-default">Отмена</button>
                    <button v-if="isNewTheme" @click="themeModalSubmit" type="button" class="btn btn-success">Добавить тему</button>
                    <button v-if="!isNewTheme" @click="themeModalSubmit" type="button" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-qa" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Тестирование</h4>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group" :class="{'has-error': moduleHasError('button_title')}">
                                <label for="module-button_name">Название кнопки</label>
                                <input v-model="module.model.button_name" type="text" class="form-control" id="module-button_bame" autocomplete="off">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div>
                        <div v-for="(question, index) in qa.list" class="panel panel-default">
                            <div class="panel-body">


                                <label :for="'qa-title-' + index">Вопрос</label>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group" :class="{'has-error': qaHasError(index, 'title')}">
                                            <textarea v-model="question.title" class="form-control" id="'qa-title-' + index" placeholder="Вопрос" rows="4" style="resize: vertical;"></textarea>
                                            <span v-if="qaHasError(index, 'title')" class="help-block">{{ qaGetError(index, 'title') }}</span>
                                        </div>
                                        <div class="form-group" :class="{'has-error': qaHasError(index, 'comment_after')}">
                                            <label :for="'qa-comment_after-' + index">Комментарий после теста</label>
                                            <tinymce v-model="question.comment_after" value="66" class="form-control" :id = "'qa-comment_after-' + index"  rows="3">sdfgsdfg</tinymce>
                                            <input type="hidden"  v-model="question.comment_after" class="comment_after" />
                                            <!-- <textarea v-model="question.comment_after" class="form-control" :id="'qa-comment_after-' + index" placeholder="Комментарий после теста" rows="3" style="resize: vertical;"></textarea> -->
                                            <span v-if="qaHasError(index, 'comment_after')" class="help-block">{{ qaGetError(index, 'comment_after') }}</span>
                                        </div>
                                        <div class="form-group" :class="{'has-error': qaHasError(index, 'comment_after')}">
                                            <label for="file">Файлы к тесту</label>
                                            <p v-if="question.file">{{question.file}} <a href="#" @click = "qaApiDeleteFile(question)">Удалить файл</a> </p>
                                            <input type="file" @change = "qaApiUpdateFile(question,index)"  :id = "'qa-file-' + index" />
                                            <!-- <textarea v-model="question.comment_after" class="form-control" :id="'qa-comment_after-' + index" placeholder="Комментарий после теста" rows="3" style="resize: vertical;"></textarea> -->
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <button @click="qaQuestionDel(index)" type="button" class="btn btn-danger btn-block">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group" :class="{'has-error': qaHasError(index, 'answers')}">
                                    <label>Ответы</label>
                                    <span v-if="qaHasError(index, 'answers')" class="help-block">{{ qaGetError(index, 'answers') }}</span>
                                    <draggable v-model="question.answers" :options="{group:'answers_sort_' + question.id, draggable:'.answer-row', handle:'.answer-handle'}" @update="qaAnswerSort(question.answers)">
                                        <div v-for="(answer, i) in question.answers" class="row answer-row">
                                            <div class="col-md-10">
                                                <div class="form-group" :class="{'has-error': qaHasError(index, 'answers-' + i + '-title')}">
                                                    <div class="row">
                                                        <div class="col-md-1 answer-handle">
                                                            <button type="button" class="btn btn-link" style="padding: 12px 4px;border: none;">
                                                                <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                        <template v-if="answer.is_true >= 0">
                                                            <div class="col-md-1">
                                                                <input v-model="answer.is_true" :true-value="1" :false-value="0" :id="'answer_' + index + '-' + i" type="checkbox" aria-label="..." class="pseudo-checkbox sr-only">
                                                                <label :for="'answer_' + index + '-' + i" class='fancy-checkbox-label'></label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <textarea v-model="answer.title" class="form-control" :id="'qa-title-' + index + '-' + i" placeholder="Ответ" rows="2" style="resize: vertical;"></textarea>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="col-md-11">
                                                                <input v-model="answer.title" class="form-control" :id="'qa-title-' + index + '-' + i" placeholder="Разделитель" />
                                                            </div>
                                                        </template>
                                                    </div><!-- /input-group -->
                                                    <span v-if="qaHasError(index, 'answers-' + i + '-title')" class="help-block">{{ qaGetError(index, 'answers-' + i + '-title') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2" :class="{hidden: question.answers.length < 3 && answer.is_true != -1}">
                                                <button @click="qaAnswerDel(index, i)" type="button" class="btn btn-default btn-block">
                                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </draggable></div>
                                <div class="form-group">
                                    <button @click="qaAnswerAdd(question)" type="button" class="btn btn-default">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить ответ
                                    </button>
                                    <button @click="qaAnswerAdd(question, true)" type="button" class="btn btn-default">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить разделитель
                                    </button>
                                </div>
                            </div>
                        </div>


                    </div>
                    <button @click="qaQuestionAdd()" type="button" class="btn btn-info">Добавить вопрос</button>
                </div>
                <div class="modal-footer">
                    <button @click="modalHide('qa')" type="button" class="btn btn-default">Отмена</button>
                    <button @click="qaModalSubmit" type="button" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </div>
        </div>
    </div>
</div>
