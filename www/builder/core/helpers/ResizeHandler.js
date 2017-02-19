{{GLOBAL}}.set(function() {
	var subscribers = [], timer;	
	this.subscribe = function(subscriber, options) {
		subscribers.push([subscriber, options]);
	};
	var onResize = function() {
		window.clearTimeout(timer);
		timer = window.setTimeout(function() {
			for (var i = 0; i < subscribers.length; i++) {
				var callback = Objects.get(subscribers[i][1], 'callback');
				if (isFunction(callback)) callback.call(subscribers[i][0]);
			}
		}, 200);
	};
	window.addEventListener('resize', onResize, false);
}, 'ResizeHandler');