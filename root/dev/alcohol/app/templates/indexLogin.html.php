<? include R . '/templates/header.html.php'; ?>
	<h1>Logowanie</h1>
	<form action="login/login" method="post">
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
<? include R . '/templates/footer.html.php'; ?>