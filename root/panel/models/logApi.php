<h2>Api RQ Log:</h2>
<code>
	<?php
		Log::cut();
		echo nl2br(Log::read(Log::$API_LOG));
	?>
</code>
