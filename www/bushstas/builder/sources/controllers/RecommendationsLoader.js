controller RecommendationsLoader

initial actions = {
	'load': {
		'url': Api.keywords.recommendations,
		'method': 'POST',
		'autoset': {
			'data': 'items'
		}
	}
};