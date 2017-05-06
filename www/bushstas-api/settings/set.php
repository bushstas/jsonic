<?php 

	$data = json_decode(file_get_contents('data.json'), true);


	foreach ($_REQUEST as $key => $value) {
		switch ($key) {
			case 'tenderOfFavorite':
				$data['options']['tenderOfFavorite'] = (int)$value;
				break;

			case 'protocolOfFavorite':
				$data['options']['protocolOfFavorite'] = (int)$value;
				break;
			
			case 'protocolOfFilter':
				$data['options']['protocolOfFilter'] = (int)$value;
				break;

			default:
				# code...
				break;
		}
	}

	file_put_contents('data.json', json_encode($data));