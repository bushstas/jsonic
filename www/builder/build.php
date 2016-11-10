<?php
		
	$isTest = !empty($_GET['istest']);

	include_once 'builder.php.classes/header.php';
	include_once 'builder.php.classes/builder.core.php';

	if ($isTest) {
		die('<script>window.location.href = "http://'.$_SERVER['HTTP_HOST'].'/test_index.html"</script>');
	}
	include_once 'builder.php.classes/footer.php';

?>