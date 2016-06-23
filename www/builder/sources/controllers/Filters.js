controller Filters

initial options = {
	'key': 'filterId',
	'store': false,
	'storeAs': 'filters',
	'storePeriod': '1day',
	'clone': true
};

initial actions = {
	'load': {
		'url'     : CONFIG.filters.load,
		'method'  : 'GET',
		'callback': this.onLoad.bind(this)
	},
	'save': {
		'url'     : CONFIG.filters.save,
		'method'  : 'POST',
		'callback': this.onAdd
	},
	'set': {
		'url'   : CONFIG.filters.set,
		'method': 'POST'
	}
};

function onLoadFilters(data) {
	
}


function onLoad(data) {
	
}

function onAdd(data) {

}