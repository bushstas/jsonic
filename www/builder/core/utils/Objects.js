function Objects() {
	this.each = function(arr, callback) {
		if (arguments[2]) {
			callback = callback.bind(arguments[2]);
		}
		for (var i = 0; i < arr.length; i++) {
			var result = callback(arr[i], i);
			if (result == '__break') break;
		}
	};
	this.remove = function(arr, item) {
		var idx = arr.indexOf(item);
		if (idx > -1) {
			this.removeAt(arr, idx);
		}
	};
	this.removeAt = function(arr, idx) {
		arr.splice(idx, 1);
	};
	this.equals = function(arr1, arr2) {
		if (typeof arr1 !== typeof arr2) return false;
	    if (isArray(arr1) && isArray(arr2) && arr1.length !== arr2.length) return false;
	    if (isObject(arr1)) {
	        for (var p in arr1) {
	        	if (arr1.hasOwnProperty(p)) {
		            if (isFunction(arr1[p]) && isFunction(arr2[p])) continue;
		            if (isArray(arr1[p]) && isArray(arr2[p]) && arr1[p].length !== arr2[p].length) return false;
		            if (typeof arr1[p] !== typeof arr2[p]) return false;
		            if (isObject(arr1[p]) && isObject(arr2[p])) {
		            	if (!this.equals(arr1[p], arr2[p])) return false; 
		            } else if (arr1[p] !== arr2[p]) {
		            	return false;
		            }
	        	}
	        }
	    } else return arr1 === arr2;
	    return true;		
	};
	this.merge = function() {
		var arrs = arguments;
		if (!isObject(arrs[0])) {
			arrs[0] = {};
		}
		for (var i = 1; i < arrs.length; i++) {
			if (isArrayLike(arrs[i])) {
				for (var k in arrs[i]) {
					arrs[0][k] = arrs[i][k];
				}
			}
		}
	};
	this.clone = function(obj) {
		if (!isArrayLike(obj)) return obj;
		return JSON.parse(JSON.stringify(obj));
	};
	this.get = function(obj, key, defaultValue) {
		return this.has(obj, key) ? obj[key] : defaultValue;
	};
	this.has = function(obj, key, value) {
		if (!isObject(obj)) return false;
		var has = !isUndefined(obj[key]);
		if (has && !isUndefined(value)) {
			return obj[key] == value;
		}
		return has;
	};
	this.empty = function(obj) {
		if (!isArrayLike(obj)) {
			return true;
		}
		if (isObject(obj)) {
			for (var k in obj) {
				return false;
			}
			return true;
		}
		return isUndefined(obj[0]);
	};
}