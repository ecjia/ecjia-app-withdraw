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

                if (start_date == '' || end_date == '') {
                    var data = {
                        message: '开始或结束时间不能为空',
                        state: "error",
                    };
                    ecjia.admin.showmessage(data);
                    return false;
                }

                if (start_date >= end_date) {
                    var data = {
                        message: '开始时间不能大于或等于结束时间',
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
        },

        validate: function () {
            $(".user-mobile").blur(function () {
                var $this = $(this);
                var url = $this.attr('action');
                var mobile = $this.val();
                var data = {
                    user_mobile: mobile,
                }

                $.post(url, data, function (data) {
                    if (data.state == 'error') {
                        ecjia.admin.showmessage(data);
                    }

                    if (data.status == 1) {
                        $(".user").removeClass("username");
                        $(".userinfo").html(data.username);
                    }
                }, 'json');
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
                        required: account_jslang.username_required
                    },
                    amount: {
                        required: account_jslang.amount_required
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
        }
    };

    app.account_check = {
        init: function () {
            app.account_check.submit();
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
        }
    };

})(ecjia.admin, jQuery);

// end