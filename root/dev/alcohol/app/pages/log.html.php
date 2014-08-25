<?php
	require_once R . '/model/log.php';
?>
<h2>MAIN log:</h2>
<code>
	<?php
		LogModel::cut();
		echo nl2br(LogModel::read());
	?>
</code>
