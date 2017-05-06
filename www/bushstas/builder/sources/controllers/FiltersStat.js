controller FiltersStat

initial options = {
	'key': 'filterId',
	'store': false,
	'storeAs': 'filterStat_$filterId',
	'storePeriod': '4hour'
}

initial actions = {
	'load': {
		'url': CONFIG.filterStat.load,
		'method': 'GET'
	}
}
