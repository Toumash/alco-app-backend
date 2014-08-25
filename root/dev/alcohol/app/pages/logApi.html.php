<?php
	require_once R . '/model/log.php';
?>
<h2>Api RQ Log:</h2>
<code>
	<?php
		LogModel::cut();
		echo nl2br(LogModel::read(LogModel::$API_LOG));
	?>
</code>
