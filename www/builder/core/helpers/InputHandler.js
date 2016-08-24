function InputHandler() {
	var subscribers = [];
	var options = [];	
	this.subscribe = function(subscriber, opts) {
		if (isObject(opts['callbacks']) && isString(opts['inputSelector']) && subscribers.indexOf(subscriber) == -1) {
			var input = subscriber.findElement(opts['inputSelector']);
			var actions = Objects.getKeys(opts['callbacks']);
			if (input) {
				opts['input'] = input;
				subscribers.push(subscriber);
				options.push(opts);
				var index = subscribers.length - 1;
				if (actions.hasExcept('focus', 'blur')) input.addEventListener('keyup', onInput.bind(null, index), false);
				if (actions.has('focus')) input.addEventListener('focus', onFocus.bind(null, index), false);
				if (actions.has('blur')) input.addEventListener('blur', onBlur.bind(null, index), false);				
			}
		}
	};
	var onInput = function(index, e) {
		var keyCode = e.keyCode;
		var keyName = getKeyName(keyCode);
		var opts = options[index];
		var subscriber = subscribers[index];
		var cb = opts['callbacks'];
		var value = e.target.value;
		if (keyName && isFunction(cb[keyName])) cb[keyName].call(subscriber, value, e);
		else if (isFunction(cb[keyCode])) cb[keyCode].call(subscriber, value, e);
		if (isFunction(cb['input'])) cb['input'].call(subscriber, value, e);
	};
	var getKeyName = function(keyCode) {
		return ({'13': 'enter', '27': 'esc'})[keyCode];
	};
	var onFocus = function(index, e) {
		options[index]['callbacks']['focus'].call(subscribers[index]);
	};
	var onBlur = function(index, e) {
		options[index]['callbacks']['blur'].call(subscribers[index]);
	};
}
InputHandler = new InputHandler();