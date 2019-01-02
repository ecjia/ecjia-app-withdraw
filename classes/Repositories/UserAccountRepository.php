<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/26
 * Time: 14:13
 */

namespace Ecjia\App\Withdraw\Repositories;

use Royalcms\Component\Repository\Repositories\AbstractRepository;
use RC_Time;
use RC_DB;

/**
 * Class WithdrawRecordRepository
 * 只处理提现订单
 * @package Ecjia\App\Withdraw\Repositories
 */
class WithdrawRecordRepository extends AbstractRepository
{
    protected $model = 'Ecjia\App\Withdraw\Models\UserAccountModel';

    protected $orderBy = ['id' => 'desc'];

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
            'user_id'		=> $data['user_id'] ,
            'order_sn'		=> $data['order_sn'],
            'admin_user'	=> '' ,
            'amount'		=> $amount ,
            'add_time'		=> RC_Time::gmtime() ,
            'paid_time'		=> 0,
            'admin_note'	=> '' ,
            'user_note'		=> $data['user_note'] ,
            'process_type'	=> $data['process_type'] ,
            'payment'		=> $data['payment'] ,
            'is_paid'		=> 0,
            'from_type'		=> $data['from_type'],
            'from_value'	=> $data['from_type']
        );
        return RC_DB::table('user_account')->insertGetId($data);
    }

    /**
     * 更新会员提现订单
     *
     * @access  public
     * @param   array     $id          帐目ID
     * @param   array     $admin_note  管理员描述
     * @param   array     $amount      操作的金额
     * @param   array     $is_paid     是否已完成
     *
     * @return  int
     */
    public function updateUserAccount($order_sn, $amount, $admin_note, $is_paid)
    {
        $data = array(
            'admin_user'	=> $_SESSION['admin_name'],
            'amount'		=> $amount,
            'paid_time'		=> RC_Time::gmtime(),
            'admin_note'	=> $admin_note,
            'is_paid'		=> $is_paid,
            'review_time'   => RC_Time::gmtime(),
        );
        return RC_DB::table('user_account')->where('order_sn', $order_sn)->update($data);
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
        return RC_DB::table('user_account')->where('is_paid', 0)
            ->where('order_sn', $order_sn)
            ->where('user_id', $user_id)
            ->delete();
    }



}