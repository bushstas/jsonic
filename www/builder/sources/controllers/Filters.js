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
		'callback': this.onLoad
	},
	'save': {
		'url'     : CONFIG.filters.save,
		'method'  : 'POST',
		'callback': this.onAdd
	},
	'set': {
		'url'   : CONFIG.filters.set,
		'method': 'POST'
	},
	'subscribe': {
		'url'   : CONFIG.filters.subscribe,
		'method': 'POST',
		'callback': this.onSubscribe
	}
};

function onLoadFilters(data) {
	
}


function onLoad(data) {
	
}

function onAdd(data) {
	
}

function onSubscribe() {
	this.load();
}