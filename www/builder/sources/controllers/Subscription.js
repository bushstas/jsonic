controller Subscription

initial actions = {
	'load': {
		'url': CONFIG.settings.subscr,
		'method': 'GET',
		'autoset': {
			'options': 'opts'
		}
	},
	'save': {
		'url': CONFIG.settings.set,
		'method': 'GET'
	}
};