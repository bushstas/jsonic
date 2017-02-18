var __StyleNameCache = {};
_p=Element.prototype;
_p.setClass = function(className) {
	this.className = className.trim();
}
_p.toggleClass = function(className, isAdding) {
	if (isAdding) {
		this.addClass(className);
	} else {
		this.removeClass(className);
	}
};
_p.switchClasses = function(className1, className2) {
	var classes = this.getClasses();
	if (classes.contains(className1)) { 
		this.removeClass(className1);
		this.addClass(className2);
	} else if (classes.contains(className2)) {
		this.removeClass(className2);
		this.addClass(className1);
	}
};
_p.addClass = function(className) {
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
};
_p.removeClass = function(className) {
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
};
_p.hasClass = function(className) {
	return this.getClasses().indexOf(className) > -1;
};
_p.getClasses = function() {
	var classNames = (this.className || '').trim().replace(/ {2,}/g, ' ');
	if (classNames) {
		return classNames.split(' ');
	}
	return [];
};
_p.getAncestor = function(selector) {
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
};
_p.getData = function(name) {
	return this.getAttribute('_' + name) || '';
};
_p.setData = function(name, value) {
	this.setAttribute('_' + name, value);
};
_p.getRect = function() {
	return this.getBoundingClientRect();
};
_p.setWidth = function(width) {
	this.style.width = isNumber(width) ? width + 'px' : width;
};
_p.setHeight = function(height) {
	this.style.height = isNumber(height) ? height + 'px' : height;
};
_p.getWidth = function() {
	return this.getRect().width;
};
_p.getHeight = function() {
	return this.getRect().height;
};
_p.getTop = function() {
	return this.getRect().top;
};
_p.getLeft = function() {
	return this.getRect().left;
};
_p.css = function(style) {
	var element = this;
	var set = function(value, style) {
		var propertyName = getVendorJsStyleName(style);	
		if (propertyName) {
			element.style[propertyName] = value;
		}
	};
	var getVendorJsStyleName = function(style) {
		var propertyName = __StyleNameCache[style];
		if (!propertyName) {
			propertyName = toCamelCase(style);
	    	__StyleNameCache[style] = propertyName;
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
};
_p.getChildAt = function(index) {
	return this.childNodes[index];
};
_p.attr = function(attrName) {
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
};
_p.show = function(isShown) {
	var display = isString(isShown) ? isShown : (isUndefined(isShown) || isShown ? 'block' : 'none');
	this.style.display = display;
};
_p.hide = function() {
	this.show(false);
};
_p.find = function(selector) {
	return this.querySelector(selector);
};
_p.finds = function(selector) {
	return this.querySelectorAll(selector);
};
_p.getParent = function() {
	return this.parentNode;
};
_p.scrollTo = function(pxy, duration) {
	if (isElement(pxy)) pxy = pxy.getRelativePosition(this).y
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
};
_p.getRelativePosition = function(element) {
	var a = this.getRect();
	var b = element.getRect();
	return {x: Math.round(a.left - b.left + element.scrollLeft), y:  Math.round(a.top - b.top + element.scrollTop)};
};
_p.clear = function() {
	if (isString(this.value)) this.value = '';
	else this.innerHTML = '';
};
_p.prev = function() {
	return this.previousSibling;
};
_p.next = function() {
	return this.nextSibling;
};