<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2019/1/2
 * Time: 14:13
 */
namespace Ecjia\App\Withdraw\Orders;

use \Ecjia\App\Withdraw\Models\UserAccountModel;
use Ecjia\App\Withdraw\WithdrawConstant;

/**
 * Class WithdrawOrderFailed
 * 提现订单失败
 */
class WithdrawOrderFailedProcess extends WithdrawOrderSuccessProcess
{

    /**
     * 更新提现订单
     */
    protected function updateWithdrawOrder($admin_user, $admin_note)
    {
        $amount            = $this->order->amount;

        $this->repository->updateCancelOrderUserAccount($this->order->order_sn, $admin_user, $admin_note);
    }

    /**
     * 更新帐户资金
     */
    protected function updateAccountMoney()
    {
        $amount            = $this->order->amount;

        $this->user_account->withdrawCancel($amount);
    }

    /**
     * 发送短信消息通知
     */
    protected function sendSmsMessageNotice()
    {

    }


}