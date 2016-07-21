function CheckboxHandler() {
	var subscribers = [];
	var options = [];
	var defaultCheckboxClass = '->> app-ui-checkbox';
	var defaultCheckboxCheckedClass = '->> checked';
	var currentOptions, currentObject, currentScope, checkbox,
		checkboxClass, targetClasses, currentCheckedClass, currentTarget;
	this.subscribe = function(subscriber, opts) {
		if (isFunction(opts['callback']) && subscribers.indexOf(subscriber) == -1) {
			subscribers.push(subscriber);
			options.push(opts || null);
			var element = subscriber.getElement();
			if (element) {
				var index = subscribers.length - 1;
				element.addEventListener('click', onClick.bind(null, index), false);
			}
		}
	};
	var onClick = function(index, e) {
		currentTarget = e.target;
		defineOptions(index);
		defineTargetClasses();
		if (isProperTarget()) {
			defineCheckbox();
			defineCheckedClass();			
			var checked = !isChecked();
			currentTarget.toggleClass(currentCheckedClass, checked);
			if (checkbox) checkbox.toggleClass(currentCheckedClass, checked);
			currentOptions['callback'].call(currentObject, {
				'target': currentTarget,
				'name': getName(),
				'value': getValue(),
				'checked': checked,
				'intChecked': checked ? 1 : 0
			});
		}
	};
	var defineOptions = function(index) {
		currentOptions = options[index];
		currentObject = subscribers[index];
		currentScope = currentObject.getElement();
	};
	var defineTargetClasses = function() {
		targetClasses = [];
		defineCheckboxClass();
		if (checkboxClass) targetClasses.push(checkboxClass);
		var labelClass = Objects.get(currentOptions, 'labelClass');
		if (isString(labelClass)) targetClasses.push(labelClass);
		else if (isArray(labelClass)) targetClasses = targetClasses.concat(labelClass);
	};
	var defineCheckboxClass = function(options) {
		checkboxClass = Objects.get(options || currentOptions, 'checkboxClass', defaultCheckboxClass);
	};
	var isProperTarget = function() {
		while (currentTarget) {
			if (targetClasses.hasIntersections(currentTarget.getClasses())) return true;
			currentTarget = currentTarget.parentNode;
			if (currentTarget == currentScope) break;
		}
		return false;
	};
	var defineCheckbox = function() {
		checkbox = currentTarget.find('.' + checkboxClass);
	};
	var defineCheckedClass = function() {
		currentCheckedClass = Objects.get(currentOptions, 'checkboxCheckedClass', defaultCheckboxCheckedClass);
	};
	var isChecked = function() {
		if (checkbox) return checkbox.hasClass(currentCheckedClass);
		return currentTarget.hasClass(currentCheckedClass);
	};
	var getValue = function() {
		var value;
		if (checkbox) value = checkbox.getData('value');
		return isIntValue() ? ~~value : value;
	};
	var getName = function() {
		if (checkbox) return checkbox.getData('name');
	};
	var isIntValue = function() {
		return Objects.has(currentOptions, 'intValue', true);
	};
	var getOptionsOfSubscriber = function(subscriber) {
		return options[subscribers.indexOf(subscriber)];
	};
	var getCheckboxByName = function(name, subscriber) {
		currentOptions = getOptionsOfSubscriber(subscriber);
		defineCheckboxClass();
		return subscriber.findElement('.' + checkboxClass + '[_name="' + name + '"]');
	};
	this.isChecked = function(name, subscriber) {
		var checkbox = getCheckboxByName(name, subscriber);
		defineCheckedClass();
		return checkbox && checkbox.hasClass(currentCheckedClass);
	};
	this.getValue = function(name, subscriber) {
		var checkbox = getCheckboxByName(name, subscriber);
		if (checkbox) return checkbox.getData('value');
	};
}
CheckboxHandler = new CheckboxHandler();