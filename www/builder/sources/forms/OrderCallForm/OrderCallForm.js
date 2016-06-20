form OrderCallForm

initial options = {
	'action': 'orderCall/send.php',
	'method': 'POST',
	'container': 'app-order-call',
	'controls': [
		{
			'cmpid': 'name',
			'type': 'text',
			'name': 'name',
			'caption': @contactName,
			'class': 'half-width'
		},
		{
			'cmpid': 'phone',
			'type': 'text',
			'name': 'phone',
			'caption': @contactPhone,
			'class': 'half-width'
		}
	],
	'submit': {
		'value': @orderCall,
		'class': 'standart-button green-button send-button'
	}
};