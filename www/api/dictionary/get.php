<?php
	
	$dictionary = array(
		'orderCallTopics' => array(
			array('value' => 'tariff',  'title' => 'Вопрос по тарифам'),
			array('value' => 'doc',     'title' => 'Документооборот'),
			array('value' => 'serv',    'title' => 'Дополнительные услуги'),
			array('value' => 'account', 'title' => 'Заказ/Продление доступа'),
			array('value' => 'tender',  'title' => 'Заявка на учатие в тендере'),
			array('value' => 'ecp',     'title' => 'Заказ ЭЦП'),
			array('value' => 'skype',   'title' => 'Скайп презентация'),
			array('value' => 'other',   'title' => 'Другое')
		)
	);

	die(json_encode($dictionary));
?>