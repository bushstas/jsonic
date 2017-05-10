controller Subscription

initial actions = {
	'load': {
		'url': Api.settings.subscr,
		'method': 'GET',
		'autoset': {
			'options': 'opts'
		}
	},
	'save': {
		'url': Api.settings.set,
		'method': 'GET'
	}
};