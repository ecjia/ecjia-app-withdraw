<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.account_edit.init();
</script>
<!-- {/block} -->

<!-- {block name="main_content"} -->

<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times" data-original-title=""
		 title=""></i></button>
	<p><strong>温馨提示：</strong></p>
	<p>线下提现申请：指用户通过线下人工或其他渠道套现由管理员提交申请后自动记录对应账号内提现数据及金额变动。</p>
</div>

<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		<!-- {if $action_link} -->
		<a class="btn plus_or_reply data-pjax" href="{$action_link.href}"><i class="fontello-icon-reply"></i>{$action_link.text}</a>
		<!-- {/if} -->
	</h3>
</div>

<div class="row-fluid edit-page">
	<div class="span12">
		<form class="form-horizontal" id="form-privilege" name="theForm" action="{$form_action}" method="post">
			<fieldset>
				<div class="control-group formSep">
					<label class="control-label">{lang key='user::user_account.user_mobile'}：</label>
					<div class="controls">
						<input class="w350 user-mobile" name="user_mobile" action='{url path="withdraw/admin/validate_acount"}' type="text"
						 value="{if $user_mobile}{$user_mobile}{else if $smarty.get.id}匿名会员{else}{/if}" {if $user_surplus.is_paid}
						 readonly="true" {/if} />
						<span class="input-must">{lang key='system::system.require_field'}</span>
					</div>
				</div>

				<div class="control-group-user hide">
					<div class="control-group formSep">
						<label class="control-label">会员名称：</label>
						<div class="controls userinfo l_h30">
						</div>
					</div>

					<div class="control-group formSep">
						<label class="control-label">可用余额：</label>
						<div class="controls user_money l_h30">
						</div>
					</div>

					<div class="control-group formSep">
						<label class="control-label">微信昵称：</label>
						<div class="controls wechat_nickname">
							<span></span>
							<div class="help-block">仔细查看会员手机号、名称、微信昵称是否正确，若正确请忽视，若不正确，请主动调整手机号。</div>
						</div>
					</div>
				</div>

				<div class="control-group formSep">
					<label class="control-label">提现金额：</label>
					<div class="controls">
						<input class="w350" type="text" name="amount" value="{$user_surplus.amount}" {if $user_surplus.is_paid} readonly="true"
						 {/if} /> 元
						<span class="input-must">{lang key='system::system.require_field'}</span>
						<span class="help-block">提现金额不能大于可用余额。</span>
					</div>
				</div>

				<div class="control-group formSep">
					<label class="control-label">提现手续费：</label>
					<div class="controls l_h30">￥0.00</div>
				</div>

				<div class="control-group formSep ">
					<label class="control-label">提现方式：</label>
					<div class="controls chk_radio">
						<input class="uni_style" type="radio" name="recharge_type" value="0" checked /><span>微信钱包</span>
						<input class="uni_style" type="radio" name="recharge_type" value="1" /><span>手动打款</span>
						<span class="help-block">选择微信钱包后确认，系统会向微信发送打款请求，并在对应会员账户中生成对应提现记录，当请求被微信方处理成功后，申请会被自动处理为已完成状态</span>
						<span class="help-block">当选择手动打款后确认，则申请直接被处理为已完成状态</span>
					</div>
				</div>

				<div class="control-group formSep">
					<label class="control-label">{lang key='user::user_account.label_surplus_notic'}</label>
					<div class="controls">
						<textarea class="span6" name="admin_note" rows="6">{$user_surplus.admin_note}</textarea>
					</div>
					<div class="controls">
						<select class="select_admin_note span5">
							<option value="0">请选择管理员备注</option>
							<option value="1">随机奖励金额</option>
							<option value="2">管理员手动充值</option>
							<option value="3">用户已在线下门店现金支付</option>
							<option value="4">通过线下柜台、手机银行或网银将款项转账至商城账号上</option>
						</select>
						<span class="help-block">此备注仅限管理员查看，备注内容可使用快捷用语。</span>
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<button class="btn btn-gebo" type="submit">{lang key='system::system.button_submit'}</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
<!-- {/block} -->