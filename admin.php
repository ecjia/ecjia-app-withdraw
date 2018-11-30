<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * ECJIA 会员充值提现管理
 */
class admin extends ecjia_admin
{

    public function __construct()
    {
        parent::__construct();

        RC_Loader::load_app_func('admin_user', 'finance');
        RC_Loader::load_app_func('global', 'goods');

        /* 加载所需js */
        RC_Script::enqueue_script('jquery-validate');
        RC_Script::enqueue_script('jquery-form');
        RC_Script::enqueue_script('smoke');

        //时间控件
        RC_Script::enqueue_script('bootstrap-datepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datepicker.min.js'));
        RC_Style::enqueue_style('datepicker', RC_Uri::admin_url('statics/lib/datepicker/datepicker.css'));

        RC_Script::enqueue_script('jquery-chosen');
        RC_Style::enqueue_style('chosen');
        RC_Script::enqueue_script('jquery-uniform');
        RC_Style::enqueue_style('uniform-aristo');

        RC_Script::enqueue_script('admin', RC_App::apps_url('statics/js/admin.js', __FILE__));
        RC_Style::enqueue_style('admin', RC_App::apps_url('statics/css/admin.css', __FILE__), array());

        $account_jslang = array(
            'keywords_required' => RC_Lang::get('user::user_account.keywords_required'),
            'username_required' => RC_Lang::get('user::user_account.username_required'),
            'amount_required'   => RC_Lang::get('user::user_account.amount_required'),
            'check_time'        => RC_Lang::get('user::user_account.check_time'),
        );
        RC_Script::localize_script('admin', 'account_jslang', $account_jslang);

        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('user::user_account.withdraw_apply'), RC_Uri::url('withdraw/admin/init')));
    }

    /**
     * 充值提现申请列表
     */
    public function init()
    {
        $this->admin_priv('withdraw_manage');

        ecjia_screen::get_current_screen()->remove_last_nav_here();
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('user::user_account.withdraw_apply')));

        $this->assign('ur_here', RC_Lang::get('user::user_account.withdraw_apply'));
        $this->assign('action_link', array('text' => '线下打款', 'href' => RC_Uri::url('withdraw/admin/add')));

        $list = $this->get_withdraw_list();

        $this->assign('list', $list);
        $this->assign('filter', $list['filter']);
        $this->assign('type_count', $list['type_count']);

        $this->assign('form_action', RC_Uri::url('withdraw/admin/init'));
        $this->assign('batch_action', RC_Uri::url('withdraw/admin/batch_remove'));

        $this->display('admin_account_list.dwt');
    }

    public function add()
    {
        $this->admin_priv('withdraw_manage');

        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here('线下打款'));

        $this->assign('ur_here', '线下打款');
        $this->assign('action_link', array('href' => RC_Uri::url('withdraw/admin/init'), 'text' => RC_Lang::get('user::user_account.withdraw_apply')));

        $payment = get_payment();
        $this->assign('payment', $payment);

        $this->assign('form_action', RC_Uri::url('withdraw/admin/insert'));

        $this->display('admin_account_edit.dwt');
    }

    /**
     * 添加充值提现申请
     */
    public function insert()
    {
        $this->admin_priv('withdraw_manage');

        /* 初始化变量 */
        $id           = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $is_paid      = !empty($_POST['is_paid']) ? intval($_POST['is_paid']) : 0;
        $amount       = !empty($_POST['amount']) ? floatval($_POST['amount']) : 0;
        $process_type = !empty($_POST['process_type']) ? intval($_POST['process_type']) : 0;
        $user_mobile  = !empty($_POST['user_mobile']) ? trim($_POST['user_mobile']) : '';
        $admin_note   = !empty($_POST['admin_note']) ? trim($_POST['admin_note']) : '';
        $user_note    = !empty($_POST['user_note']) ? trim($_POST['user_note']) : '';
        $payment      = !empty($_POST['payment']) ? trim($_POST['payment']) : '';
        $amount_count = $amount;

        /* 验证参数有效性  */
        if (!is_numeric($amount) || empty($amount) || $amount <= 0 || strpos($amount, '.') > 0) {
            return $this->showmessage(RC_Lang::get('user::user_account.js_languages.deposit_amount_error'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $user_info = RC_DB::table('users')->where('mobile_phone', $user_mobile)->first();
        /* 此会员是否存在 */
        if (empty($user_info)) {
            return $this->showmessage(RC_Lang::get('user::user_account.username_not_exist'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        if (empty($payment)) {
            return $this->showmessage(RC_Lang::get('user::user_account.js_languages.pay_code_empty'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        /* 退款，检查余额是否足够 */
        if ($process_type == 1) {
            //$user_account = get_user_surplus($user_info['user_id']);
            $user_account = user_account::get_user_money($user_info['user_id']);
            /* 如果扣除的余额多于此会员拥有的余额，提示 */
            if ($amount > $user_account) {
                return $this->showmessage(RC_Lang::get('user::user_account.surplus_amount_error'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }
        }

        /* 入库的操作 */
        if ($process_type == 1) {
            $amount = (-1) * $amount;
        }

        /*金额必须为1元起*/
        if (abs($amount) < 1) {
            return $this->showmessage(RC_Lang::get('user::user_account.min_amount_error'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $order_sn = ecjia_order_deposit_sn();

        $data = array(
            'user_id'      => $user_info['user_id'],
            'admin_user'   => $_SESSION['admin_name'],
            'amount'       => $amount,
            'add_time'     => RC_Time::gmtime(),
            'admin_note'   => $admin_note,
            'user_note'    => $user_note,
            'process_type' => $process_type,
            'payment'      => $payment,
            'is_paid'      => $is_paid,
            'order_sn'     => $order_sn,
            'from_type'    => 'admin',
            'from_value'   => $_SESSION['admin_id'],
        );
        if ($is_paid == 1) {
            $data['paid_time'] = RC_Time::gmtime();
        }

        $accountid = RC_DB::table('user_account')->insertGetId($data);

        /* 更新会员余额数量 */
        if ($is_paid == 1) {
            $change_desc = $amount > 0 ? RC_Lang::get('user::user_account.surplus_type.0') : RC_Lang::get('user::user_account.surplus_type.1');
            $change_type = $amount > 0 ? ACT_SAVING : ACT_DRAWING;

            change_account_log($user_info['user_id'], $amount, 0, 0, 0, $change_desc, $change_type);
        } else {
            //提现申请且到款状态为未确认状态时；且提现申请成功
            if ($process_type == '1' && !empty($accountid) && $is_paid == '0') {
                //提现申请成功，记录account_log；从余额中冻结提现金额
                $frozen_money = abs($amount);
                $user_money   = $amount;

                $options = array(
                    'user_id'      => $user_info['user_id'],
                    'frozen_money' => $frozen_money,
                    'user_money'   => $user_money,
                    'change_type'  => ACT_DRAWING,
                    'change_desc'  => '【申请提现】',
                );

                RC_Api::api('user', 'account_change_log', $options);
            }
        }

        if ($process_type == 0) {
            $account = RC_Lang::get('user::user_account.deposit');
        } else {
            $account = RC_Lang::get('user::user_account.withdraw');
        }

        ecjia_admin::admin_log(RC_Lang::get('user::user_account.log_username') . $user_info['user_name'] . ',' . $account . $amount, 'add', 'user_account');

        $links[0]['text'] = RC_Lang::get('user::user_account.back_withdraw_list');
        $links[0]['href'] = RC_Uri::url('withdraw/admin/init');
        $links[1]['text'] = RC_Lang::get('user::user_account.continue_add');
        $links[1]['href'] = RC_Uri::url('withdraw/admin/add');

        return $this->showmessage(RC_Lang::get('user::user_account.add_success'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('links' => $links, 'pjaxurl' => RC_Uri::url('withdraw/admin/init')));
    }

    /**
     * 编辑充值提现申请
     */
    public function edit()
    {
        $this->admin_priv('withdraw_manage');

        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('user::user_account.surplus_edit')));
        ecjia_screen::get_current_screen()->add_help_tab(array(
            'id'      => 'overview',
            'title'   => RC_Lang::get('user::users.overview'),
            'content' => '<p>' . RC_Lang::get('user::users.edit_account_help') . '</p>',
        ));

        ecjia_screen::get_current_screen()->set_help_sidebar(
            '<p><strong>' . RC_Lang::get('user::users.more_info') . '</strong></p>' .
            '<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:充值和提现申请#.E6.B7.BB.E5.8A.A0.E7.94.B3.E8.AF.B7" target="_blank">' . RC_Lang::get('user::users.about_edit_account') . '</a>') . '</p>'
        );

        $this->assign('ur_here', RC_Lang::get('user::user_account.surplus_edit'));
        $this->assign('action_link', array('text' => RC_Lang::get('user::user_account.withdraw_apply'), 'href' => RC_Uri::url('withdraw/admin/init')));

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        /* 查询当前的预付款信息 */
        $account = array();
        $account = RC_DB::table('user_account')->where('id', $id)->first();

        $account['add_time'] = RC_Time::local_date(ecjia::config('time_format'), $account['add_time']);
        $user_mobile         = RC_DB::table('users')->where('user_id', $account['user_id'])->pluck('mobile_phone');

        $account['user_note'] = htmlspecialchars($account['user_note']);
        $account['payment']   = strip_tags($account['payment']);
        $account['amount']    = abs($account['amount']);

        /* 模板赋值 */
        $this->assign('surplus', $account);
        $this->assign('user_mobile', $user_mobile);
        $this->assign('id', $id);

        $this->assign('form_action', RC_Uri::url('withdraw/admin/update'));

        $this->display('admin_account_check.dwt');
    }

    /**
     * 更新充值提现申请
     */
    public function update()
    {
        /* 权限判断 */
        $this->admin_priv('withdraw_manage', ecjia::MSGTYPE_JSON);

        $id          = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $admin_note  = !empty($_POST['admin_note']) ? trim($_POST['admin_note']) : '';
        $user_note   = !empty($_POST['user_note']) ? trim($_POST['user_note']) : '';
        $user_mobile = !empty($_POST['user_mobile']) ? trim($_POST['user_mobile']) : '';

        $info = RC_DB::table('user_account')->where('id', $id)->first();

        if (!empty($info['order_sn'])) {
            $order_sn = $info['order_sn'];
        } else {
            $order_sn = ecjia_order_deposit_sn();
        }

        /* 更新数据表 */
        $data = array(
            'admin_note' => $admin_note,
            'user_note'  => $user_note,
            'order_sn'   => $order_sn,
        );
        RC_DB::table('user_account')->where('id', $id)->update($data);

        if ($info['process_type'] == 0) {
            $account = RC_Lang::get('user::user_account.deposit');
        } else {
            $account        = RC_Lang::get('user::user_account.withdraw');
            $info['amount'] = abs($info['amount']);
        }

        ecjia_admin::admin_log(RC_Lang::get('user::user_account.log_username') . $user_name . ',' . $account . $info['amount'], 'edit', 'user_account');

        $links[0]['text'] = RC_Lang::get('user::user_account.back_withdraw_list');
        $links[0]['href'] = RC_Uri::url('withdraw/admin/init');

        return $this->showmessage(RC_Lang::get('user::user_account.edit_success'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('links' => $links, 'pjaxurl' => RC_Uri::url('withdraw/admin/info', array('id' => $id))));
    }

    /**
     * 审核会员余额页面
     */
    public function check()
    {
        $this->admin_priv('withdraw_manage');

        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('user::user_account.check')));

        $this->assign('ur_here', RC_Lang::get('user::user_account.check'));
        $this->assign('action_link', array('text' => '提现申请', 'href' => RC_Uri::url('withdraw/admin/init')));

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        /* 查询当前的预付款信息 */
        $account = array();
        $account = RC_DB::table('user_account')->where('id', $id)->first();

        $account['add_time']  = RC_Time::local_date(ecjia::config('time_format'), $account['add_time']);
        $account['user_note'] = htmlspecialchars($account['user_note']);

        $user_name    = RC_DB::table('users')->where('user_id', $account['user_id'])->pluck('user_name');
        $payment_name = RC_DB::table('payment')->where('pay_code', $account['payment'])->pluck('pay_name');

        $account['payment'] = empty($payment_name) ? strip_tags($account['payment']) : strip_tags($payment_name);
        $account['amount']  = abs($account['amount']);

        $this->assign('surplus', $account);
        $this->assign('user_name', $user_name);
        $this->assign('id', $id);
        $this->assign('check_action', RC_Uri::url('withdraw/admin/action'));
        $this->assign('is_check', 1);

        $this->display('admin_account_check.dwt');
    }

    /**
     * 更新会员余额的状态
     */
    public function action()
    {
        /* 检查权限 */
        $this->admin_priv('withdraw_manage', ecjia::MSGTYPE_JSON);

        /* 初始化 */
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        // $is_paid    = isset($_POST['is_paid']) ? intval($_POST['is_paid']) : 0;
        $is_paid    = isset($_POST['confirm']) ? 1 : 2;
        $admin_note = isset($_POST['admin_note']) ? trim($_POST['admin_note']) : '';

        /* 查询当前的预付款信息 */
        $account           = array();
        $account           = RC_DB::table('user_account')->where('id', $id)->first();
        $amount            = $account['amount'];
        $frozen_money      = $account['amount'];
        $user_frozen_money = user_account::get_frozen_money($account['user_id']);

        //到款状态不能再次修改
        if (!empty($account['is_paid'])) {
            return $this->showmessage('该订单已审核，请勿重复操作', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        /* 如果是退款申请, 并且已完成,更新此条记录,扣除相应的余额 */
        if ($is_paid == 1) {
            if ($account['process_type'] == 1) {
                //$user_account = get_user_surplus($account['user_id']);
                $user_account = user_account::get_user_money($account['user_id']);

                $fmt_amount = str_replace('-', '', $amount);

                /* 如果扣除的余额多于此会员的总冻结金额，提示 */
                if ($fmt_amount > $user_frozen_money) {
                    return $this->showmessage(RC_Lang::get('user::user_account.surplus_amount_error'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
                }

                update_user_account($id, $amount, $admin_note, 1);

                /* 更新会员余额数量 */
                // change_account_log($account['user_id'], $amount, 0, 0, 0, RC_Lang::get('user::user_account.surplus_type.1'), ACT_DRAWING); //提现申请时已记录

                //解冻提现时冻结的冻结金额
                $user_account = user_account::change_frozen_money($account['user_id'], $frozen_money);
            } else {
                /* 如果是预付款，并且已完成, 更新此条记录，增加相应的余额 */
                update_user_account($id, $amount, $admin_note, 1);

                /* 更新会员余额数量 */
                change_account_log($account['user_id'], $amount, 0, 0, 0, RC_Lang::get('user::user_account.surplus_type.0'), ACT_SAVING);
            }
        } else {
            /* 否则更新信息 */
            $data = array(
                'admin_user' => $_SESSION['admin_name'],
                'admin_note' => $admin_note,
                'is_paid'    => $is_paid,
            );
            //如果是提现且取消；解冻提现时冻结的冻结金额；返还余额
            if ($is_paid == 2 && $account['process_type'] == 1) {
                user_account::change_frozen_money($account['user_id'], $frozen_money); //冻结金额解冻
                user_account::change_user_money($account['user_id'], abs($account['amount'])); //返还余额
            }

            RC_DB::table('user_account')->where('id', $id)->update($data);
        }

        ecjia_admin::admin_log('(' . addslashes(RC_Lang::get('user::user_account.check')) . ')' . $admin_note, 'check', 'user_surplus');

        $links[0]['text'] = RC_Lang::get('user::user_account.back_withdraw_list');
        $links[0]['href'] = RC_Uri::url('withdraw/admin/init');

        return $this->showmessage(RC_Lang::get('user::user_account.attradd_succed'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('links' => $links, 'pjaxurl' => RC_Uri::url('withdraw/admin/info', array('id' => $id))));
    }

    /**
     * 删除一条信息
     */
    public function remove()
    {
        /* 检查权限 */
        $this->admin_priv('withdraw_manage', ecjia::MSGTYPE_JSON);

        $id = intval($_GET['id']);

        $user_account_info = RC_DB::table('user_account')->where('id', $id)->first();
        if (empty($user_account_info)) {
            return $this->showmessage('该记录不存在', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $userinfo = RC_DB::table('users')->where('user_id', $user_account_info['user_id'])->first();
        $name     = $userinfo['user_name'];
        //提现申请记录删除；且到款状态是未确认时；解冻提现申请时冻结的资金
        if ($user_account_info['process_type'] == '1') {
            if ($user_account_info['is_paid'] == '0') {
                $frozen_money = $user_account_info['amount'];
                $user_money   = abs($user_account_info['amount']);

                user_account::change_user_money($user_account_info['user_id'], $user_money); //返还余额
                user_account::change_frozen_money($user_account_info['user_id'], $frozen_money); //减掉冻结金额
            }
        }
        $user_name = empty($name) ? RC_Lang::get('user::users.no_name') : $name;

        RC_DB::table('user_account')->where('id', $id)->delete();
        ecjia_admin::admin_log(addslashes($user_name), 'remove', 'user_account');

        return $this->showmessage(RC_Lang::get('user::user_account.drop_success'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
    }

    /**
     * 批量删除
     */
    public function batch_remove()
    {
        /* 检查权限 */
        $this->admin_priv('withdraw_manage', ecjia::MSGTYPE_JSON);

        if (!empty($_SESSION['ru_id'])) {
            return $this->showmessage(RC_Lang::get('user::user_account.merchants_notice'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        if (isset($_POST['checkboxes'])) {
            $idArr = explode(',', $_POST['checkboxes']);

            $count = count($idArr);

            $data = RC_DB::table('user_account')->whereIn('id', $idArr)->get();

            if (RC_DB::table('user_account')->whereIn('id', $idArr)->delete()) {
                foreach ($data as $v) {
                    if ($v['process_type'] == 1) {
                        $amount = (-1) * $v['amount'];
                        //提现且状态为未确认的；返还余额；解冻冻结金额
                        if ($v['is_paid'] == '0') {
                            $frozen_money = $v['amount'];
                            $user_money   = abs($v['amount']);
                            user_account::change_user_money($v['user_id'], $user_money); //返还余额
                            user_account::change_frozen_money($v['user_id'], $frozen_money); //减掉冻结金额
                        }
                        ecjia_admin::admin_log(sprintf(RC_Lang::get('user::user_account.user_name_is'), $v['user_name']) . sprintf(RC_Lang::get('user::user_account.money_is'), price_format($amount)), 'batch_remove', 'withdraw_apply');
                    } else {
                        ecjia_admin::admin_log(sprintf(RC_Lang::get('user::user_account.user_name_is'), $v['user_name']) . sprintf(RC_Lang::get('user::user_account.money_is'), price_format($v['amount'])), 'batch_remove', 'pay_apply');
                    }
                }
                return $this->showmessage(sprintf(RC_Lang::get('user::user_account.delete_record_count'), $count), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('withdraw/admin/init')));
            }
        } else {
            return $this->showmessage(RC_Lang::get('user::user_account.select_operate_item'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }

    /**
     * 充值提现详情
     */
    public function info()
    {
        $this->admin_priv('withdraw_manage');

        $text    = '提现申请';
        $ur_here = '提现详情';

        $this->assign('ur_here', $ur_here);
        $this->assign('action_link', array('text' => $text, 'href' => RC_Uri::url('withdraw/admin/init')));
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here($ur_here));

        $order_sn = isset($_GET['order_sn']) ? $_GET['order_sn'] : '';
        $id       = isset($_GET['id']) ? $_GET['id'] : 0;

        $account_info              = RC_DB::table('user_account')->where('id', $id)->first();
        $account_info['user_name'] = RC_DB::table('users')->where('user_id', $account_info['user_id'])->pluck('user_name');
        $account_info['pay_name']  = RC_DB::table('payment')->where('pay_code', $account_info['payment'])->pluck('pay_name');
        $account_info['amount']    = abs($account_info['amount']);
        $account_info['user_note'] = htmlspecialchars($account_info['user_note']);
        $account_info['add_time']  = RC_Time::local_date(ecjia::config('time_format'), $account_info['add_time']);
        $account_info['pay_time']  = RC_Time::local_date(ecjia::config('time_format'), $account_info['paid_time']);

        //订单流程状态
        if ($account_info['is_paid'] == 0) {
            $is_paid = 0;
        } elseif ($account_info['is_paid'] == 1) {
            $is_paid = 1;
        } elseif ($account_info['is_paid'] == 2) {
            $is_paid = 2;
        }
        $this->assign('is_paid', $is_paid);

        $this->assign('check_action', RC_Uri::url('withdraw/admin/action'));
        $this->assign('form_action', RC_Uri::url('withdraw/admin/update'));

        $withdraw_fee                          = ecjia::config('withdraw_fee');
        $withdraw_min_amount                   = ecjia::config('withdraw_min_amount');
        $account_info['withdraw_fee']          = $account_info['amount'] * $withdraw_fee / 100;
        $account_info['formated_withdraw_fee'] = ecjia_price_format($account_info['withdraw_fee']);
        $account_info['real_amount']           = $account_info['amount'] - $account_info['withdraw_fee'];
        $account_info['formated_real_amount']  = ecjia_price_format($account_info['real_amount']);

        $this->assign('account_info', $account_info);
        $this->assign('order_sn', $order_sn);
        $this->assign('id', $id);

        $this->display('admin_account_info.dwt');
    }

    /**
     * 账户信息ajax验证
     */
    public function validate_acount()
    {
        $user_mobile = empty($_POST['user_mobile']) ? 0 : $_POST['user_mobile'];
        $user_info   = RC_DB::table('users')->where('mobile_phone', $user_mobile)->first();

        if (empty($user_mobile)) {
            return $this->showmessage('会员手机号码不能为空！', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } elseif (empty($user_info)) {
            return $this->showmessage('该手机号对应的会员信息不存在！', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } else {
            $result = array();
            $result = array('status' => 1, 'username' => $user_info['user_name']);
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, $result);
        }
    }

    /**
     * 获取提现申请列表
     */
    private function get_withdraw_list()
    {
        $filter['start_date'] = empty($_GET['start_date']) ? '' : $_GET['start_date'];
        $filter['end_date']   = empty($_GET['end_date']) ? '' : $_GET['end_date'];
        $filter['keywords']   = trim($_GET['keywords']);
        $filter['type']       = trim($_GET['type']);

        $filter['sort_by']    = empty($_GET['sort_by']) ? 'ua.add_time' : trim($_GET['sort_by']);
        $filter['sort_order'] = empty($_GET['sort_order']) ? 'desc' : trim($_GET['sort_order']);

        $db_user_account = RC_DB::table('user_account as ua')
            ->leftJoin('users as u', RC_DB::raw('ua.user_id'), '=', RC_DB::raw('u.user_id'))
            ->where(RC_DB::raw('ua.process_type'), 1);

        if ($filter['keywords']) {
            $db_user_account->where(RC_DB::raw('u.user_name'), 'like', '%' . mysql_like_quote($filter['keywords']) . '%');
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $start_date = RC_Time::local_strtotime($filter['start_date']);
            $end_date   = RC_Time::local_strtotime($filter['end_date']);

            $db_user_account->where('add_time', '>=', $start_date)->where('add_time', '<', $end_date);
        }

        $type_count = $db_user_account->select(RC_DB::raw('SUM(IF(ua.is_paid = 0, 1, 0)) as wait'),
            RC_DB::raw('SUM(IF(ua.is_paid = 1, 1, 0)) as finished'),
            RC_DB::raw('SUM(IF(ua.is_paid = 2, 1, 0)) as canceled'))->first();

        if ($filter['type'] == 'finished') {
            $db_user_account->where(RC_DB::raw('ua.is_paid'), 1);
        } elseif ($filter['type'] == 'canceled') {
            $db_user_account->where(RC_DB::raw('ua.is_paid'), 2);
        } else {
            $db_user_account->where(RC_DB::raw('ua.is_paid'), 0);
        }

        $count = $db_user_account->count();
        $page  = new ecjia_page($count, 15, 6);

        $payment_method = RC_Loader::load_app_class('payment_method', 'payment');
        $payment_list   = $payment_method->available_payment_list(false);

        $pay_name = array();
        if (!empty($payment_list) && is_array($payment_list)) {
            foreach ($payment_list as $key => $value) {
                $pay_name[$value['pay_code']] = $value['pay_name'];
            }
        }

        $list = $db_user_account
            ->orderBy(RC_DB::raw($filter['sort_by']), $filter['sort_order'])
            ->take(15)
            ->skip($page->start_id - 1)
            ->select(RC_DB::raw('ua.*'), RC_DB::raw('u.user_name'))
            ->get();

        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $list[$key]['surplus_amount'] = ecjia_price_format(abs($value['amount']), false);
                $list[$key]['add_date']       = RC_Time::local_date(ecjia::config('time_format'), $value['add_time']);
                $list[$key]['payment']        = empty($pay_name[$value['payment']]) ? strip_tags($value['payment']) : strip_tags($pay_name[$value['payment']]);
            }
        }
        return array('list' => $list, 'filter' => $filter, 'page' => $page->show(5), 'desc' => $page->page_desc(), 'type_count' => $type_count);
    }

}

// end
