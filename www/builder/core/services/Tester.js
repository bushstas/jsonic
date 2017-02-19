{{GLOBAL}}.set(function() {
	var log = [];
	var views = [];
	this.assert = function(t, a, k, e, c, m) {
		var i = this.check(t, a, k);
		if (!i) this.log(e, c, m);
		return i;
	};
	this.check = function(t, a, k) {
		var d = [], isa = isArray(k);
		if (isa) {
			for (var i = 0; i < k.length; i++) {
				d.push(k[i]);
				if (i < k.length - 1 && !this.check('arrayLike', a, d)) return false;
			}
		}
		d = null;
		if (isa) {
			for (var i = 0; i < k.length; i++) a = a[k[i]];
		}
		switch (t) {
			case 'string': return isString(a);
			case 'number': return isNumber(a);
			case 'numeric': return isNumeric(a);
			case 'bool': return isBool(a);
			case 'function': return isFunction(a);
			case 'array': return isArray(a);
			case 'object': return isObject(a);
			case 'arrayLike': return isArrayLike(a);
			case 'element': return isElement(a);
			case 'node': return isNode(a);
			case 'text': return isText(a);
			case 'componentLike': return isComponentLike(a);
			case 'component': return isComponent(a);
			case 'control': return isControl(a);
			case 'null': return isNull(a);
			case 'undefined': return isUndefined(a);
			case 'empty': return isNone(a);
			case 'notEmptyString': return isNotEmptyString(a);
			case 'zero': return isZero(a);
		}
		return true;
	};
	this.log = function(t, c, m) {
		t = c + '.' + m + ': ' + t;
		window.console.log(t);
		log.push(t);
	};
	this.onTested = function(view) {

	};
}, 'Tester');