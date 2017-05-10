controller FiltersStat

initial options = {
	'key': 'filterId',
	'store': false,
	'storeAs': 'filterStat_$filterId',
	'storePeriod': '4hour'
}

initial actions = {
	'load': {
		'url': Api.filterStat.load,
		'method': 'GET'
	}
}
