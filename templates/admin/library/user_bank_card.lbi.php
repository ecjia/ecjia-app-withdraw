<?php defined('IN_ECJIA') or exit('No permission resources.');?>

{if $user_bank_card.bank_type eq 'bank'}
<div class="control-group formSep">
    <label class="control-label">银行卡号：</label>
    <div class="controls l_h30">{$user_bank_card.bank_name} <strong>( {$user_bank_card.bank_card} )</strong></div>
</div>
{else if $user_bank_card.bank_type eq 'wechat'}
<div class="control-group formSep">
    <label class="control-label">微信钱包：</label>
    <div class="controls l_h30">{$user_bank_card.bank_name} <strong>( {$user_bank_card.cardholder} )</strong></div>
</div>
{/if}