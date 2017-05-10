controller UserInfoLoader

initial actions = {
	'load': {
		'url'     : Api.user.get,
		'method'  : 'GET'
	}
};