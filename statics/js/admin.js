// JavaScript Document
;
(function (app, $) {
    app.account_list = {
        init: function () {
            /* 加载日期控件 */
            $(".date").datepicker({
                format: "yyyy-mm-dd",
                container: '.main_content',
            });
            app.account_list.search();
        },

        search: function () {
            $(".select-button").click(function () {
                var start_date = $("input[name='start_date']").val();
                var end_date = $("input[name='end_date']").val();

                if (start_date != '' && end_date != '' && start_date >= end_date) {
                    var data = {
                        message: js_lang.time_error,
                        state: "error",
                    };
                    ecjia.admin.showmessage(data);
                    return false;
                }
                var url = $("form[name='searchForm']").attr('action');

                if (start_date != '') url += '&start_date=' + start_date;
                if (end_date != '') url += '&end_date=' + end_date;

                var keywords = $("input[name='keywords']").val();

                if (keywords != '') {
                    url += '&keywords=' + keywords;
                }

                ecjia.pjax(url);
            });
        },
    };

    app.account_edit = {
        init: function () {
            app.account_edit.validate();
            app.account_edit.submit();
            app.account_edit.select_note();
            app.account_edit.select_payment();
            app.account_edit.btn_clear();
        },

        validate: function () {
            $(".user-mobile").koala({
                delay: 500,
                keyup: function (event) {
                    var $this = $(this);
                    var url = $this.attr('action');
                    var mobile = $this.val();
                    if (mobile.length < 11) {
                        return false;
                    }
                    var data = {
                        user_mobile: mobile,
                    }

                    $('.withdraw_card_content').html('');
                    $.post(url, data, function (data) {
                        if (data.state == 'error') {
                            $(".control-group-user").addClass("hide");
                            ecjia.admin.showmessage(data);
                        }

                        if (data.result.status == 1) {
                            //移除已经选中的提现方式
                            $('input[name="payment"]').each(function () {
                                $(this).prop('checked', false).parent().removeClass('uni-checked');
                            });
                            $('input[name="user_id"]').val(data.result.user_id);

                            var content = data.content;
                            $('.withdraw_card_content').html(content);

                            $('input[name="user_mobile"]').prop('disabled', true);
                        }
                    }, 'json');
                }
            });

            $('input[name="apply_amount"]').koala({
                delay: 500,
                keyup: function (event) {
                    var $this = $(this);
                    var url = $this.attr('data-url');
                    var val = $this.val();
                    var data = {
                        val: val,
                    }
                    $.post(url, data, function (data) {
                        if (data.state == 'error') {
                            ecjia.admin.showmessage(data);
                        }
                        $(".withdraw_pay_fee").html(data.pay_fee);
                    }, 'json');
                }
            });
        },

        submit: function () {
            var $this = $("form[name='theForm']");
            var option = {
                rules: {
                    username: {
                        required: true
                    },
                    amount: {
                        required: true
                    }
                },
                messages: {
                    username: {
                        required: js_lang.username_required
                    },
                    amount: {
                        required: js_lang.amount_required
                    }
                },
                submitHandler: function () {
                    $this.ajaxSubmit({
                        dataType: "json",
                        success: function (data) {
                            ecjia.admin.showmessage(data);
                        }
                    });
                }
            }
            var options = $.extend(ecjia.admin.defaultOptions.validate, option);
            $this.validate(options);
        },

        select_note: function () {
            $('.select_admin_note').off('change').on('change', function () {
                var $this = $('.select_admin_note option:selected');
                var text = $this.text();
                var val = $this.val();
                var html = '';
                if (val != 0) {
                    html = text;
                }
                $('textarea[name="admin_note"]').val(html);
            });
        },

        select_payment: function () {
            $('input[name="payment"]').off('click').on('click', function () {
                var $this = $(this),
                    code = $this.val(),
                    url = $this.parents('.chk_radio').attr('data-url'),
                    user_id = $('input[name="user_id"]').val();

                if (user_id == 0 || user_id == undefined) {
                    return false;
                }

                $('.user_bank_card').html('');
                $.post(url, {
                    code: code,
                    user_id: user_id
                }, function (data) {
                    if (data.state == 'error') {
                        ecjia.admin.showmessage(data);
                        return false;
                    }
                    $('.user_bank_card').html(data.content);
                });
            });
        },

        btn_clear: function () {
            $('.btn-clear').off('click').on('click', function () {
                //移除已经选中的提现方式
                $('input[name="payment"]').each(function () {
                    $(this).prop('checked', false).parent().removeClass('uni-checked');
                });
                $('input[name="user_id"]').val('');

                $('.withdraw_card_content').html('');
                $('input[name="user_mobile"]').val('');

                $('input[name="user_mobile"]').prop('disabled', false);
            });
        },
    };

    app.account_check = {
        init: function () {
            app.account_check.submit();
            app.account_check.withdraw_query();
        },

        submit: function () {
            var $this = $("form[name='theForm']");
            var option = {
                submitHandler: function () {
                    $this.ajaxSubmit({
                        dataType: "json",
                        success: function (data) {
                            ecjia.admin.showmessage(data);
                        }
                    });
                }
            }
            var options = $.extend(ecjia.admin.defaultOptions.validate, option);
            $this.validate(options);
        },

        withdraw_query: function () {
            $('.withdraw_query').off('click').on('click', function () {
                var $this = $(this),
                    url = $this.attr('data-url');
                $.post(url, function (data) {
                    ecjia.admin.showmessage(data);
                });
            });
        }
    };

})(ecjia.admin, jQuery);

// end