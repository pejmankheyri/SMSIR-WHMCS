{if $mess_no_mobile}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_no_mobile}
	
{elseif $mess_no_user}

	{include file="$template/includes/alert.tpl" type="success" textcenter=true hide=false additionalClasses="my-custom-class" idname="my-alert" msg=$mess_no_user}
	
{elseif $mess_client_is_active}

	{include file="$template/includes/alert.tpl" type="success" textcenter=true hide=false additionalClasses="my-custom-class" idname="my-alert" msg=$mess_client_is_active}

{elseif $mess_resend_code}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_resend_code}
	{$LANG.smsir_your_mobile} {$user_mobile}
	<form method="post" action="{$smarty.server.PHP_SELF}?type={$type}" enctype="multipart/form-data" role="form">
		<div class="row">
			<div class="form-group col-sm-6" style="text-align: center;">
				<input type="submit" value="{$LANG.smsir_verification_code_resend}" class="btn btn-primary" />
			</div>
		</div>
		<input type="hidden" name="submited" value="resend" />
	</form>
	
{elseif $mess_updatedUser}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_updatedUser}
	{$LANG.smsir_your_mobile} {$user_mobile}
	<form method="post" action="{$smarty.server.PHP_SELF}?type={$type}" enctype="multipart/form-data" role="form">
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="verificationcode">{$LANG.smsir_verification_code}</label>
				<input type="text" name="verificationcode" id="verificationcode" class="form-control" required />
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6" style="text-align: center;">
				<input style="float: right;" type="submit" value="{$LANG.smsir_verify}" class="btn btn-primary" />
				<input type="hidden" name="submited" value="ok" />
	</form>
	<form method="post" action="{$smarty.server.PHP_SELF}?type={$type}" enctype="multipart/form-data" role="form">
		<input style="float: right;margin:0 5px" type="submit" value="{$LANG.smsir_verification_code_resend}" class="btn btn-primary" />
		<input type="hidden" name="submited" value="resend" />
	</form>	
			</div>
		</div>
	
{elseif $mess_novalidmobile}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_novalidmobile}
	{$LANG.smsir_novalidmobile_desc}
	
{elseif $mess_enter_code}
	
	{if $mess_code_resend_success}
		{include file="$template/includes/alert.tpl" type="success" textcenter=true hide=false additionalClasses="my-custom-class" idname="my-alert" msg=$mess_code_resend_success}
	{/if}
	
	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_enter_code}
	{$LANG.smsir_your_mobile} {$user_mobile}
	
	<form method="post" action="{$smarty.server.PHP_SELF}?type={$type}" enctype="multipart/form-data" role="form">
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="verificationcode">{$LANG.smsir_verification_code}</label>
				<input type="text" name="verificationcode" id="verificationcode" class="form-control" required />
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6" style="text-align: center;">
				<input style="float: right;" type="submit" value="{$LANG.smsir_verify}" class="btn btn-primary" />
				<input type="hidden" name="submited" value="ok" />
	</form>
	<form method="post" action="{$smarty.server.PHP_SELF}?type={$type}" enctype="multipart/form-data" role="form">
		<input style="float: right;margin:0 5px" type="submit" value="{$LANG.smsir_verification_code_resend}" class="btn btn-primary" />
		<input type="hidden" name="submited" value="resend" />
	</form>	
			</div>
		</div>

{elseif $mess_success}

	{include file="$template/includes/alert.tpl" type="success" textcenter=true hide=false additionalClasses="my-custom-class" idname="my-alert" msg=$mess_success}
	{$LANG.smsir_your_mobile} {$user_mobile}

{elseif $mess_code_nomatch}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_code_nomatch}
	{$LANG.smsir_your_mobile} {$user_mobile}
	
{elseif $mess_code_length}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_code_length}
	{$LANG.smsir_your_mobile} {$user_mobile}
	
{elseif $mess_code_null}

	{include file="$template/includes/alert.tpl" type="error" errorshtml=$mess_code_null}
	{$LANG.smsir_your_mobile} {$user_mobile}
	
{/if}
