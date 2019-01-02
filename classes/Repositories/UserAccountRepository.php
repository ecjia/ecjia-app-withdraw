<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/26
 * Time: 14:13
 */

namespace Ecjia\App\Withdraw\Repositories;

use Ecjia\App\Withdraw\WithdrawConstant;
use Royalcms\Component\Repository\Repositories\AbstractRepository;
use RC_Time;

/**
 * Class UserAccountRepository
 * 只处理提现订单
 * @package Ecjia\App\Withdraw\Repositories
 */
class UserAccountRepository extends AbstractRepository
{
    protected $model = 'Ecjia\App\Withdraw\Models\UserAccountModel';

    protected $orderBy = ['id' => 'desc'];

    protected $process_type = 1; //提现类型


    public function getUserAccountOrder($order_sn)
    {
        return $this->getModel()->where('order_sn', $order_sn)
            ->where('process_type', $this->process_type)
            ->first();
    }

    /**
     * 插入会员提现订单
     *
     * @access  public
     * @param   array     $data     会员提现信息
     * @param   string    $amount   余额
     *
     * @return  int
     */
    public function insertUserAccount($data, $amount)
    {
        $data = array(
            'user_id'		=> $data['user_id'],
            'order_sn'		=> $data['order_sn'],
            'admin_user'	=> '',
            'amount'		=> $amount,
            'add_time'		=> RC_Time::gmtime(),
            'paid_time'		=> 0,
            'admin_note'	=> '' ,
            'user_note'		=> $data['user_note'],
            'process_type'	=> $this->process_type,
            'payment'		=> $data['payment'],
            'is_paid'		=> 0,
            'from_type'		=> $data['from_type'],
            'from_value'	=> $data['from_type']
        );
        return $this->getModel()->create($data);
    }

    /**
     * 更新会员提现订单
     *
     * @param   string     $id          帐目ID
     * @param   string     $admin_note  管理员描述
     * @param   float     $amount      操作的金额
     *
     * @return  int
     */
    public function updatePaidOrderUserAccount($order_sn, $amount, $admin_name, $admin_note)
    {
        $data = array(
            'admin_user'	=> $admin_name,
            'amount'		=> $amount,
            'paid_time'		=> RC_Time::gmtime(),
            'admin_note'	=> $admin_note,
            'is_paid'		=> WithdrawConstant::ORDER_PAY_STATUS_PAYED,
            'review_time'   => RC_Time::gmtime(),
        );
        return $this->getModel()->where('order_sn', $order_sn)
            ->where('process_type', $this->process_type)
            ->update($data);
    }


    /**
     * 更新会员提现订单
     *
     * @param   string     $id          帐目ID
     * @param   string     $admin_note  管理员描述
     * @param   float     $amount      操作的金额
     *
     * @return  int
     */
    public function updateCancelOrderUserAccount($order_sn, $admin_name, $admin_note)
    {
        $data = array(
            'admin_user'	=> $admin_name,
            'admin_note'	=> $admin_note,
            'is_paid'		=> WithdrawConstant::ORDER_PAY_STATUS_CANCEL,
            'review_time'   => RC_Time::gmtime(),
        );
        return $this->getModel()->where('order_sn', $order_sn)
            ->where('process_type', $this->process_type)
            ->update($data);
    }

    /**
     *  删除未确认的会员提现订单信息
     *
     * @access  public
     * @param   int         $rec_id     会员余额记录的ID
     * @param   int         $user_id    会员的ID
     * @return  int
     */
    public function deleteUserAccount($order_sn, $user_id)
    {
        return $this->getModel()->where('is_paid', 0)
            ->where('order_sn', $order_sn)
            ->where('user_id', $user_id)
            ->where('process_type', $this->process_type)
            ->delete();
    }



}