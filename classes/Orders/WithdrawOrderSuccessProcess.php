<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2019/1/2
 * Time: 14:13
 */
namespace Ecjia\App\Withdraw\Orders;

use Ecjia\App\Finance\UserAccountBalance;
use Ecjia\App\Withdraw\Models\UserAccountModel;
use Ecjia\App\Withdraw\Exceptions\WithdrawException;

/**
 * Class WithdrawOrderSuccess
 * 提现订单成功
 */
class WithdrawOrderSuccessProcess
{
    protected $order;

    protected $user_account;

    public function __construct($order_sn)
    {
        $this->order = UserAccountModel::where('order_sn', $order_sn)->first();

        $this->user_account = new UserAccountBalance($this->order->user_id);
    }

    /**
     * 提现处理操作
     */
    public function process()
    {
        //更新提现订单
        $this->updateWithdrawOrder();
        //处理冻结金额
        //更新账户日志
        $this->updateAccountMoney();

        //发送提现通知
        $this->sendSmsMessageNotice();
    }

    /**
     * 更新提现订单
     */
    protected function updateWithdrawOrder()
    {

    }

    /**
     * 更新帐户资金
     */
    protected function updateAccountMoney()
    {
        $amount            = $this->order->amount;
        $user_frozen_money = $this->user_account->getFrozenMoney();

        $fmt_amount = abs($amount);

        /* 如果扣除的余额多于此会员的总冻结金额，提示 */
        if ($fmt_amount > $user_frozen_money) {
            throw new WithdrawException('要提现的金额超过了此会员的帐户余额，此操作将不可进行！');
        }

        $this->user_account->withdrawSuccessful($amount);
    }

    /**
     * 发送短信消息通知
     */
    protected function sendSmsMessageNotice()
    {

    }

}