<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2019/1/2
 * Time: 14:13
 */
namespace Ecjia\App\Withdraw\Orders;

use \Ecjia\App\Withdraw\Models\UserAccountModel;

/**
 * Class WithdrawOrderSuccess
 * 提现订单成功
 */
class WithdrawOrderSuccessProcess
{
    protected $order;

    public function __construct($order_sn)
    {
        $this->order = UserAccountModel::where('order_sn', $order_sn)->first();
    }

    /**
     * 提现处理操作
     */
    public function process()
    {
        //更新提现订单
        //处理冻结金额
        //更新账户日志
        //发送提现通知
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

    }



}