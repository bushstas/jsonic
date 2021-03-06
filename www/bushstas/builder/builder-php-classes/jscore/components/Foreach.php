<?php

	$data = array(
		'name' => 'Foreach',
		'args' => array('params'),
		'afterCondition' => "
			this.params = params;
		",
		'condition' => CONST_ENTERCOND,
		'privateMethods' => array(
			'getKeysInRandomOrder' => array(
				'body' => "
					var keys = ".CONST_OBJECTS.".getKeys(getParam.call(this, 'p'));
					keys.shuffle();
					return keys;
				"
			),
			'createIfEmptyLevel' => array(
				'body' => "
					if (!isUndefined(this.params['ie'])) {
						".CONST_CORE.".createLevel.call(this, this.params['ie']);
					}
				"
			),
			'getParam' => array(
				'args' => array('p'),
				'body' => "
					return (isFunction(this.params['p']) ? this.params['p']() : this.params)[p];
				"
			),
			'createLevels' => array(
				'args' => array('isUpdating'),
				'body' => "
					var p = this.params;
					var items = getParam.call(this, 'p'), limit = getParam.call(this, 'l'), r;
					if (isArrayLike(items)) {
						if (p['ra']) {
							if (!".CONST_OBJECTS.".empty(items)) {
								var keys = getKeysInRandomOrder();
								for (var i = 0; i < keys.length; i++) {
									if (limit && i + 1 > limit) break;
									r = p['h'](items[keys[i]], keys[i]);
									if (r == '".CONST_BREAK."') break;
									".CONST_CORE.".createLevel.call(this, r, isUpdating);
								}
								return;
							}
						} else if (isArray(items)) {
							var from = getParam.call(this, 'fr'), to = getParam.call(this, 'to');
							if (!items.isEmpty()) {
								var start;
								if (!p['r']) {
									start = isNumber(from) ? from : 0;
									for (var i = start; i < items.length; i++) {
										if (limit && i + 1 > limit) break;
										if (isNumber(to) && i > to) break;
										r = p['h'](items[i], i);
										if (r == '".CONST_BREAK."') break;
										".CONST_CORE.".createLevel.call(this, r, isUpdating);
									}
								} else {
									var j = 0;
									start = isNumber(from) ? from : items.length - 1;
									for (var i = start; i >= 0; i--) {
										j++;
										if (limit && j > limit) break;
										if (isNumber(to) && i < to) break;
										r = p['h'](items[i], i);
										if (r == '".CONST_BREAK."') break;
										".CONST_CORE.".createLevel.call(this, r, isUpdating);
									}
								}
								return;
							}
						} else if (isObject(items)) {
							if (!".CONST_OBJECTS.".empty(items)) {
								if (!p['r']) {
									var i = 0;
									for (var k in items) {
										i++;
										if (limit && i > limit) break;
										r = p['h'](items[k], k);
										if (r == '".CONST_BREAK."') break;
										".CONST_CORE.".createLevel.call(this, r, isUpdating);
									}
								} else {
									var keys = ".CONST_OBJECTS.".getKeys(items);
									keys.reverse();
									for (var i = 0; i < keys.length; i++) {
										if (limit && i + 1 > limit) break;
										r = p['h'](items[keys[i]], keys[i]);
										if (r == '".CONST_BREAK."') break;
										".CONST_CORE.".createLevel.call(this, r, isUpdating);
									}
								}
								return;
							}
						}
					}
					createIfEmptyLevel.call(this);
				"
			)
		),
		'methods' => array(
			'render' => array(
				'args' => array('pe', 'pl'),
				'body' => "
					".CONST_CORE.".initOperator.call(this, pe, pl);					
					createLevels.call(this, false);
				"
			),
			'update' => array(
				'args' => array(''),
				'body' => "
					".CONST_CORE.".disposeLevels.call(this);
					createLevels.call(this, true);
				"
			),
			'add' => array(
				'args' => array('item', 'index'),
				'body' => "
					var r = this.params['h'](item, ~~index);
					if (r != '".CONST_BREAK."') ".CONST_CORE.".createLevel.call(this, r, false, index);	
				"
			),
			'remove' => array(
				'args' => array('index'),
				'body' => "
					if (this.levels[index]) {
						this.levels[index].dispose();
						this.levels.splice(index, 1);
					}
				"
			),
			'dispose' => array(
				'body' => "
					".CONST_CORE.".disposeOperator.call(this);
				"
			)
		)
	);
?>