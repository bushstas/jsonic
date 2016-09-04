function Tester() {
	this.assert = function(t, a, c, e) {
		var i = this.check(t, a, c);
		if (!i) this.log(e);
		return i;
	};
	this.check = function(t, a, c) {
		var d = [], isa = isArray(c);
		if (isa) {
			for (var i = 0; i < c.length; i++) {
				d.push(c[i]);
				if (i < c.length - 1 && !this.check('arrayLike', a, d)) return false;
			}
		}
		d = null;
		if (isa) {
			for (var i = 0; i < c.length; i++) a = a[c[i]];
		}
		switch (t) {
			case 'string':
				return isString(a);
			case 'number':
				return isNumber(a);
			case 'numeric':
				return isNumeric(a);
			case 'bool':
				return isBool(a);
			case 'function':
				return isFunction(a);
			case 'array':
				return isArray(a);
			case 'object':
				return isObject(a);
			case 'arrayLike':
				return isArrayLike(a);
			case 'element':
				return isElement(a);
			case 'node':
				return isNode(a);
			case 'text':
				return isText(a);
			case 'componentLike':
				return isComponentLike(a);
			case 'component':
				return isComponent(a);
			case 'control':
				return isControl(a);
			case 'null':
				return isNull(a);
			case 'undefined':
				return isUndefined(a);
			case 'empty':
				return isNone(a);
			case 'notEmptyString':
				return isNotEmptyString(a);
			case 'zero':
				return isZero(a);
		}
		return true;
	};
	this.log = function() {

	};
}
Tester = new Tester();