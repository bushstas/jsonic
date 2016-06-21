var StyleNameCache = {};
Element.prototype.setClass = function(className) {
	this.className = className;
}
Element.prototype.toggleClass = function(className, isAdding) {
	if (isAdding) {
		this.addClass(className);
	} else {
		this.removeClass(className);
	}
};
Element.prototype.addClass = function(className) {
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
Element.prototype.removeClass = function(className) {
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
Element.prototype.hasClass = function(className) {
	return this.getClasses().indexOf(className) > -1;
};
Element.prototype.getClasses = function() {
	var classNames = (this.className || '').trim().replace(/ {2,}/g, ' ');
	if (classNames) {
		return classNames.split(' ');
	}
	return [];
};
Element.prototype.getAncestor = function(selector) {
	if (isNone(selector) || !isString(selector)) {
		return null;
	}
	if (isFunction(Element.prototype.closest)) {
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
Element.prototype.getData = function(name) {
	return this.getAttribute('_' + name) || '';
};
Element.prototype.getRect = function() {
	return this.getBoundingClientRect();
};
Element.prototype.setStyle = function(style) {
	var element = this;
	var set = function(value, style) {
		var propertyName = getVendorJsStyleName(style);	
		if (propertyName) {
			element.style[propertyName] = value;
		}
	};
	var getVendorJsStyleName = function(style) {
		var propertyName = StyleNameCache[style];
		if (!propertyName) {
			propertyName = toCamelCase(style);
	    	StyleNameCache[style] = propertyName;
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
Element.prototype.getChildAt = function(index) {
	return this.childNodes[index];
};