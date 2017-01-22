function MouseHandler() {
	var subscribers = [];
	var options = [];
	var eventHandler = new EventHandler();
	var extendOptions = function(index, opts) {
		Objects.merge(options[index], opts);
	};
	this.subscribe = function(subscriber, opts) {
		var index = subscribers.indexOf(subscriber);
		if (index == -1) {
			options.push(opts);
			eventHandler.listen(subscriber.getElement(), 'click', onClick.bind(null, subscriber));
			subscribers.push(subscriber);
		} else extendOptions(index, opts);
	};
	this.unsubscribe = function(subscriber) {
		var idx = subscribers.indexOf(subscriber);
		if (idx > -1) {
			eventHandler.unlisten(subscriber.getElement(), 'click');
			subscribers.splice(idx, 1);
		}
	};
	var onClick = function(subscriber, e) {
		var index = subscribers.indexOf(subscriber);
		var opts = options[index];
		var target;
		for (var k in opts) {
			target = e.getTargetWithClass(k, true);
			if (target) {
				if (isFunction(opts[k])) {
					opts[k].call(subscriber, target, e);
					e.stopPropagation();
					break;
				}
			}
		}
	};
}
var ClickHandler = new MouseHandler();
var MouseHandler = ClickHandler;