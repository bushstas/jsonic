function ClickHandler() {
	var subscribers = [];
	var eventHandler = new EventHandler();
	this.subscribe = function(subscriber, options) {
		if (subscribers.indexOf(subscriber) == -1) {
			var element = subscriber.getElement();
			eventHandler.listen(element, 'click', onClick.bind(null, options, subscriber));
			subscribers.push(subscriber);
		}
	};
	var onClick = function(options, subscriber, e) {
		for (var k in options) {
			if (e.targetHasClass(k)) {
				if (isFunction(options[k])) {
					options[k].call(subscriber, e);
					break;
				}
			}
		}
	};
}
ClickHandler = new ClickHandler();