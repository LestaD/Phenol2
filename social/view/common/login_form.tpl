<div class="centerbox">
	<div class="box loginbox box-shadow">
		<div class="box-title">{_login_to}</div>
		<form action="{action}" method="post" enctype="multipart/form-data">
			<div class="box-inputs">
				<input type="text" value="" name="data[login]" class="input" placeholder="{_login_or_email}" /><input type="password" class="input" name="data[password]" value="" placeholder="{_password}" />
			</div>
			<div class="box-buttons">
				<input type="submit" value="{_login}" class="button green" />
				<?php if ( $recovery ) {?><a href="" class="button red">{_recover_password}</a><?php }?>
			</div>
		</form>
	</div>
	<? if ($errorbox) {?>
		<br /><div class="box error-box">{errorbox}</div>
	<?}?>
</div>