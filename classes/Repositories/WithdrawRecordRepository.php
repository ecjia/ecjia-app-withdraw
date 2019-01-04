<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/26
 * Time: 14:13
 */

namespace Ecjia\App\Withdraw\Repositories;

use Royalcms\Component\Repository\Repositories\AbstractRepository;
use Ecjia\App\Withdraw\WithdrawConstant;
use RC_Time;

class WithdrawRecordRepository extends AbstractRepository
{
    protected $model = 'Ecjia\App\Withdraw\Models\WithdrawRecordModel';

    protected $orderBy = ['id' => 'desc'];


    /**
     * 创建提现支付流水记录
     *
     * @param array $data
     * @return static
     */
    public function createWithdrawRecord(array $data)
    {
        $model = $this->findWithdrawOrderSn(array_get($data, 'order_sn'));
        if (empty($model)) {
            $insertData['order_sn'] = array_get($data, 'order_sn');
            $insertData['withdraw_code'] = array_get($data, 'withdraw_code');
            $insertData['withdraw_name'] = array_get($data, 'withdraw_name');
            $insertData['withdraw_amount'] = array_get($data, 'withdraw_amount');
            $insertData['withdraw_status'] = WithdrawConstant::WITHDRAW_RECORD_STATUS_WAIT;
            $insertData['create_time'] = RC_Time::gmtime();

            $model = $this->getModel()->create($insertData);
        }

        return $model;
    }

//    /**
//     * 退款成功记录
//     * @param string $refund_out_no 退款商户号
//     * @param string $refund_trade_no 退款流水号
//     * @param array $refund_info 退款信息，序列化存储
//     */
//    public function refundProcessRecord($refund_out_no, $refund_trade_no, array $refund_info)
//    {
//        $model = $this->findUnSuccessfulRefundOutNo($refund_out_no);
//        if (!empty($model)) {
//            $model->refund_trade_no = $refund_trade_no;
//            $model->refund_status = PayConstant::PAYMENT_REFUND_STATUS_PROGRESS;
//            $model->refund_info = serialize($refund_info);
//            $model->last_error_message = null;
//            $model->last_error_time = null;
//            $model->save();
//        }
//    }

    /**
     * 退款成功记录
     * @param string $order_sn 退款商户号
     * @param string $withdraw_trade_no 退款流水号
     * @param array $return_info 退款信息，序列化存储
     */
    public function withdrawSuccessfulRecord($order_sn, $withdraw_trade_no, array $return_info)
    {
        $model = $this->findUnSuccessfulWithdrawOrderSn($order_sn);
        if (!empty($model)) {
            $model->refund_trade_no = $withdraw_trade_no;
            $model->refund_status = PayConstant::PAYMENT_REFUND_STATUS_REFUND;
            $model->success_result = serialize($return_info);
            $model->last_error_message = null;
            $model->last_error_time = null;
            $model->save();

            //消费订单退款成功后续处理
            (new \Ecjia\App\Refund\RefundProcess\BuyOrderRefundProcess(null, $refund_trade_no))->run();
        }

    }

    /**
     * 退款失败记录
     *
     * @param string $refund_out_no 退款商户号
     * @param string $error_message
     */
    public function withdrawErrorRecord($refund_out_no, $error_message)
    {
        $model = $this->findUnSuccessfulRefundOutNo($refund_out_no);
        if (!empty($model)) {
            $model->refund_status = PayConstant::PAYMENT_REFUND_STATUS_FAIL;
            $model->last_error_message = $error_message;
            $model->last_error_time = \RC_Time::gmtime();
            $model->save();
        }
    }

    /**
     * 退款失败记录
     *
     * @param string $refund_out_no 退款商户号
     * @param string $error_message
     */
    public function withdrawFailedRecord($refund_out_no, $refund_trade_no, array $refund_info)
    {
        $model = $this->findUnSuccessfulRefundOutNo($refund_out_no);
        if (!empty($model)) {
            //处理refund_info是否有数据，如果有数据，合并后存入
            $refund_info_data = unserialize($model->refund_info);
            if (! empty($refund_info_data)) {
                $refund_info = array_merge($refund_info_data, $refund_info);
            }

            $model->refund_trade_no = $refund_trade_no;
            $model->refund_status = PayConstant::PAYMENT_REFUND_STATUS_FAIL;
            $model->refund_info = serialize($refund_info);
            $model->save();
        }
    }

    /**
     * 退款关闭记录
     *
     * @param string $refund_out_no 退款商户号
     * @param string $error_message
     */
    public function withdrawClosedRecord($refund_out_no, $refund_trade_no, array $refund_info)
    {
        $model = $this->findUnSuccessfulRefundOutNo($refund_out_no);
        if (!empty($model)) {
            //处理refund_info是否有数据，如果有数据，合并后存入
            $refund_info_data = unserialize($model->refund_info);
            if (! empty($refund_info_data)) {
                $refund_info = array_merge($refund_info_data, $refund_info);
            }

            $model->refund_trade_no = $refund_trade_no;
            $model->refund_status = PayConstant::PAYMENT_REFUND_STATUS_REFUND;
            $model->refund_info = serialize($refund_info);
            $model->last_error_message = null;
            $model->last_error_time = null;
            $model->save();
        }
    }

    /**
     * 查找提现记录
     * @param string $partner_trade_no 退款商户号
     */
    public function findWithdrawOrderSn($partner_trade_no)
    {
        $model = $this->findBy('order_sn', $partner_trade_no);
        return $model;
    }

    /**
     * 查找未成功提现记录
     * @param string $partner_trade_no 退款商户号
     */
    public function findUnSuccessfulWithdrawOrderSn($partner_trade_no)
    {
        $model = $this->findWhere(['order_sn' => $partner_trade_no, 'withdraw_status' => ['withdraw_status', '<>', WithdrawConstant::WITHDRAW_RECORD_STATUS_PAYED]])->first();
        return $model;
    }

}