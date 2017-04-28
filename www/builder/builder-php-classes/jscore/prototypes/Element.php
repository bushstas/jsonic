<?php

	$data = array(
		'mode' => 4,
		'prototypeOf' => 'Element',
		'before' => "
			var cache = {};
		",
		'methods' => array(
			'setClass' => array(
				'args' => array('className'),
				'body' => "
					this.className = className.trim();
				"
			),
			'toggleClass' => array(
				'args' => array('className', 'isAdding'),
				'body' => "
					if (isUndefined(isAdding)) {
						isAdding = !this.hasClass(className);
					}
					if (isAdding) {
						this.addClass(className);
					} else {
						this.removeClass(className);
					}
				"
			),
			'switchClasses' => array(
				'args' => array('className1', 'className2'),
				'body' => "
					var classes = this.getClasses();
					if (classes.has(className1)) { 
						this.removeClass(className1);
						this.addClass(className2);
					} else if (classes.has(className2)) {
						this.removeClass(className2);
						this.addClass(className1);
					}
				"
			),
			'addClass' => array(
				'args' => array('className'),
				'body' => "
					if (isString(className)) {
						var classNames = this.getClasses();
						var addedClasses = className.split(' ');
						for (var i = 0; i < addedClasses.length; i++) {
							if (classNames.indexOf(addedClasses[i]) == -1) {
								classNames.push(addedClasses[i]);
							}
						}
						this.className = classNames.join(' ');
					}
				"
			),
			'removeClass' => array(
				'args' => array('className'),
				'body' => "
					if (isString(className)) {
						var classNames = this.getClasses();
						var removedClasses = className.split(' ');
						var newClasses = [];
						for (var i = 0; i < classNames.length; i++) {
							if (removedClasses.indexOf(classNames[i]) == -1) {
								newClasses.push(classNames[i]);
							}
						}
						this.className = newClasses.join(' ');
					}
				"
			),
			'hasClass' => array(
				'args' => array('className'),
				'body' => "
					return this.getClasses().has(className);
				"
			),
			'getClasses' => array(
				'body' => "
					if (!this.className) return [];
					var classNames = this.className.trim().replace(/ {2,}/g, ' ');
					return classNames.split(' ');
				"
			),
			'getAncestor' => array(
				'args' => array('selector'),
				'body' => "
					if (isNone(selector) || !isString(selector)) {
						return null;
					}
					if (isFunction(this.closest)) {
						return this.closest(selector);
					}
					var parts = selector.trim().split(' ');
					var properSelector = parts[parts.length - 1];
					var classes = properSelector.split('.');
					var selectorTag;
					var thisTag = this.tagName.toLowerCase();
					if (!isNone(classes[0])) {
						selectorTag = classes[0].toLowerCase();
					}
					Objects.removeAt(classes, 0);
					var element = this, isSameTag, foundClasses, elementClasses;
					while (element) {
						elementClasses = element.getClasses();
						isSameTag = isUndefined(selectorTag) || selectorTag == thisTag;
						foundClasses = 0;
						for (var i = 0; i < elementClasses.length; i++) {
							if (classes.indexOf(elementClasses[i]) > -1) {
								foundClasses++;
							}
						}
						if (foundClasses == classes.length && isSameTag) {
							return element;
						}
						element = element.parentNode;
					}
					return null;
				"
			),
			'getData' => array(
				'args' => array('name'),
				'body' => "
					return this.getAttribute('_' + name) || '';
				"
			),
			'setData' => array(
				'args' => array('name', 'value'),
				'body' => "
					this.setAttribute('_' + name, value);
				"
			),
			'getRect' => array(
				'body' => "
					return this.getBoundingClientRect();
				"
			),
			'setWidth' => array(
				'args' => array('width'),
				'body' => "
					this.style.width = isNumber(width) ? width + 'px' : width;
				"
			),
			'setHeight' => array(
				'args' => array('height'),
				'body' => "
					this.style.height = isNumber(height) ? height + 'px' : height;
				"
			),
			'getWidth' => array(
				'body' => "
					return this.getRect().width;
				"
			),
			'getHeight' => array(
				'body' => "
					return this.getRect().height;
				"
			),
			'getTop' => array(
				'body' => "
					return this.getRect().top;
				"
			),
			'getLeft' => array(
				'body' => "
					return this.getRect().left;
				"
			),
			'css' => array(
				'args' => array('style'),
				'body' => "
					var element = this;
					var set = function(value, style) {
						var propertyName = getVendorJsStyleName(style);	
						if (propertyName) {
							element.style[propertyName] = value;
						}
					};
					var getVendorJsStyleName = function(style) {
						var propertyName = cache[style];
						if (!propertyName) {
							propertyName = toCamelCase(style);
					    	cache[style] = propertyName;
					  	}	
						return propertyName;
					};
					if (typeof style == 'string') {
					    set(value, style);
					} else {
						for (var key in style) {
					  		set(style[key], key);
					   	}
					}
				"
			),
			'getChildAt' => array(
				'args' => array('index'),
				'body' => "
					return this.childNodes[index];
				"
			),
			'attr' => array(
				'args' => array('attrName'),
				'body' => "
					if (!isUndefined(arguments[1])) {
						if (attrName == 'class') {
							this.setClass(arguments[1]);
						} else if (attrName == 'value') {
							this.value = arguments[1];
						} else {
							this.setAttribute(attrName, arguments[1]);
						}
					} else {
						return this.getAttribute(attrName);
					}
				"
			),
			'show' => array(
				'args' => array('isShown'),
				'body' => "
					var display = isString(isShown) ? isShown : (isUndefined(isShown) || isShown ? 'block' : 'none');
					this.style.display = display;
				"
			),
			'hide' => array(
				'body' => "
					this.show(false);
				"
			),
			'find' => array(
				'args' => array('selector'),
				'body' => "
					return this.querySelector(selector);
				"
			),
			'finds' => array(
				'args' => array('selector'),
				'body' => "
					return this.querySelectorAll(selector);
				"
			),
			'getParent' => array(
				'body' => "
					return this.parentNode;
				"
			),
			'scrollTo' => array(
				'args' => array('pxy', 'duration'),
				'body' => "
					if (isElement(pxy)) pxy = pxy.getRelativePosition(this).y;
					if (!duration || !isNumber(duration)) this.scrollTop = pxy;
					else {
						var px = pxy - this.scrollTop, ratio = 15,
						steps = duration / ratio, step = Math.round(px / steps),
						currentStep = 0, e = this, 
						cb = function() {
							currentStep++;
							e.scrollTop = e.scrollTop + step;
							if (currentStep < steps) setTimeout(cb, ratio);
							else e.scrollTop = pxy;
						};
						if (px != 0) cb();
					}
				"
			),
			'getRelativePosition' => array(
				'args' => array('element'),
				'body' => "
					var a = this.getRect();
					var b = element.getRect();
					return {x: Math.round(a.left - b.left + element.scrollLeft), y:  Math.round(a.top - b.top + element.scrollTop)};
				"
			),
			'clear' => array(
				'body' => "
					if (isString(this.value)) this.value = '';
					else this.innerHTML = '';
				"
			),
			'prev' => array(
				'body' => "
					return this.previousSibling;
				"
			),
			'next' => array(
				'body' => "
					return this.nextSibling;
				"
			)
		)
	);
?>