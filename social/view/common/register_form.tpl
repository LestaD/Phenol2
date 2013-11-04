<div class="centerbox">
	<div class="box loginbox box-shadow">
		<div class="box-title">{_register}</div>
		<form action="{action}" method="post" enctype="multipart/form-data">
			<? if ( $step == 1 ) { ?>
			<div class="box-inputs">
				<input type="text" class="input" name="data[email]" placeholder="{_your_email}" value="{regdata_email}" />
				<input type="text" class="input" name="data[login]" placeholder="{_your_login}" value="{regdata_login}" />
			</div>
			<?} elseif ( $step == 2 ) { ?>
			<div class="box-inputs">
				<input type="text" class="input" value="{user_email}" disabled="disabled" title="{_your_email}" />
				<input type="text" class="input" value="{user_login}" disabled="disabled" title="{_your_login}" />
			</div>
			<div class="box-inputs">
				<input type="text" class="input" name="data[name]" placeholder="{_your_name}" />
				<input type="text" class="input" name="data[surname]" placeholder="{_your_surname}" />
			</div>
			<div class="box-inputs">
				<input type="text" class="input" name="data[passwr]" placeholder="{_password}" />
				<input type="text" class="input" name="data[repeat]" placeholder="{_repeat_password}" />
			</div>
			<? } ?>
			<div class="box-buttons">
				<input type="submit" value="{_register_in}" class="button green" />
				<? if ( $step == 2 OR $step == 3 ) { ?>
				<input type="reset" value="{_clear_form}" class="button gray" />
				<? } ?>
			</div>
		</form>
	</div>
	<? if ($errorbox) {?>
		<br /><div class="box error-box">{errorbox}</div>
	<?}?>
</div>