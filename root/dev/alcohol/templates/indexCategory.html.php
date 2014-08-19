<? include R . '/templates/header.html.php'; ?>
	<h1>Lista kategorii</h1>

	<table>
		<tbody>
		<? foreach ($this->get('catsData') as $cats) { ?>
			<tr>
				<td>Nazwa</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><a href="?module=categories&action=delete&id=<?php echo $cats['id']; ?>"><?php echo $cats['name'] ?>
						usuń</a></td>
			</tr>
		<? } ?>
		</tbody>
	</table>
<? include R . '/templates/footer.html.php'; ?>