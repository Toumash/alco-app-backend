<? include 'templates/header.html.php'; ?>

	<h1>Dodaj artykuł</h1>
	<form action="?task=articles&amp;action=insert" method="post">
		Tytuł: <input type="text" name="title"/><br/>
		Autor: <input type="text" name="author"/><br/>
		Data dodania: <input type="text" name="date_add" value="<?= date("Y:m:d"); ?>"/><br/>
		Treść:<br/>
		<textarea name="content"></textarea><br/>
		Kategoria: <select name="cat" size="0">
			<? foreach ($this->get('catsData') as $cats) { ?>
				<option value="<?= $cats['id']; ?>"><?= $cats['name']; ?></option>
			<? } ?>
		</select><br/>
		<input type="submit" value="Dodaj"/>
	</form>

<? include 'templates/footer.html.php'; ?>