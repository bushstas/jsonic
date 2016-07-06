function ClickHandler() {
	var subscribers = [];
	var eventHandler = new EventHandler();
	this.subscribe = function(subscriber, options) {
		if (subscribers.indexOf(subscriber) == -1) {
			eventHandler.listen(subscriber.getElement(), 'click', onClick.bind(null, options, subscriber));
			subscribers.push(subscriber);
		}
	};
	this.unsubscribe = function(subscriber) {
		var idx = subscribers.indexOf(subscriber);
		if (idx > -1) {
			eventHandler.unlisten(subscriber.getElement(), 'click');
			subscribers.splice(idx, 1);
		}
	};
	var onClick = function(options, subscriber, e) {
		var target;
		for (var k in options) {
			target = e.getTargetWithClass(k);
			if (target) {
				if (isFunction(options[k])) {
					options[k].call(subscriber, e, target);
					break;
				}
			}
		}
	};
}
ClickHandler = new ClickHandler();