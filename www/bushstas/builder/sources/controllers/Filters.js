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
		'url'     : Api.filters.load,
		'method'  : 'GET',
		'callback': this.onLoad
	},
	'save': {
		'url'     : Api.filters.save,
		'method'  : 'POST',
		'callback': this.onAdd
	},
	'set': {
		'url'   : Api.filters.set,
		'method': 'POST'
	},
	'subscribe': {
		'url'   : Api.filters.subscribe,
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