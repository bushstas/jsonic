<?php

	$data = array(
		'mode' => 4,
		'prototypeOf' => 'Array',
		'methods' => array(
			'contains' => array(
				'args' => array('v'),
				'body' => "
					var iv = ~~v;
					if (iv == v) return this.indexOf(iv) > -1 || this.indexOf(v + '') > -1;
					return this.has(v);
				"
			),
			'has' => array(
				'args' => array('v'),
				'body' => "
					return this.indexOf(v) > -1;
				"
			),
			'hasAny' => array(
				'args' => array('a'),
				'body' => "
					if (!isArray(a)) a = arguments;
					for (var i = 0; i < a.length; i++) {
						if (this.indexOf(a[i]) > -1) return true;
					}
				"
			),
			'hasExcept' => array(
				'args' => array(''),
				'body' => "
					var args = Array.prototype.slice.call(arguments);
					for (var i = 0; i < this.length; i++) {
						if (args.indexOf(this[i]) == -1) return true;
					}
				"
			),
			'removeDuplicates' => array(
				'body' => "
					this.filter(function(item, pos, self) {
					    return self.indexOf(item) == pos;
					});
					return this;
				"
			),
			'getIntersections' => array(
				'args' => array('arr'),
				'body' => "
					return this.filter(function(n) {
					    return arr.indexOf(n) != -1;
					});
				"
			),
			'hasIntersections' => array(
				'args' => array('arr'),
				'body' => "
					return !isUndefined(this.getIntersections(arr)[0]);
				"
			),
			'removeIndexes' => array(
				'args' => array('indexes'),
				'body' => "
					var deleted = 0;
					for (var i = 0; i < indexes.length; i++) {
						this.splice(indexes[i] - deleted, 1);
						deleted++;
					}
				"
			),
			'isEmpty' => array(
				'body' => "
					return this.length == 0;
				"
			),
			'removeItems' => array(
				'args' => array('items'),
				'body' => "
					for (var i = 0; i < items.length; i++) this.removeItem(items[i]);
				"
			),
			'removeItem' => array(
				'args' => array('item'),
				'body' => "
					var index = this.indexOf(item);
					if (index > -1) this.splice(index, 1);	
				"
			),
			'insertAt' => array(
				'args' => array('item', 'index'),
				'body' => "
					if (!isNumber(index) || index >= this.length) this.push(item);
					else this.splice(index, 0, item);
				"
			),
			'shuffle' => array(
				'args' => array(''),
				'body' => "
					var tmp;
					for (var i = this.length - 1; i > 0; i--) {
						var j = Math.floor(Math.random() * (i + 1));
						tmp = this[i];
						this[i] = this[j];
						this[j] = tmp;
					}
				"
			),
			'addUnique' => array(
				'args' => array('item'),
				'body' => "
					if (!this.has(item)) this.push(item);
				"
			),
			'addRemove' => array(
				'args' => array('item', 'add', 'addUnique'),
				'body' => "
					if (add) {
						if (addUnique) this.addUnique(item);
						else this.push(item);
					} else this.removeItem(item);
				"
			)
		)
	);
?>