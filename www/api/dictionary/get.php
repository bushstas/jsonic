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
		),
		'timeOptions' => array(
			array('value' => '10_13', 'title' => 'с 7.00 - 11.00 мск'),
			array('value' => '13_16', 'title' => 'с 11.00 - 13.00 мск'),
			array('value' => '16_19', 'title' => 'с 13.00 - 16.00 мск')
		),
		'monthNames' => array(
			'1'  => 'января',
			'2'  => 'февраля',
			'3'  => 'марта',
			'4'  => 'апреля',
			'5'  => 'мая',
			'6'  => 'июня',
			'7'  => 'июля',
			'8'  => 'августа',
			'9'  => 'сентября',
			'10' => 'октября',
			'11' => 'ноября',
			'12' => 'декабря'
		),
		'dayNames' => array(
			'', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт'
		)
	);

	die(json_encode($dictionary));
?>