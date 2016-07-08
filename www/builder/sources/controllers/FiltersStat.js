controller FiltersStat

initial options = {
	'key': 'filterId',
	'store': true,
	'storeAs': 'filterStat_$filterId',
	'storePeriod': '4hour'
};

initial actions = {
	'load': {
		'url'     : CONFIG.filterStat.load,
		'method'  : 'GET',
		'callback': this.onLoad.bind(this)
	}
};

function onLoad(data) {
	
}