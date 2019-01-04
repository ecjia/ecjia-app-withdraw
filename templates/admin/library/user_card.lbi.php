<?php defined('IN_ECJIA') or exit('No permission resources.'); ?>

<div class="withdraw_card_info">
    <div class="user-info">
        <div class="left">
            <img src="{$data.avatar_img}" alt="">
            <div class="info">
                <div class="name">{$data.user_name}</div>
                <div><a href="{RC_Uri::url('user/admin/info')}&id={$data.user_id}" target="_blank">详情 >></a></div>
            </div>
        </div>
        {if $data.rank_name}
        <div class="right">
            <span>{$data.rank_name}</span>
        </div>
        {/if}
    </div>
    <div class="card-info">
        <div class="item">
            <div class="left">可提现金额：</div>
            <div class="right">{$data.formated_user_money}</div>
        </div>
        <div class="item">
            <div class="left">已绑提现账号：</div>
            <div class="right">
                {foreach from=$data.user_binded_list item=val}
                <p><img src="{$val.bank_icon}" alt="" width="25" height="25">{$val.formated_pay_name}</p>
                {foreachelse}
                <p><img src="{$data.unbind_icon}" alt="" width="25" height="25">未绑定银行卡</p>
                <p><img src="{$data.unbind_icon}" alt="" width="25" height="25">未绑定微信钱包</p>
                {/foreach}
            </div>
        </div>
    </div>
</div>