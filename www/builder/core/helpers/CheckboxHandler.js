function CheckboxHandler() {
	var subscribers = [];
	var options = [];
	var defaultCheckboxClass = '->> app-ui-checkbox';
	var defaultCheckboxCheckedClass = '->> checked';
	var currentOptions, currentObject,
		currentClasses, currentCheckedClass, currentTarget;

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
		currentClasses = currentTarget.getClasses();
		if (isProperTarget()) {
			defineOptions(index);
			defineCheckedClass();			
			var checked = isChecked();
			currentTarget.toggleClass(currentCheckedClass, checked);
			currentOptions['callback'].call(currentObject, {
				'target': currentTarget,
				'value': getValue(),
				'checked': checked,
				'intChecked': checked ? 1 : 0
			});
		}
	};
	var defineOptions = function(index) {
		currentOptions = options[index];
		currentObject = subscribers[index];
	};
	var isProperTarget = function() {
		var checkboxClass = Objects.get(currentOptions, 'checkboxClass', defaultCheckboxClass);
		var is = currentClasses.contains(checkboxClass);
		return is || isLabelTarget();
	};
	var isLabelTarget = function() {
		var labelClass = Objects.get(currentOptions, 'labelClass');
		if (isString(labelClass)) {
			return currentClasses.contains(labelClass);
		}
		return false;
	};
	var defineCheckedClass = function() {
		currentCheckedClass = Objects.get(currentOptions, 'checkboxCheckedClass', defaultCheckboxCheckedClass);
	};
	var isChecked = function() {
		return !currentClasses.contains(currentCheckedClass);
	};
	var getValue = function() {
		var value = currentTarget.getData('value');
		return isIntValue() ? ~~value : value;
	};
	var isIntValue = function() {
		return Objects.has(currentOptions, 'intValue', true);
	};
}
CheckboxHandler = new CheckboxHandler();