controller Subscription

initial actions = {
	'load': {
		'url': CONFIG.settings.subscr,
		'method': 'GET'
	},
	'save': {
		'url': CONFIG.settings.set,
		'method': 'GET'
	}
};