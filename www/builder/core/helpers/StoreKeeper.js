function StoreKeeper() {
	var prefix = 'stored_';
	var secondsInMeasures = {
		'month': 2592000,
		'day'  : 86400,
		'hour' : 3600,
		'min'  : 60
	};
	this.set = function(key, value) {
		var localStorageKey = getLocalStorageKey(key);
		var item = JSON.stringify({
			'data': value,
			'timestamp': (window['Date']['now']()).toString()
		});
		window.localStorage.setItem(localStorageKey, item);
	};
	this.get = function(key) {
		var item = getItem(key);
		return Objects.has(item, 'data') ? item['data'] : null;
	};
	this.getActual = function(key, period) {
		var item = getItem(key);
		return Objects.has(item, 'data') && isActual(item['timestamp'], period) ? item['data'] : null;
	};
	this.remove = function(key) {
		var localStorageKey = getLocalStorageKey(key);
		window.localStorage.removeItem(localStorageKey);
	};
	var isActual = function(savedMilliseconds, period) {
		var nowMilliseconds    = window['Date']['now'](),
			periodMilliseconds = getMilliseconds(period);
		if (isString(savedMilliseconds)) {
			savedMilliseconds = stringToNumber(savedMilliseconds);
		}
		return periodMilliseconds && savedMilliseconds && nowMilliseconds - savedMilliseconds < periodMilliseconds;
	};
	var getItem = function(key) {
		var localStorageKey = getLocalStorageKey(key);
		var item = window.localStorage.getItem(localStorageKey);
		if (!item) return null;
		try {
			item = JSON.parse(item);
		} catch (exception) {
			log('Json parse exception', 'getItem', this, {'item': item});
			return null;	
		}
		return item;
	};
	var getMilliseconds = function(period) {
		var periodNumber  = ~~period.replace(/[^\d]/g, '');
		var periodMeasure =   period.replace(/\d/g, '');

		if (!periodNumber) {
			log('Given period number is empty', 'getMilliseconds', this);
			return 0;		
		}
		if (!secondsInMeasures[periodMeasure]) {
			log('No given measure: ' + periodMeasure, 'getMilliseconds', this);
			return 0;
		}
		return secondsInMeasures[periodMeasure] * periodNumber * 1000;
	};
	var getLocalStorageKey = function(key) {
		return prefix + key;
	};
}
StoreKeeper = new StoreKeeper();