controller RecommendationsLoader

initial actions = {
	'load': {
		'url': CONFIG.keywords.recommendations,
		'method': 'POST',
		'autoset': {
			'data': 'items'
		}
	}
};