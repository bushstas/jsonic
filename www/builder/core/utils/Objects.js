function Objects() {
	this.each = function(obj, callback, thisObj) {
		if (isArrayLike(obj)) {
			if (thisObj) callback = callback.bind(thisObj);
			for (var k in obj) if (callback(obj[k], k) == 'break') break;
		}
	};
	this.remove = function(obj, item) {
		if (isArray(obj)) this.removeAt(obj, obj.indexOf(item));
		else if (isObject(obj)) delete obj[obj.getKey(item)];
	};
	this.removeAt = function(arr, idx) {
		if (isArray(arr) && isNumber(idx) && idx >= 0) arr.splice(idx, 1);
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
		var objs = arguments;
		if (!isArrayLike(objs[0])) objs[0] = {};
		for (var i = 1; i < objs.length; i++) {
			if (isArrayLike(objs[i])) {
				for (var k in objs[i]) {
					if (!isUndefined(objs[i][k])) objs[0][k] = objs[i][k];
				}
			}
		}
		return objs[0];
	};
	this.clone = function(obj) {
		if (!isArrayLike(obj)) return obj;
		return JSON.parse(JSON.stringify(obj));
	};
	this.get = function(obj, key, defaultValue) {
		return this.has(obj, key) ? obj[key] : defaultValue;
	};
	this.getByIndex = function(obj, idx) {
		if (!isArrayLike(obj)) return;
		if (isArray(obj)) return obj[idx];
		var count = 0;
		for (var k in obj) {
			if (count == idx) return obj[k];
			count++;
		}
	};
	this.has = function(obj, key, value) {
		if (!isArrayLike(obj)) return false;
		var has = !isUndefined(obj[key]);
		if (has && !isUndefined(value)) return obj[key] == value;
		return has;
	};
	this.empty = function(obj) {
		if (!isArrayLike(obj)) return true;
		if (isObject(obj)) {
			for (var k in obj) return false;
			return true;
		}
		return isUndefined(obj[0]);
	};
	this.getKey = function(obj, value) {
		for (var k in obj) if (obj[k] == value) return k;
	};
	this.getKeys = function(obj) {
		var keys = [];
		for (var k in obj) keys.push(k);
		return keys;
	};
	this.flatten = function(obj, flattened, transformed) {
		var top = isUndefined(transformed);
		flattened = flattened || {};
		transformed = transformed || [];
		if (!isObject(obj)) return obj;
		for (var k in obj) {
			if (isObject(obj[k])) Objects.flatten(obj[k], flattened, transformed);
			else {
				if (!isUndefined(flattened[k])) {
					if (transformed.indexOf(k) == -1 || !isArray(flattened[k])) {
						flattened[k] = [flattened[k]];
						transformed.push(k);
					}					
					flattened[k].push(obj[k])
				} else flattened[k] = obj[k];
			}
		}
		if (top) transformed = null;
		return flattened;
	};
}
Objects = new Objects();