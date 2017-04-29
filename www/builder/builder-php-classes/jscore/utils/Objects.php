<?php

	$data = array(
		'name' => CONST_OBJECTS,
		'var' => CONST_OBJECTS,
		'define' => true,
		'mode' => 2,
		'thisMethods' => array(
			'each' => array(
				'args' => array('obj', 'callback', 'thisObj'),
				'body' => "
					if (isArrayLike(obj)) {
						if (thisObj) callback = callback.bind(thisObj);
						for (var k in obj) if (callback(obj[k], k) == 'break') break;
					}
				"
			),
			'remove' => array(
				'args' => array('obj', 'item'),
				'body' => "
					if (isArray(obj)) {
						var idx = obj.indexOf(item);
						if (idx > 0) obj.splice(idx, 1);
					} else if (isObject(obj)) delete obj[obj.getKey(item)];
				"
			),
			'equals' => array(
				'args' => array('a', 'b'),
				'body' => "
					return a === b && a !== 0 || _equals( a, b );
					
					function _equals( a, b ) {    
					  var s, l, p, x, y;
					  if ( ( s = toString.call( a ) ) !== toString.call( b ) ) return false;
					  
					  switch( s ) {
					    default:
					      return a.valueOf() === b.valueOf();
					    
					    case '[object Function]':
					      return false;
					    
					    case '[object Array]':
					      if ( ( l = a.length ) != b.length ) return false;          
					      while ( l-- ) {
					        if ( ( x = a[ l ] ) === ( y = b[ l ] ) && x !== 0 || _equals( x, y ) ) continue;            
					        return false;
					      }
					      
					      return true;
					    
					    case '[object Object]':
					      l = 0;          

					      for ( p in a ) {
					        if ( a.hasOwnProperty( p ) ) {
					          ++l;                
					          if ( ( x = a[ p ] ) === ( y = b[ p ] ) && x !== 0 || _equals( x, y ) ) continue;                
					          return false;
					        }
					      }
					      for ( p in b )
					        if ( b.hasOwnProperty( p ) && --l < 0 )
					          return false;
					              
					      return true;
					  }
					}
				"
			),
			'merge' => array(
				'body' => "
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
				"
			),
			'concat' => array(
				'body' => "
					var arrs = arguments;
					if (!isArray(arrs[0])) arrs[0] = [];
					for (var i = 1; i < arrs.length; i++) {
						if (isArray(arrs[i])) {
							for (var j = 0; j < arrs[i].length; j++) {
								arrs[0].push(arrs[i][j]);
							}
						}
					}
					return arrs[0];
				"
			),
			'clone' => array(
				'args' => array('obj'),
				'body' => "
					if (!isArrayLike(obj)) return obj;
					return JSON.parse(JSON.stringify(obj));
				"
			),
			'get' => array(
				'args' => array('obj', 'key', 'defaultValue'),
				'body' => "
					return this.has(obj, key) ? obj[key] : defaultValue;
				"
			),
			'getByIndex' => array(
				'args' => array('obj', 'idx'),
				'body' => "
					if (!isArrayLike(obj)) return;
					if (isArray(obj)) return obj[idx];
					var count = 0;
					for (var k in obj) {
						if (count == idx) return obj[k];
						count++;
					}
				"
			),
			'has' => array(
				'args' => array('obj', 'key', 'value'),
				'body' => "
					if (!isArrayLike(obj)) return false;
					var has = !isUndefined(obj[key]);
					if (has && !isUndefined(value)) return obj[key] == value;
					return has;
				"
			),
			'empty' => array(
				'args' => array('obj'),
				'body' => "
					if (!isArrayLike(obj)) return true;
					if (isObject(obj)) {
						for (var k in obj) return false;
						return true;
					}
					return isUndefined(obj[0]);
				"
			),
			'getKey' => array(
				'args' => array('obj', 'value'),
				'body' => "
					for (var k in obj) if (obj[k] == value) return k;
				"
			),
			'getValues' => array(
				'args' => array('obj'),
				'body' => "
					var vals = [];
					for (var k in obj) vals.push(obj[k]);
					return vals;
				"
			),
			'getKeys' => array(
				'args' => array('obj'),
				'body' => "
					var keys = [];
					if (isObject(obj)) {
						for (var k in obj) keys.push(k);
					} else if (isArray(obj)) {
						for (var i = 0; i < obj.length; i++) keys.push(i);
					}
					return keys;
				"
			),
			'flatten' => array(
				'args' => array('obj', 'flattened', 'transformed'),
				'body' => "
					var top = isUndefined(transformed);
					flattened = flattened || {};
					transformed = transformed || [];
					if (!isObject(obj)) return obj;
					for (var k in obj) {
						if (isObject(obj[k])) this.flatten(obj[k], flattened, transformed);
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
				"
			)
		)
	);
?>