<?php if (!class_exists('Rain\Tpl')) {
	exit;
}?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="/images/favicon.ico">
	<link rel="icon" href="/images/favicon.ico">
	<link rel="favicon" href="/images/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="stylesheet" type="text/css" href="/templates/default/panel_style.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<!--    <script type="text/javascript" src="../jQuery.js"></script>-->
	<!--<script type="text/javascript" src="rmvAds.js"></script>-->
	<title><?php echo $title; ?></title>
</head>
<body>
<div class="container">
	<div class="content-wrapper">
		<div class="content">
			<h1>Logowanie</h1>

			<form action="/login/login" method="post">
				<table>
					<tr>
						<td><label for="login">Login:</label></td>
						<td><input type="text" id="login" name="login"></td>
					</tr>
					<tr>
						<td><label for="password">Lol:</label></td>
						<td><input type="password" id="password" name="password"></td>
					</tr>
					<tr>
						<td><label>Potwierdź:</label></td>
						<td><input type="submit"></td>
					</tr>
				</table>
			</form>
			<p>

				<?php if (isset($result)) { ?>
					RESULT:
					<?php echo $result; ?>
				<?php } ?>
			</p>
		</div>
	</div>

</div>
</body>
</html>