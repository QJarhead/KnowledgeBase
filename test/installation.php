<?php
include_once "language.php";
pt("USERNAME");
?>

<form action="installation.php" method="post">
	<fieldset id="adminaccount">
		<legend><?php p("CREATE ADMIN ACCOUNT"); ?></legend>
		<p class="grouptop">
			<input type="text" name="adminlogin" id="adminlogin"
				placeholder="<?php pt('USERNAME'); ?>"
				value=""
				autocomplete="on" autocapitalize="off" autocorrect="off" autofocus required>
			<label for="adminlogin" class="infield"><?php pt('USERNAME'); ?></label>
		</p>
		<p class="groupbottom">
			<input type="password" name="adminpass" data-typetoggle="#show" id="adminpass"
				placeholder="<?php pt('PASSWORD'); ?>"
				value="<?php p($_['adminpass']); ?>"
				autocomplete="off" autocapitalize="off" autocorrect="off" required>
			<label for="adminpass" class="infield"><?php pt('PASSWORD'); ?></label>
			<input type="checkbox" id="show" name="show">
			<label for="show"></label>
		</p>
	</fieldset>
	<div class="buttons"><input type="submit" class="primary" value="<?php pt( 'FINISH'); ?>" data-finishing="<?php pt( 'Finishing …' ); ?>"></div>
</form>
