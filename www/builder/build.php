<?php
	
	include_once __DIR__.'/init.php';
	include_once FOLDER.'/header.php';
	include_once FOLDER.'/builder.core.php';

	if (IS_TEST) {
		die('<script>window.location.href = "http://'.$_SERVER['HTTP_HOST'].'/test_index.html"</script>');
	}
	include_once FOLDER.'/footer.php';

?>