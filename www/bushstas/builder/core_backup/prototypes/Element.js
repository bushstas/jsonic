;(function(){
var cache = {};
{{PROTO}}=Element.prototype;
{{PROTO}}.setClass = function(className) {
	this.className = className.trim();
}
{{PROTO}}.toggleClass = function(className, isAdding) {
	if (isAdding) {
		this.addClass(className);
	} else {
		this.removeClass(className);
	}
};
{{PROTO}}.switchClasses = function(className1, className2) {
	var classes = this.getClasses();
	if (classes.contains(className1)) { 
		this.removeClass(className1);
		this.addClass(className2);
	} else if (classes.contains(className2)) {
		this.removeClass(className2);
		this.addClass(className1);
	}
};
{{PROTO}}.addClass = function(className) {
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
{{PROTO}}.removeClass = function(className) {
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
{{PROTO}}.hasClass = function(className) {
	return this.getClasses().indexOf(className) > -1;
};
{{PROTO}}.getClasses = function() {
	if (!this.className) return [];
	var classNames = this.className.trim().replace(/ {2,}/g, ' ');
	return classNames.split(' ');
};
{{PROTO}}.getAncestor = function(selector) {
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
{{PROTO}}.getData = function(name) {
	return this.getAttribute('_' + name) || '';
};
{{PROTO}}.setData = function(name, value) {
	this.setAttribute('_' + name, value);
};
{{PROTO}}.getRect = function() {
	return this.getBoundingClientRect();
};
{{PROTO}}.setWidth = function(width) {
	this.style.width = isNumber(width) ? width + 'px' : width;
};
{{PROTO}}.setHeight = function(height) {
	this.style.height = isNumber(height) ? height + 'px' : height;
};
{{PROTO}}.getWidth = function() {
	return this.getRect().width;
};
{{PROTO}}.getHeight = function() {
	return this.getRect().height;
};
{{PROTO}}.getTop = function() {
	return this.getRect().top;
};
{{PROTO}}.getLeft = function() {
	return this.getRect().left;
};
{{PROTO}}.css = function(style) {
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
};
{{PROTO}}.getChildAt = function(index) {
	return this.childNodes[index];
};
{{PROTO}}.attr = function(attrName) {
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
{{PROTO}}.show = function(isShown) {
	var display = isString(isShown) ? isShown : (isUndefined(isShown) || isShown ? 'block' : 'none');
	this.style.display = display;
};
{{PROTO}}.hide = function() {
	this.show(false);
};
{{PROTO}}.find = function(selector) {
	return this.querySelector(selector);
};
{{PROTO}}.finds = function(selector) {
	return this.querySelectorAll(selector);
};
{{PROTO}}.getParent = function() {
	return this.parentNode;
};
{{PROTO}}.scrollTo = function(pxy, duration) {
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
{{PROTO}}.getRelativePosition = function(element) {
	var a = this.getRect();
	var b = element.getRect();
	return {x: Math.round(a.left - b.left + element.scrollLeft), y:  Math.round(a.top - b.top + element.scrollTop)};
};
{{PROTO}}.clear = function() {
	if (isString(this.value)) this.value = '';
	else this.innerHTML = '';
};
{{PROTO}}.prev = function() {
	return this.previousSibling;
};
{{PROTO}}.next = function() {
	return this.nextSibling;
};
})();