"use strict";

function initCourse(el, config) {

    Vue.component('tinymce', {
        template: '<textarea :id="id"></textarea>',
        props: {
            id: {
                type: String,
                default: ''
            },
            value: {
                type: String,
                default: ''
            },
            toolbar: {
                default: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            },
            toolbar2: {
                default: 'print preview media | forecolor backcolor emoticons | codesample help'
            },
            menubar: {
                default: true
            },
            otherProps: {
                default: ''
            }
        },
        mounted: function () {
            var vm = this;
            var el = $(this.$el);
            // console.log(this.toolbar);
            tinymce.init({
                selector: '#' + this.id,
                language_url: config.tinymce.language_url,
                height: 150,
                autoresize_min_height: 150,
                toolbar1: this.toolbar,
                toolbar2: this.toolbar2,
                menubar: this.menubar,
                plugins: [
                    'advlist autolink lists link image charmap preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime nonbreaking table contextmenu',
                    'emoticons paste textcolor colorpicker textpattern imagetools codesample toc help autoresize'
                ],
                paste_data_images: true,
                init_instance_callback: function (editor) {
                    editor.on('keyup', function (e) {
                        vm.$emit('input', editor.getContent());
                    });
                    editor.on('change', function (e) {
                        vm.$emit('input', editor.getContent());
                    });
                },
                // enable title field in the Image dialog
                image_title: true,
                // enable automatic uploads of images represented by blob or data URIs
                automatic_uploads: true,
                // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
                images_upload_url: config.api.url.file,
                // here we add custom filepicker only to Image dialog
                file_picker_types: 'image',
                // and here's our custom image picker
                file_picker_callback: window.tinymce_file_picker_callback, // @see upload-handlers.js
                images_upload_handler: window.tinymce_images_upload_handler // @see upload-handlers.js
            });
            // console.log(t.activeEditor);
        },
        watch: {
            value: function (value) {

            }
        },
        destroyed: function () {
            tinymce.get(this.id).destroy();
        }
    });

    /**
     * tinymce modals input focus problem fix
     * @see solution https://stackoverflow.com/a/18209594
     */
    $(document).on('focusin', function (e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    });

    new Vue({
        el: el,
        data: {
            message: 'hello world',
            module: {
                api: undefined,
                list: [],
                collapsed: [],
                model: {},
                emptyModel: config.module.emptyModel,
                errors: {}
            },
            theme: {
                api: undefined,
                uploads: {
                    attach: [],
                    books: []
                },
                model: {},
                collapsed: [],
                emptyModel: config.theme.emptyModel,
                errors: {}
            },
            file: {
                api: undefined,
                upload: {
                    progress: {
                        show: false,
                        pct: 0,
                        total: 0,
                        count: 0
                    }
                }
            },
            qa: {
                question: config.qa.question,
                answer: config.qa.answer,
                api: undefined,
                apiParams: {
                    module_id: null,
                    theme_id: null,
                },
                list: [],
                errors: []
            },
            modals: {
                module: '#modal-module',
                theme: '#modal-theme',
                qa: '#modal-qa'
            }
        },
        computed: {
            isNewModule: function () {
                return !this.module.model.id;
            },
            isNewTheme: function () {
                return !this.theme.model.id;
            },
            fileUploadProgressPct: function () {
                var progress = this.file.upload.progress;
                return progress.count / progress.total * 100;
            },
            filesIsUploading: function () {
                var progress = this.file.upload.progress;
                return progress.count < progress.total;
            }
        },
        created: function () {
            var api = config.api;
            this.module.api = this.$resource(api.url.module + '{/id}', {expand: 'themes'}, {
                order: {method: 'PUT', url: api.url.module + '/order'}
            }, {
                headers: {
                    'X-CSRF-Token': api.csrfToken
                }
            });
            this.theme.api = this.$resource(api.url.theme + '{/id}', {expand: 'filesAttached'}, {
                order: {method: 'PUT', url: api.url.theme + '/order'}
            }, {
                headers: {
                    'X-CSRF-Token': api.csrfToken
                }
            });
            this.file.api = this.$resource(api.url.file + '{/id}', {}, {}, {
                headers: {
                    'X-CSRF-Token': api.csrfToken
                }
            });
            this.qa.api = this.$resource(api.url.qa, this.qa.apiParams, {}, {
                headers: {
                    'X-CSRF-Token': api.csrfToken
                }
            });
        },
        mounted: function () {
            this.moduleApiFetchAll();
        },
        methods: {
            modalAlertError: function (message, title, buttonText) {
                swal({
                    title: title ? title : "Ой!",
                    text: message ? message : "Произошла очень странная ошибка, даже не знаю что сказать по этому поводу.",
                    html: true,
                    type: "error",
                    confirmButtonText: buttonText ? buttonText : "Ок"
                });
            },
            modalAlertWarning: function (message, title, buttonText) {
                swal({
                    title: title ? title : "Ой!",
                    text: message ? message : "Произошла очень странная ошибка, даже не знаю что сказать по этому поводу.",
                    html: true,
                    type: "warning",
                    confirmButtonText: buttonText ? buttonText : "Ок"
                });
            },
            modalConfirmDelete: function (message, cb, title) {
                swal({
                    title: title ? title : "Вы уверены?",
                    text: message ? message : undefined,
                    type: "warning",
                    html: true,
                    allowOutsideClick: true,
                    showCancelButton: true,
                    cancelButtonText: 'Отмена',
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Да, удалить",
                    closeOnConfirm: true
                }, cb);
            },

            modalShow: function (modal) {
                var m = $(this.modals[modal]);
                m.modal('show');
            },
            modalHide: function (modal) {
                var m = $(this.modals[modal]);
                m.modal('hide');
            },

            // module error methods
            moduleHasError: function (attribute) {
                return this.moduleGetError(attribute) ? true : false;
            },
            moduleGetError: function (attribute) {
                var errors = this.module.errors;
                for (var i = 0; i < errors.length; i++) {
                    if (errors[i].field == attribute) {
                        return errors[i].message;
                    }
                }
                return null;
            },
            moduleClearErrors: function () {
                Vue.set(this.module, 'errors', {});
            },
            moduleApiResponseErrorCallback: function (response, model, index) {
                var _t = this;
                switch (response.status) {
                    case 422:
                        Vue.set(_t.module, 'errors', response.body);
                        break;
                    case 404:
                        _t.moduleItemDelete(index);
                        _t.modalHide('module');
                        _t.modalAlertWarning('Модуль <b>' + model.title + '</b> отсутствует в системе, возможно ранее он был удален!');
                        break;
                    default:
                        _t.modalAlertError();
                }
            },

            // module api methods
            moduleApiFetchAll: function () {
                var _t = this;
                this.module.api.get().then(function (response) {
                    _t.module.list = response.body;
                });
            },
            moduleApiCreate: function () {
                var _t = this;
                var model = _t.moduleGetModel();
                this.module.api.save({}, model).then(function (response) {
                    _t.modalHide('module');
                    _t.moduleItemCreate(response.body);
                }, _t.moduleApiResponseErrorCallback);
            },
            moduleApiUpdate: function () {
                var _t = this;
                var model = _t.moduleGetModel();
                var index = _t.moduleItemGetIndexById(model.id);
                this.module.api.update({id: model.id}, model).then(function (response) {
                    _t.modalHide('module');
                    _t.moduleItemUpdate(index, response.body);
                }, function (response) {
                    _t.moduleApiResponseErrorCallback(response, model, index);
                });
            },
            moduleApiDelete: function (index) {
                var _t = this;
                var model = _t.moduleItemGetByIndex(index);
                this.module.api.delete({id: model.id}).then(function (response) {
                    if (_t.moduleIsCollapsed(model.id)) {
                        _t.moduleCollapseToggle(model.id)
                    }
                    _t.moduleItemDelete(index);
                }, function (response) {
                    _t.moduleApiResponseErrorCallback(response, model, index);
                });
            },

            // module help methods
            moduleGetModel: function () {
                return this.module.model;
            },
            moduleItemGetByIndex: function (index) {
                var list = this.module.list;
                if (typeof list[index] !== 'undefined') {
                    return list[index];
                }
                return null;
            },
            moduleItemGetById: function (id) {
                var list = this.module.list;
                for (var i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        return list[i];
                    }
                }
                return null;
            },
            moduleItemGetIndexById: function (id) {
                var list = this.module.list;
                for (var i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        return i;
                    }
                }
                return null;
            },
            moduleItemCreate: function (data) {
                var len = this.module.list.push(data);
                var index = len - 1;
                this.moduleCollapseToggle(index);
            },
            moduleItemUpdate: function (index, data) {
                Vue.set(this.module.list, index, data);
            },
            moduleItemDelete: function (index) {
                this.module.list.splice(index, 1);
            },

            // module modals methods
            moduleModalCreate: function () {
                var _t = this;
                _t.moduleClearErrors();
                _t.modalShow('module');
                var list = _t.module.list;
                var sort = 0;
                if (list.length > 0) {
                    sort = list[list.length - 1].sort + 1;
                }
                Vue.set(_t.module, 'model', _.cloneDeep(_t.module.emptyModel));
                this.module.model.sort = sort;
            },
            moduleModalUpdate: function (index) {
                var _t = this;
                var model = _t.moduleItemGetByIndex(index);
                _t.moduleClearErrors();
                _t.modalShow('module');
                Vue.set(_t.module, 'model', _.cloneDeep(model, true));
            },
            moduleModalDelete: function (index) {
                var _t = this;
                var model = _t.moduleItemGetByIndex(index);
                _t.modalConfirmDelete('Вы действительно хотите удалить модуль <b>' + model.title + '</b>?', function () {
                    _t.moduleApiDelete(index);
                });
            },
            moduleModalSubmit: function () {
                if (this.isNewModule) {
                    this.moduleApiCreate();
                } else {
                    this.moduleApiUpdate();
                }
            },

            // module collapse help methods
            moduleCollapseToggle: function (id) {
                var list = this.module.collapsed;
                if (!this.moduleIsCollapsed(id)) {
                    list.push(id);
                } else {
                    var position = list.indexOf(id);
                    if (position !== -1) {
                        list.splice(position, 1);
                    }
                }
            },
            moduleIsCollapsed: function (id) {
                return this.module.collapsed.indexOf(id) !== -1;
            },
            themeCollapseToggle: function (id) {
                var list = this.theme.collapsed;
                if (!this.themeIsCollapsed(id)) {
                    list.push(id);
                } else {
                    var position = list.indexOf(id);
                    if (position !== -1) {
                        list.splice(position, 1);
                    }
                }
            },
            themeIsCollapsed: function (id) {
                return this.theme.collapsed.indexOf(id) !== -1;
            },

            // module set order
            moduleSort: function (e) {
                var _t = this;
                _t.module.api.order(_.reduce(_t.module.list, function (result, model, key) {
                    result.push({id: model.id});
                    return result;
                }, []));
            },

            // module files event
            moduleFileInputChange: function (event, files, attribute) {
                if (!files.length) return;
                var progress = this.file.upload.progress;
                progress.total = files.length;
                progress.count = 0;
                var module = this.module.model;
                if (typeof module[attribute] === 'undefined') {
                    Vue.set(module, attribute, []);
                }
                for (var i = 0; i < files.length; i++) {
                    this.moduleFileApiUpload(files[i], attribute);
                }
            },
            // module help methods
            moduleFileGenerateRandomUnique: function () {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (var i = 0; i < 64; i++) {
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                }
                return text;
            },
            // module file api
            moduleFileApiUpload: function (file, attribute) {
                var _t = this;
                var progress = _t.file.upload.progress;
                var list = _t.module.model[attribute];
                var formData = new FormData();
                formData.append('file', file);
                formData.append('name', file.name);
                _t.file.api.save({}, formData).then(function (response) {
                    progress.count++;
                    var file = response.body;
                    list.push({
                        unique: _t.moduleFileGenerateRandomUnique(),
                        file_id: file.id,
                        name: file.name
                    });
                }, function (response) {
                    progress.count++;
                })
            },
            // module modal methods
            moduleFileModalDelete: function (attribute, index) {
                // maybe modal???
                return this.module.model[attribute].splice(index, 1);
            },


            // moduleGetItemById: function (id) {
            //   var list = this.module.list;
            //   for (var index in list) {
            //     if (list[index].id == id) {
            //       return list[index];
            //     }
            //   }
            //   return false;
            // },


            // theme error methods
            themeHasError: function (attribute) {
                return this.themeGetError(attribute) ? true : false;
            },
            themeGetError: function (attribute) {
                var errors = this.theme.errors;
                for (var i = 0; i < errors.length; i++) {
                    if (errors[i].field == attribute) {
                        return errors[i].message;
                    }
                }
                return null;
            },
            themeClearErrors: function () {
                Vue.set(this.theme, 'errors', {});
            },
            themeApiResponseErrorCallback: function (response, model, index) {
                var _t = this;
                switch (response.status) {
                    case 422:
                        Vue.set(_t.theme, 'errors', response.body);
                        break;
                    case 404:
                        // _t.moduleItemDelete(index);
                        _t.modalHide('theme');
                        _t.modalAlertWarning('Тема <b>' + model.title + '</b> отсутствует в системе, возможно ранее она была удалена!');
                        break;
                    default:
                        _t.modalAlertError();
                }
            },

            // theme api methods
            themeApiCreate: function () {
                var _t = this;
                var theme = _t.themeGetModel();
                this.theme.api.save({}, theme).then(function (response) {
                    _t.modalHide('theme');
                    _t.themeItemCreate(response.body)
                }, _t.themeApiResponseErrorCallback);
            },
            themeApiUpdate: function () {
                var _t = this;
                var theme = _t.theme.model;
                this.theme.api.update({id: theme.id}, theme).then(function (response) {
                    _t.modalHide('theme');
                    _t.themeItemUpdate(response.body);
                }, function (response) {
                    _t.themeApiResponseErrorCallback(response, theme);
                });
            },
            themeApiDelete: function (theme) {
                var _t = this;
                this.theme.api.delete({id: theme.id}).then(function (response) {
                    if (_t.themeIsCollapsed(theme.id)) {
                        _t.themeCollapseToggle(theme.id)
                    }
                    _t.themeItemDelete(theme);
                }, function (response) {
                    _t.themeApiResponseErrorCallback(response, theme);
                });
            },

            // theme help methods
            themeGetModel: function () {
                return this.theme.model;
            },
            themeItemCreate: function (theme) {
                var module = this.moduleItemGetById(theme.module_id);
                if (module) {
                    if (theme.parent_id) {
                        var parent = this.themeItemGetByIdAndModuleId(theme.parent_id, theme.module_id);
                        if (typeof parent.themes != 'undefined') {
                            parent.themes.push(theme);
                        } else {
                            Vue.set(parent, 'themes', [theme]);
                        }
                    } else {
                        if (typeof module.themes != 'undefined') {
                            module.themes.push(theme);
                        } else {
                            Vue.set(module, 'themes', [theme]);
                        }
                    }

                    return true;
                }
                return false;
            },
            themeItemUpdate: function (theme) {
                var module = this.moduleItemGetById(theme.module_id);
                if (module) {
                    var themes = module.themes;
                    if (theme.parent_id) {
                        var parent = this.themeItemGetByIdAndModuleId(theme.parent_id, theme.module_id);
                        themes = parent.themes;
                    }
                    for (var i = 0; i < themes.length; i++) {
                        if (themes[i].id == theme.id) {
                            Vue.set(themes, i, theme);
                            return true;
                        }
                    }
                }
                return false;
            },
            themeItemDelete: function (theme) {
                var module = this.moduleItemGetById(theme.module_id);
                if (module) {
                    var themes = module.themes;
                    if (theme.parent_id) {
                        var parent = this.themeItemGetByIdAndModuleId(theme.parent_id, theme.module_id);
                        themes = parent.themes;
                    }
                    for (var i = 0; i < themes.length; i++) {
                        if (themes[i].id == theme.id) {
                            themes.splice(i, 1);
                            return true;
                        }
                    }
                }
                return false;
            },
            themeItemGetByIdAndModuleId: function (theme_id, module_id) {
                if (typeof module_id !== 'undefined') {
                    var module = this.moduleItemGetById(module_id);
                    var list = module.themes;
                    for (var i = 0; i < list.length; i++) {
                        if (list[i].id === theme_id) {
                            return list[i];
                        }
                    }
                    return null;
                }
            },

            // theme modals methods
            themeModalCreate: function (moduleIndex, themeModel, themeIndex) {
                var _t = this;
                var module = _t.moduleItemGetByIndex(moduleIndex);
                _t.themeClearErrors();
                _t.modalShow('theme');
                Vue.set(_t.theme, 'model', _.cloneDeep(_t.theme.emptyModel));
                var model = _t.themeGetModel();
                model.sort = 0;
                model.module_id = module.id;
                _t.themeTinymceSetContent();
                if (typeof themeModel !== 'undefined') {
                    model.parent_id = themeModel.id;
                    if (themeModel.themes !== 'undefined' && themeModel.themes.length > 0) {
                        model.sort = themeModel.themes[themeModel.themes.length - 1].sort + 1;
                    }
                } else {
                    if (module.themes !== 'undefined' && module.themes.length > 0) {
                        model.sort = module.themes[module.themes.length - 1].sort + 1;
                    }
                }

            },
            themeModalUpdate: function (theme) {
                var _t = this;
                _t.themeClearErrors();
                _t.modalShow('theme');
                Vue.set(_t.theme, 'model', _.cloneDeep(theme));
                _t.themeTinymceSetContent();
            },
            themeTinymceSetContent: function () {
                var description = this.theme.model.description;
                tinymce.activeEditor.setContent((description ? description : ''));
            },
            themeModalDelete: function (theme) {
                var _t = this;
                _t.modalConfirmDelete('Вы действительно хотите удалить тему <b>' + theme.title + '</b>?', function () {
                    _t.themeApiDelete(theme);
                });
            },
            themeModalSubmit: function () {
                var _t = this;
                if (_t.isNewTheme) {
                    _t.themeApiCreate();
                } else {
                    _t.themeApiUpdate();
                }
            },
            // theme order
            themeSort: function (models) {
                var _t = this;
                _t.theme.api.order(_.reduce(models, function (result, model, key) {
                    result.push({id: model.id});
                    return result;
                }, []));
            },

            // ??
            themeGetItemByModuleIdAndItemId: function (module_id, item_id) {
                var module = this.moduleGetItemById(module_id);
                if (module) {
                    for (var i in module.themes) {
                        if (module.themes[i].id == item_id) {
                            return module.themes[i];
                        }
                    }
                }
            },


            // theme files event
            themeFileInputChange: function (event, files, attribute) {
                if (!files.length) return;
                var progress = this.file.upload.progress;
                progress.total = files.length;
                progress.count = 0;
                var theme = this.theme.model;
                if (typeof theme[attribute] === 'undefined') {
                    Vue.set(theme, attribute, []);
                }
                for (var i = 0; i < files.length; i++) {
                    this.themeFileApiUpload(files[i], attribute);
                }
            },
            // theme help methods
            themeFileGenerateRandomUnique: function () {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (var i = 0; i < 64; i++) {
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                }
                return text;
            },
            // theme file api
            themeFileApiUpload: function (file, attribute) {
                var _t = this;
                var progress = _t.file.upload.progress;
                var list = _t.theme.model[attribute];
                var formData = new FormData();
                formData.append('file', file);
                formData.append('name', file.name);
                _t.file.api.save({}, formData).then(function (response) {
                    progress.count++;
                    var file = response.body;
                    list.push({
                        unique: _t.themeFileGenerateRandomUnique(),
                        file_id: file.id,
                        name: file.name
                    });
                }, function (response) {
                    progress.count++;
                })
            },
            // theme modal methods
            themeFileModalDelete: function (attribute, index) {
                // maybe modal???
                return this.theme.model[attribute].splice(index, 1);
            },


            // qa errors methods
            qaHasError: function (index, attribute) {
                return this.qaGetError(index, attribute) ? true : false;
            },
            qaGetError: function (index, attribute) {
                var errors = this.qa.errors[index];
                if (typeof errors === 'undefined') {
                    return false;
                }
                if (typeof errors[attribute] !== 'undefined') {
                    return errors[attribute];
                }
                return false;
            },
            qaApiResponseErrorCallback: function (response) {
                var _t = this;
                switch (response.status) {
                    case 422:
                        Vue.set(_t.qa, 'errors', response.body);
                        break;
                    case 404:
                        _t.modalHide('qa');
                        _t.modalAlertWarning(response.body.message);
                        break;
                    default:
                        _t.modalAlertError();
                }
            },

            // qa api methods
            qaApiFetchAll: function () {
                var _t = this;
                var qa = _t.qa;
                qa.api.get(qa.apiParams).then(function (response) {
                    this.modalShow('qa');
                    Vue.set(_t.qa, 'list', response.body);
                }, _t.qaApiResponseErrorCallback);
            },
            qaApiUpdate: function () {
                var _t = this;
                var qa = _t.qa;
                qa.errors = {};
                qa.api.update(qa.apiParams, qa.list).then(function (response) {
                    this.modalHide('qa');
                }, _t.qaApiResponseErrorCallback)
            },
            qaApiUpdateFile: function () {

                var xhr = new XMLHttpRequest();
                var _t = this;
                var qa = _t.qa;
                // обработчик для закачки
                xhr.upload.onprogress = function (event) {
                    console.log(event.loaded + ' / ' + event.total);
                }
                var file = document.querySelector("#file").files[0];
                var fd = new FormData();
                fd.append("testfile", file);
                fd.append("module_id", qa.apiParams.module_id);
                fd.append("theme_id", qa.apiParams.theme_id);
                // обработчики успеха и ошибки
                // если status == 200, то это успех, иначе ошибка
                xhr.onload = xhr.onerror = function () {
                    if (this.status == 200) {
                        console.log("success");
                    } else {
                        console.log("error " + this.status);
                    }
                };

                xhr.open("POST", "/api/qa/uploadFile", true);
                xhr.send(fd);
            },
            qaApiDeleteFile: function () {
                var xhr = new XMLHttpRequest();
                var _t = this;
                var qa = _t.qa;
                var fd = new FormData();
                fd.append("module_id", qa.apiParams.module_id);
                fd.append("theme_id", qa.apiParams.theme_id);
                fd.append("delete_file", true);
                // обработчики успеха и ошибки
                // если status == 200, то это успех, иначе ошибка
                xhr.onload = xhr.onerror = function () {
                    if (this.status == 200) {
                        console.log("success");
                        qa.list.question.file = '';
                    } else {
                        console.log("error " + this.status);
                    }
                };

                xhr.open("POST", "/api/qa/uploadFile", true);
                xhr.send(fd);
            },
            // qa modal methods
            qaModalUpdate: function (model) {
                var qa = this.qa;
                if (typeof model.module_id !== 'undefined') {
                    qa.apiParams.module_id = model.module_id;
                    qa.apiParams.theme_id = model.id;
                } else {
                    qa.apiParams.module_id = model.id;
                    qa.apiParams.theme_id = null;
                }
                this.qaApiFetchAll();
            },
            qaModalSubmit: function () {
                this.qaApiUpdate();
            },


            // qa controls add/del methods
            qaQuestionAdd: function () {
                var qa = this.qa;
                qa.list.push(
                    _.cloneDeep(qa.question, true)
                )
            },
            qaQuestionDel: function (index) {
                this.qa.list.splice(index, 1);
            },
            qaAnswerAdd: function (question, isDelimiter) {
                var qa = _.clone(this.qa.answer, true);
                if (isDelimiter === true) {
                    qa.is_true = -1;
                }
                question.answers.push(qa);
                this.qaAnswerSort(question.answers);
            },
            qaAnswerDel: function (questionIndex, answerIndex) {
                this.qa.list[questionIndex].answers.splice(answerIndex, 1);
            },
            qaAnswerSort: function (models) {
                for (var sort = 0; sort < models.length; sort++) {
                    models[sort].sort = sort;
                }
            }
        }
    });
}

/* [bug/fix] multiple modals backdrop */
$(document).on('hidden.bs.modal', '.modal', function () {
    if ($('.modal:visible').length > 0) {
        $('body').addClass('modal-open');
    }
})


window.onload = function () {
    $('#modal-qa').on('shown.bs.modal', function () {
        $(document).off('focusin.modal');
        tinymce.get('qa-comment_after').setContent($("#comment_after").val());
    });
};

