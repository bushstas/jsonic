function generateRandomKey() {
	var x = 2147483648, now = +new Date();
	return Math.floor(Math.random() * x).toString(36) + Math.abs(Math.floor(Math.random() * x) ^ now).toString(36);
}
function toCamelCase(str) {
	return String(str).replace(/\-([a-z])/g, function(all, match) {
		return match.toUpperCase();
	});
}
function isComponentLike(a) {
	return isObject(a) && isFunction(a.instanceOf);
}
function isComponent(a) {
	return isComponentLike(a) && a.instanceOf('Component');
}
function isController(a) {
	return isComponentLike(a) && a.instanceOf('Controller');
}
function isControl(a) {
	return isComponentLike(a) && a.instanceOf('Control');
}
function isObject(a) {
	return !!a && typeof a == 'object' && !isNode(a) && !isArray(a);
}
function isArray(a) {
	return a instanceof Array;
}
function isArrayLike(a) {
	return isArray(a) || isObject(a);
}
function isElement(a) {
	return a instanceof Element;
}
function isNode(a) {
	return a instanceof Node;
}
function isText(a) {
	return a instanceof Text;
}
function isFunction(a) {
	return a instanceof Function;
}
function isBool(a) {
	return typeof a == 'boolean';
}
function isBoolean(a) {
	return isBool(a);
}
function isString(a) {
	return typeof a == 'string';
}
function isNumber(a) {
	return typeof a == 'number';
}
function isPrimitive(a) {
	return isString(a) || isNumber(a) || isBool(a);
}
function isNumeric(a) {
	return isNumber(a) || (isString(a) && (/^\d+$/).test(a));
}
function isUndefined(a) {
	return a === undefined;
}
function isNull(a) {
	return a === null;
}
function isNone(a) {
	return isUndefined(a) || isNull(a) || a === false || a === 0 || a === '0' || a === '';
}
function isZero(a) {
	return a === 0 || a === '0';
}
function isNotEmptyString(a) {
	return isString(a) && (/[^\s]/).test(a);
}
function stringToNumber(str) {
	return Number(str);
}
function getCount(a) {
	return isArray(a) ? a.length : 0;
}