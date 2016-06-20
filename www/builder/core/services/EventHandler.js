function EventHandler() {
	var listeners = [];
	this.listen = function(element, type, handler) {
		listeners.push([element, type, handler]);
		element.addEventListener(type, handler, false);
	};
	this.listenOnce = function(element, type, handler) {
		var callback = function() {
			handler();
			element.removeEventListener(type, callback, false);
		}
		element.addEventListener(type, callback, false);
	};
	this.unlisten = function(element, type) {
		var listener;
		for (var i = 0; i < listeners.length; i++) {
			listener = listeners[i];
			if (listener && listener[0] == element && listener[1] == type) {
				listener[0].removeEventListener(listener[1], listener[2], false);
				listeners[i] = null;
			}
		}
	};
	this.dispose = function() {
		var listener;
		for (var i = 0; i < listeners.length; i++) {
			listener = listeners[i];
			if (listener) {
				listener[0].removeEventListener(listener[1], listener[2], false);
			}
		}
		listeners = null;
	};
}