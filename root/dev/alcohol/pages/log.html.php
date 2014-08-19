<?php
	require_once R . '/model/log.php';
?>
<h2>MAIN log:</h2>
<code>
	<?php
		Log::cut();
		echo nl2br(Log::read());
	?>
</code>
