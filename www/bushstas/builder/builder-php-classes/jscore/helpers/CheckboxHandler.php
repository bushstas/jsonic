<?php

	$data = array(
		'name' => 'CheckboxHandler',
		'mode' => 2,
		'before' => "
			var subscribers = [];
			var options = [];
			var defaultCheckboxClass = '->>checkbox';
			var defaultCheckboxCheckedClass = '->>checked';
			var currentOptions, currentObject, currentScope, checkbox, labelClass,
				checkboxClass, targetClasses, currentCheckedClass, currentTarget;
		",
		'privateMethods' => array(
			'onClick' => array(
				'args' => array('index', 'e'),
				'body' => "
					currentTarget = e.target;
					defineOptions(index);
					defineTargetClasses();
					if (isProperTarget()) {
						defineCheckbox();
						defineCheckedClass();			
						var checked = !isChecked();
						if (currentTarget) currentTarget.toggleClass(currentCheckedClass, checked);
						if (checkbox) {
							checkbox.toggleClass(currentCheckedClass, checked);
							currentOptions['callback'].call(currentObject, {
								'target': checkbox,
								'name': getName(),
								'value': getValue(),
								'checked': checked,
								'intChecked': checked ? 1 : 0
							});
						}
					}
				"
			),
			'defineOptions' => array(
				'args' => array('index'),
				'body' => "
					currentOptions = options[index];
					currentObject = subscribers[index];
					currentScope = currentObject.getElement();
				"
			),
			'defineTargetClasses' => array(
				'body' => "
					targetClasses = [];
					defineCheckboxClass();
					if (checkboxClass) targetClasses.push(checkboxClass);
					labelClass = ".CONST_OBJECTS.".get(currentOptions, 'labelClass');
					if (isString(labelClass)) targetClasses.push(labelClass);
					else if (isArray(labelClass)) targetClasses = targetClasses.concat(labelClass);					
				"
			),
			'defineCheckboxClass' => array(
				'args' => array('options'),
				'body' => "
					checkboxClass = ".CONST_OBJECTS.".get(options || currentOptions, 'checkboxClass', defaultCheckboxClass);
				"
			),
			'isProperTarget' => array(
				'body' => "
					while (currentTarget) {
						if (targetClasses.hasIntersections(currentTarget.getClasses())) return true;
						currentTarget = currentTarget.parentNode;
						if (currentTarget == currentScope) break;
					}
					return false;
				"
			),
			'defineCheckbox' => array(
				'body' => "
					if (currentTarget.hasClass(checkboxClass)) {
						checkbox = currentTarget;
						currentTarget = null;
						if (isString(labelClass)) currentTarget = checkbox.getAncestor('.' + labelClass);
						else if (isArray(labelClass))  {
							for (var i = 0; i < labelClass.length; i++) {
								currentTarget = checkbox.getAncestor('.' + labelClass[i]);
								if (currentTarget) break;
							}
						}
					} else checkbox = currentTarget.find('.' + checkboxClass);					
				"
			),
			'defineCheckedClass' => array(
				'body' => "
					currentCheckedClass = ".CONST_OBJECTS.".get(currentOptions, 'checkboxCheckedClass', defaultCheckboxCheckedClass);
				"
			),
			'isChecked' => array(
				'body' => "
					if (checkbox) return checkbox.hasClass(currentCheckedClass);
					return currentTarget.hasClass(currentCheckedClass);
				"
			),
			'getValue' => array(
				'body' => "
					var value;
					if (checkbox) value = checkbox.getData('value');
					return isIntValue() ? ~~value : value;
				"
			),
			'getName' => array(
				'args' => array(''),
				'body' => "
					if (checkbox) return checkbox.getData('name');
				"
			),
			'isIntValue' => array(
				'body' => "
					return ".CONST_OBJECTS.".has(currentOptions, 'intValue', true);
				"
			),
			'getOptionsOfSubscriber' => array(
				'args' => array('subscriber'),
				'body' => "
					return options[subscribers.indexOf(subscriber)];
				"
			),
			'getCheckboxByName' => array(
				'args' => array('name', 'subscriber'),
				'body' => "
					currentOptions = getOptionsOfSubscriber(subscriber);
					defineCheckboxClass();
					return subscriber.findElement('.' + checkboxClass + '[_name=\"' + name + '\"]');
				"
			)
		),
		'thisMethods' => array(
			'subscribe' => array(
				'args' => array('subscriber', 'opts'),
				'body' => "
					if (isFunction(opts['callback']) && subscribers.indexOf(subscriber) == -1) {
						subscribers.push(subscriber);
						options.push(opts || null);
						var element = subscriber.getElement();
						if (element) {
							var index = subscribers.length - 1;
							element.addEventListener('click', onClick.bind(null, index), false);
						}
					}
				"
			),
			'isChecked' => array(
				'args' => array('name', 'subscriber'),
				'body' => "
					var checkbox = getCheckboxByName(name, subscriber);
					defineCheckedClass();
					return checkbox && checkbox.hasClass(currentCheckedClass);
				"
			),
			'getValue' => array(
				'args' => array('name', 'subscriber'),
				'body' => "
					var checkbox = getCheckboxByName(name, subscriber);
					if (checkbox) return checkbox.getData('value');
				"
			)
		)
	);
?>