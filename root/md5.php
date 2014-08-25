<?php

	echo 'send this to the admin:' . md5($_POST['p']);

?>
<form method="get" action="/alcohol/login/password">
	<label> New Password:
		<input type="password" name="password">
	</label>
	<input type="submit">
</form>