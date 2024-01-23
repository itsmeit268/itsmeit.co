var RB_ADMIN_CORE = (function (Module, $) {
    'use strict';

    Module.importProcess = false;
    Module.isImported = false;
    Module.eFlag = false;
    Module.ajaxProcess = false;
    Module.pluginProcess = false;
    Module.init = function () {

        this.registerForm = $('#rb-register-theme-form');
        this.deregisterForm = $('#rb-deregister-theme-form');
        this.globalConfigs = JSON.parse(JSON.stringify(foxizAdminCore));
        this.registerPurchase();
        this.deregisterPurchase();
        this.activePlugin();
        this.installPackaged();
        this.selectData();
        this.installDemo();
        this.fetchTranslation();
        this.updateTranslation();
        this.fetchImporter();

        /** fonts */
        this.editProjectID();
    };

    /** register */
    Module.registerPurchase = function () {
        var self = this;
        if (self.registerForm.length) {
            var submitBtn = self.registerForm.find('#rb-register-theme-btn');
            var loading = self.registerForm.find('.rb-loading');
            var messenger = self.registerForm.find('.rb-response-info');
            submitBtn.on('click', function (e) {

                e.preventDefault();
                e.stopPropagation();

                if (self.ajaxProcess === true) {
                    return;
                }

                var data = self.getFormData($(self.registerForm));
                if (!data.nonce || !data.purchaseCode || !data.emailInfo) {
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    async: true,
                    dataType: 'json',
                    url: self.globalConfigs.ajaxUrl,
                    data: {
                        action: 'rb_register_theme',
                        purchase_code: data.purchaseCode,
                        email: data.emailInfo,
                        _nonce: data.nonce
                    },
                    beforeSend: function (xhr) {
                        self.ajaxProccess = true;
                        loading.fadeIn(300).removeClass('is-hidden');
                        submitBtn.attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if ('undefined' != typeof response.data) {
                            loading.fadeOut(300).addClass('is-hidden');
                            messenger.html('<p class="info-success">' + response.data + '</p>').removeClass('is-hidden');
                        }
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    },
                    error: function (response) {
                        response = JSON.parse(JSON.stringify(response.responseJSON));
                        if ('undefined' != typeof response.data) {
                            loading.fadeOut(300).addClass('is-hidden');
                            messenger.html(response.data).removeClass('is-hidden');
                        }
                    }
                });
                return false;
            })
        }
    };

    /** deregisterPurchase */
    Module.deregisterPurchase = function () {
        var self = this;
        if (self.deregisterForm.length) {
            var submitBtn = self.deregisterForm.find('#rb-deregister-theme-btn');
            var loading = self.deregisterForm.find('.rb-loading');
            var messenger = self.deregisterForm.find('.rb-response-info');

            submitBtn.on('click', function (e) {

                e.preventDefault();
                e.stopPropagation();

                var confirm = window.confirm('Are you sure you want to deactivate this theme?');
                if (confirm === false) {
                    return;
                }

                if (self.ajaxProcess === true) {
                    return;
                }

                var data = self.getFormData($(self.deregisterForm));
                if (!data.nonce) {
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    async: true,
                    dataType: 'json',
                    url: self.globalConfigs.ajaxUrl,
                    data: {
                        action: 'rb_deregister_theme',
                        _nonce: data.nonce
                    },
                    beforeSend: function (xhr) {
                        self.ajaxProccess = true;
                        loading.fadeIn(300).removeClass('is-hidden');
                        submitBtn.attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        response = JSON.parse(JSON.stringify(response));
                        if ('undefined' != typeof response.data) {
                            loading.fadeOut(300).addClass('is-hidden');
                            messenger.html('<p class="info-success">' + response.data + '</p>').removeClass('is-hidden');
                        }
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    },
                    error: function (response) {
                        response = JSON.parse(JSON.stringify(response.responseJSON));
                        if ('undefined' != typeof response.data) {
                            loading.fadeOut(300).addClass('is-hidden');
                            messenger.html(response.data).removeClass('is-hidden');
                        }
                    }
                });
                return false;
            })
        }
    };

    /** get form data */
    Module.getFormData = function (form) {

        var data = {};

        var purchaseCodeInput = form.find('[name="purchase_code"]');
        var emailInfoInput = form.find('[name="email"]');
        var nonceInput = form.find('[name="rb-core-nonce"]');

        data.purchaseCode = purchaseCodeInput.val();
        data.emailInfo = emailInfoInput.val();
        data.nonce = nonceInput.val();

        /** validate */
        if ('' !== data.purchaseCode) {
            purchaseCodeInput.removeClass('rb-validate-error');
            purchaseCodeInput.parent().find('.rb-error-info').addClass('is-hidden');
        } else {
            purchaseCodeInput.addClass('rb-validate-error');
            purchaseCodeInput.parent().find('.rb-error-info').removeClass('is-hidden');
        }

        if ('' !== data.emailInfo) {
            emailInfoInput.removeClass('rb-validate-error');
            emailInfoInput.parent().find('.rb-error-info').addClass('is-hidden');
        } else {
            emailInfoInput.addClass('rb-validate-error');
            emailInfoInput.parent().find('.rb-error-info').removeClass('is-hidden');
        }

        return data;
    };

    /** Ruby Importer */
    Module.activePlugin = function () {
        var self = this;
        $('.rb-demos .rb-activate-plugin').unbind('click').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var target = $(this);
            if (self.pluginProcess && target.hasClass('is-install')) {
                return false;
            }
            var url = target.attr('href');
            target.addClass('loading');
            if (target.hasClass('is-install')) {
                self.pluginProcess = true;
                target.html('<span class="rb-loading-info"><span class="rb-loading"><i class="dashicons dashicons-update"></i></span><span>Installing...</span></span>');
            } else {
                target.html('<span class="rb-loading-info"><span class="rb-loading"><i class="dashicons dashicons-update"></i></span><span>Activating...</span></span>');
            }
            jQuery.ajax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.length > 0 && (response.match(/Plugin activated./gi))) {
                        target.find('.spinner').remove();
                        target.replaceWith('<span class="activate-info activated">Activated</span>');
                    } else {
                        window.onbeforeunload = null;
                        location.reload(true);
                    }
                }
            });
        });
    };

    Module.selectData = function () {
        var self = this;
        var rbDemos = $('.rb-demo-item');
        if (rbDemos.length > 0) {
            rbDemos.each(function () {
                self.importerBtnStatus($(this));
            });
        }

        $('.rb-importer-checkbox').unbind('click').on('click', function (e) {

            e.preventDefault();
            e.stopPropagation();

            var checkbox = jQuery(this);
            if (checkbox.data('checked') == 1) {
                checkbox.removeClass('checked');
                checkbox.data('checked', 0);
            } else {
                checkbox.addClass('checked');
                checkbox.data('checked', 1);
            }

            var outer = checkbox.parents('.demo-content');
            var name = checkbox.data('title');
            var wrap = checkbox.parents('.data-select');
            if (checkbox.data("checked") && 'rb_import_all' == name) {
                wrap.find('.rb_import_content').data("checked", 1).addClass('checked');
                wrap.find('.rb_import_pages').data("checked", 1).addClass('checked');
                wrap.find('.rb_import_tops').data("checked", 1).addClass('checked');
                wrap.find('.rb_import_widgets').data("checked", 1).addClass('checked');
            }
            if (!checkbox.data("checked") && 'rb_import_all' != name) {
                wrap.find('.rb_import_all').data("checked", 0).removeClass('checked');
            }
            if (checkbox.data("checked") && 'rb_import_pages' == name) {
                wrap.find('.rb_import_content').data("checked", 0).removeClass('checked');
            }

            self.importerBtnStatus(outer);
        });
    };

    /** importer button */
    Module.importerBtnStatus = function (wrapper) {
        var importAll = wrapper.find('.rb_import_all').data('checked');
        var importContent = wrapper.find('.rb_import_content').data('checked');
        var importPages = wrapper.find('.rb_import_pages').data('checked');
        var importTops = wrapper.find('.rb_import_tops').data('checked');
        var importWidgets = wrapper.find('.rb_import_widgets').data('checked');

        if (importAll || importContent || importPages || importTops || importWidgets) {
            wrapper.find('.rb-disabled').removeClass('rb-disabled');
            return true;
        }
        wrapper.find('.rb-importer-btn').addClass('rb-disabled');
        return false;
    };

    /** install package */
    Module.installPackaged = function () {

        var self = this;
        $('.rb-install-package').unbind('click').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (self.pluginProcess) {
                return false;
            }
            var target = $(this);
            target.addClass('loading');
            self.pluginProcess = true;
            target.html('<span class="spinner rb-show-spinner"></span><span class="loading-info">Installing...</span>');
            var installData = target.data();
            jQuery.post(self.globalConfigs.ajaxUrl, installData, function (response) {
                window.onbeforeunload = null;
                location.reload();
            });
        });
    };

    Module.fetchImporter = function () {

        var self = this;
        $('#rb-importer-reload').on('click', function (e) {

            e.preventDefault();
            e.stopPropagation();

            var target = $(this);
            if (self.importProcess) {
                return false;
            }

            var confirm = window.confirm('Do you want to update new import data?');
            if (confirm === false) {
                return;
            }
            target.html('<span class="spinner rb-show-spinner"></span><span class="loading-info">Updating...</span>');
            jQuery.post(self.globalConfigs.ajaxUrl, {action: 'rb_update_import'}, function (response) {
                if (response.length > 0 && (response.match(/done/gi))) {
                    location.reload();
                } else {
                    target.html('<span class="error-label">Error...</span>');
                    alert('There was an error: \n\n' + response.replace(/(<([^>]+)>)/gi, ""));
                }
            });

            return false;
        });
    };

    /** install demo */
    Module.installDemo = function () {

        var self = this;
        $('.rb-do-import, .rb-do-reimport').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (self.importProcess) {
                return false;
            }

            var target = $(this);
            var parent = target.parents('.rb-demo-item');
            var showDismiss = parent.find('.show-dismiss');

            var message = 'Ensure you have an active Grid Container in \'Elementor > Settings > Features\' before importing, as of the \'Music Magazine & Blog\' demo or later. Import Demo Content?';
            if (showDismiss.length > 0) {
                message = 'The recommended plugins are not activated. This may result in missing content after the import. Would you like to importing?';
            }
            if (parent.hasClass('is-imported')) {
                if (showDismiss.length > 0) {
                    message = 'The recommended plugins are not activated. This may result in missing content after the import. Would you like to re-importing?';
                } else {
                    message = 'Re-Import Content?';
                }
            }

            var confirm = window.confirm(message);
            if (confirm === false) {
                return;
            }

            self.importProcess = true;
            parent.addClass('is-importing');

            var importData = parent.data();
            importData.import_all = parent.find('.rb_import_all').data('checked');
            importData.import_content = parent.find('.rb_import_content').data('checked');
            importData.import_pages = parent.find('.rb_import_pages').data('checked');
            importData.import_opts = parent.find('.rb_import_tops').data('checked');
            importData.import_widgets = parent.find('.rb_import_widgets').data('checked');

            jQuery.post(self.globalConfigs.ajaxUrl, importData, function (response) {
                self.importProcess = false;
                if (response.length > 0 && (response.match(/Have fun!/gi) || response.match(/Skip content/gi))) {
                    self.isImported = true;
                } else {
                    self.eFlag = true;
                    alert('There was an error importing demo content: \n\n' + response.replace(/(<([^>]+)>)/gi, ""));
                }
            });
            self.checkImportProgress(parent);
            return false;
        });
    };

    /** check import progress */
    Module.checkImportProgress = function (parent) {

        var self = this;
        self.importInterval = setInterval(function () {
            jQuery.ajax({
                type: 'POST',
                data: {
                    action: 'rb_check_progress'
                },
                url: self.globalConfigs.ajaxUrl,
                success: function (response) {
                    if (self.eFlag) {
                        clearInterval(self.importInterval);
                        parent.find('.process-count').text('Error, Please contact customer support.');
                        parent.find('.rb-wait').html('<span class="error-label">Error...</span>');
                    } else {
                        if (self.isImported) {
                            clearInterval(self.importInterval);
                            parent.find('.demo-status').text('Already Imported');
                            parent.find('.process-count').text('Completed');
                            parent.find('.process-percent').addClass('is-completed');
                            parent.addClass('just-complete');
                            return false;
                        } else {
                            var obj = jQuery.parseJSON(JSON.stringify(response));
                            if (typeof obj == 'object') {
                                var percentage = Math.floor((obj.imported_count / obj.total_post) * 100);
                                percentage = (percentage > 0) ? percentage - 1 : percentage;
                                parent.find('.process-percent').css('width', percentage + '%');
                                parent.find('.process-count').text(percentage + '%');
                            }
                        }
                    }
                },

                error: function (response) {
                    clearInterval(self.importInterval);
                }
            });
        }, 2000);
    };

    /** translation */
    Module.fetchTranslation = function () {

        var self = this;
        var fetchBtn = $('#rb-fetch-translation');
        fetchBtn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var confirm = window.confirm('Are you sure you want to fetch new data from translation files?');
            if (confirm === false) {
                return;
            }

            if (self.ajaxProccess) {
                return false;
            }

            var form = $(this).parents('#rb-translation-form');
            var nonce = form.find('[name="rb-core-nonce"]').val();
            var loading = form.find('.fetch-translation-loader');

            $.ajax({
                type: 'POST',
                async: true,
                dataType: 'json',
                url: self.globalConfigs.ajaxUrl,
                data: {
                    action: 'rb_fetch_translation',
                    _nonce: nonce
                },
                beforeSend: function (xhr) {
                    self.ajaxProccess = true;
                    loading.fadeIn(300).removeClass('is-hidden');
                    fetchBtn.attr('disabled', 'disabled');
                },
                success: function (response) {
                    location.reload();
                }
            });

        });
    }

    /** update translation */
    Module.updateTranslation = function () {
        var self = this;
        var updateBtn = $('#rb-update-translation');

        updateBtn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (self.ajaxProccess) {
                return false;
            }

            var form = $(this).parents('#rb-translation-form');
            var nonce = form.find('[name="rb-core-nonce"]').val();
            var loading = form.find('.update-translation-loader');
            var info = form.find('.rb-info');

            var data = 'action=rb_update_translation';
            data += '&_nonce=' + nonce + '';
            data += '&' + form.find('input[type="text"]').serialize();

            $.ajax({
                type: 'POST',
                async: true,
                dataType: 'json',
                url: self.globalConfigs.ajaxUrl,
                data: data,
                beforeSend: function (xhr) {
                    self.ajaxProccess = true;
                    loading.fadeIn(300).removeClass('is-hidden');
                    updateBtn.attr('disabled', 'disabled');
                },
                success: function (response) {
                    loading.fadeOut(300).addClass('is-hidden');
                    updateBtn.removeAttr('disabled');
                    self.ajaxProccess = false;
                    info.text('Settings Saved!').slideDown(300);
                    setTimeout(function () {
                        info.slideUp(300);
                    }, 2000)
                },
                error: function () {
                    info.addClass('is-error').text('Error!').slideDown(300);
                }
            });

        });
    }

    /** edit font project */
    Module.editProjectID = function () {
        let editBtn = $('#rb-edit-project-id');
        let saveBtn = $('#submit-project-id');
        let deleteButton = $('#delete-project-id');

        editBtn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).prev('[name="rb_fonts_project_id"]').prop('readonly', false);
            $(this).remove();
            deleteButton.hide();
            saveBtn.removeClass('is-hidden');
        });

        deleteButton.on('click', function (e) {
            var confirm = window.confirm('Are you sure to delete this project?');
            if (confirm === false) {
                return false;
            }
        });
    };

    return Module;
}(RB_ADMIN_CORE || {}, jQuery));

jQuery(document).ready(function () {
    RB_ADMIN_CORE.init();
});