<h2>Mała galeria naszego projektu</h2>
<div class="images">
	<?php
		$dirf = WR . '/images/demo/';
		$dir = scandir($dirf);
		foreach ($dir as $file) {
			if ($file != '.' && $file != '..' && $file[1] != 'h' && $file[2] != 't') {
				echo '<a href="/alcohol/images/demo/' . $file . '"><img  src="/alcohol/images/demo/' . $file . '"/></a>';
			}
		}
	?>
</div>