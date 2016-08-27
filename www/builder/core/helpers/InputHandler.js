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
				if (actions.has('focus')) input.addEventListener('focus', onEvent.bind(null, index, 'focus'), false);
				if (actions.has('blur')) input.addEventListener('blur', onEvent.bind(null, index, 'blur'), false);				
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
		callSubscriber(index, 'input', value);		
		if (keyName && isFunction(cb[keyName])) callSubscriber(index, keyName, value);
		else if (isFunction(cb[keyCode])) callSubscriber(index, keyCode, value);
		
	};
	var getKeyName = function(keyCode) {
		return ({'13': 'enter', '27': 'esc'})[keyCode];
	};
	var onEvent = function(index, eventName, e) {
		callSubscriber(index, eventName, e.target.value);
	};
	var callSubscriber = function(index, eventName, value) {
		var s = subscribers[index];
		var cb = Objects.get(options[index]['callbacks'], eventName);
		if (isFunction(cb)) cb.call(s, value);
		if (isString(eventName)) s.dispatchEvent(eventName, value);
	}; 
}
InputHandler = new InputHandler();