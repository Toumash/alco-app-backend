<? include R . '/templates/header.html.php'; ?>
	<h1>Dodaj kategorię</h1>
	<form action="?task=categories&action=insert" method="post">Nazwa kategorii: <input type="text" name="name"/>
		<input type="submit" value="Dodaj"/></form>

<? include R . '/templates/footer.html.php'; ?>