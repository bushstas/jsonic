function Dictionary() {
	var onLoadCallback;
	var items = {};
	this.load = function(callback) {
		var url = __DICTURL;
		if (!isNone(url)) {
			onLoadCallback = callback;
			var request = new AjaxRequest(url, onLoad.bind(this));
			request.send('GET');
		} else {
			callback();
		}
	};
	this.get = function(key, defaultValue) {
		return Objects.get(items, key, defaultValue);
	};
	this.set = function(key, value) {
		items[key] = value;
	};
	var onLoad = function(data) {
		if (isObject(data)) {
			items = data;
		}
		onLoadCallback();
	};
}
Dictionary = new Dictionary();
var __D = Dictionary;
