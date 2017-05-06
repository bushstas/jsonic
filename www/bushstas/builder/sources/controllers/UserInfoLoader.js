controller UserInfoLoader

initial actions = {
	'load': {
		'url'     : CONFIG.user.get,
		'method'  : 'GET'
	}
};