<?php

	$data = array(
		'name' => 'Level',
		'args' => array('component'),
		'condition' => '!this||this==window',
		'afterCondition' => "
			this.children = [];
			this.cmp = component;
		",
		'privateMethods' => array(
			'renderItems' => array(
				'args' => array('items'),
				'body' => "
					if (isArray(items)) {
						for (var i = 0; i < items.length; i++) {
							if (!isArray(items[i])) renderItem.call(this, items[i]);
							else renderItems.call(this, items[i]);
						}
					} else renderItem.call(this, items);
				"
			),
			'renderItem' => array(
				'args' => array('i'),
				'body' => "
					if (!i && i !== 0) return;
					if (isFunction(i)) {
						renderItems.call(this, i());
						return;
					}
					if (!isObject(i)) createTextNode.call(this, i);
					else if (i.hasOwnProperty('t'))   createElement.call(this, i);
					else if (i.hasOwnProperty('v'))   createPropertyNode.call(this, i);
					else if (i.hasOwnProperty('i'))   createCondition.call(this, i);
					else if (i.hasOwnProperty('h'))   createForeach.call(this, i);	
					else if (i.hasOwnProperty('tmp')) includeTemplate.call(this, i);
					else if (i.hasOwnProperty('cmp')) renderComponent.call(this, i);
					else if (i.hasOwnProperty('is'))  createIfSwitch.call(this, i);
					else if (i.hasOwnProperty('sw'))  createSwitch.call(this, i);
					else if (i.hasOwnProperty('pl'))  createPlaceholder.call(this, i);
					else if (i.hasOwnProperty('l'))   createLet.call(this, i);
					else if (i.hasOwnProperty('f'))   createFrom.call(this, i);
				"
			),
			'createLevel' => array(
				'args' => array('items', 'pe'),
				'body' => "
					var lvl = ".CONST_GLOBAL.".get('Level');
					var level = new lvl(this.cmp);
					level.render(items, pe, this);
					this.children.push(level);
				"
			),
			'createTextNode' => array(
				'args' => array('content'),
				'body' => "
					if (content == '<br>') appendChild.call(this, document.createElement('br'));
					else appendChild.call(this, document.createTextNode(content));
				"
			),
			'createUpdater' => array(
				'args' => array('u', 's', 'p'),
				'body' => "
					this.updaters = this.updaters || [];
					if (p['n']) ".CONST_CORE.".createUpdater(u, p['$'] || this.cmp, s, p, this.updaters);
					if (p['g']) ".CONST_GLOBAL.".get('State').createUpdater(u, p['$'] || this.cmp, s, p);
				"
			),
			'createPropertyNode' => array(
				'args' => array('props'),
				'body' => "
					var v = '', pv = props['v'];
					if (isFunction(pv)) pv = pv();
					if (!isUndefined(pv)) v = pv;
					var node = document.createTextNode(v);
					appendChild.call(this, node);
					createUpdater.call(this, ".CONST_GLOBAL.".get('NodeUpdater'), node, props);
				"
			),
			'createElement' => array(
				'args' => array('props'),
				'body' => "
					var element = document.createElement(".CONST_TAGS."[props['t']] || 'span');
					appendChild.call(this, element);
					if (props['p']) {
						var pr = isFunction(props['p']) ? props['p']() : props['p'];
						var a;
						for (var k in pr) {
							a = ".CONST_ATTRIBUTES."[k] || k;
							if (a == 'scope') this.cmp.setScope(element);
							else if (a == 'as') ".CONST_CORE.".registerElement.call(this.cmp, element, pr[k]);
							else if (isPrimitive(pr[k]) && pr[k] !== '') {
								element.attr(a, pr[k]);
							}
						}
						if (props['n'] || props['g']) {
							createUpdater.call(this, ".CONST_GLOBAL.".get('ElementUpdater'), element, props);
						}
					}
					if (isArray(props['e'])) {
						var eventType, callback, isOnce, i;
						this.eventHandler = this.eventHandler || new (".CONST_GLOBAL.".get('EventHandler'))();
						for (i = 0; i < props['e'].length; i++) {
							eventType = ".CONST_EVENTTYPES."[props['e'][i]] || eventType;
							callback = props['e'][i + 1];
							isOnce = props['e'][i + 2] === true;
							if (isString(eventType) && isFunction(callback)) {					
								if (isOnce) {
									this.eventHandler.listenOnce(element, eventType, callback.bind(this.cmp));
									i++;
								} else this.eventHandler.listen(element, eventType, callback.bind(this.cmp));
							}
							i++;
						}
					}
					createLevel.call(this, props['c'], element);
				"
			),
			'appendChild' => array(
				'args' => array('child'),
				'body' => "
					if (this.nextSiblingChild) this.parentElement.insertBefore(child, this.nextSiblingChild);	
					else this.parentElement.appendChild(child);
					registerChild.call(this, child);
				"
			),
			'createCondition' => array(
				'args' => array('props'),
				'body' => "
					if (isFunction(props['i'])) {
						var condition = new (".CONST_GLOBAL.".get('Condition'))(props);
						condition.render(this.parentElement, this);
						registerChild.call(this, condition);
						createUpdater.call(this, ".CONST_GLOBAL.".get('OperatorUpdater'), condition, props);
					} else if (!!props['i']) {
						renderItems.call(this, props['c']);
					} else if (!isUndefined(props['e'])) {
						renderItem.call(this, props['e']);
					}
				"
			),
			'createForeach' => array(
				'args' => array('props'),
				'body' => "
					var foreach = new (".CONST_GLOBAL.".get('Foreach'))(props);
					foreach.render(this.parentElement, this);
					if (props['n'] || props['g']) {
						registerChild.call(this, foreach);
						createUpdater.call(this, ".CONST_GLOBAL.".get('OperatorUpdater'), foreach, props);
					}
				"
			),
			'createFrom' => array(
				'args' => array('props'),
				'body' => "
					var fr = new (".CONST_GLOBAL.".get('From'))(props);
					fr.render(this.parentElement, this);
					if (props['n'] || props['g']) {
						registerChild.call(this, fr);
						createUpdater.call(this, ".CONST_GLOBAL.".get('OperatorUpdater'), fr, props);
					}
				"
			),
			'createIfSwitch' => array(
				'args' => array('props'),
				'body' => "
					if (props['n'] || props['g']) {
						var swtch = new (".CONST_GLOBAL.".get('IfSwitch'))(props);
						swtch.render(this.parentElement, this);
						registerChild.call(this, swtch);
						createUpdater.call(this, ".CONST_GLOBAL.".get('OperatorUpdater'), swtch, props);
					} else {
						for (var i = 0; i < props['is'].length; i++) {
							if (!!props['is'][i]) {
								renderItems.call(this, props['c'][i]);
								return;
							}
						}
						if (!isUndefined(props['d'])) renderItems.call(this, props['d']);
					}
				"
			),
			'createSwitch' => array(
				'args' => array('props'),
				'body' => "
					if (props['n'] || props['g']) {
						var swtch = new (".CONST_GLOBAL.".get('Switch'))(props);
						swtch.render(this.parentElement, this);
						registerChild.call(this, swtch);
						createUpdater.call(this, ".CONST_GLOBAL.".get('OperatorUpdater'), swtch, props);
					} else {
						if (!isArray(props['cs'])) props['cs'] = [props['cs']];
						if (!isArray(props['c'])) props['c'] = [props['c']];
						for (var i = 0; i < props['cs'].length; i++) {
							if (props['sw'] === props['cs'][i]) {
								renderItems.call(this, props['c'][i]);
								return;
							}
						}			
						if (!isUndefined(props['d'])) renderItems.call(this, props['d']);
					}	
				"
			),
			'createLet' => array(
				'args' => array('props'),
				'body' => "
					if (props['n'] || props['g']) {
						var l = new (".CONST_GLOBAL.".get('Let'))(props);
						l.render(this.parentElement, this);
						registerChild.call(this, l);
						createUpdater.call(this, ".CONST_GLOBAL.".get('OperatorUpdater'), l, props);
					}
				"
			),
			'createPlaceholder' => array(
				'args' => array('props'),
				'body' => "
					var placeholderNode = document.createTextNode('');
					if (isString(props['d'])) placeholderNode.textContent = props['d'];
					placeholderNode.placeholderName = props['pl'];
					appendChild.call(this, placeholderNode);
				"
			),
			'registerChild' => array(
				'args' => array('child', 'isComponent'),
				'body' => "
					var isNodeChild = isNode(child);
					if (this.prevChild) ".CONST_CORE.".setNextSiblingChild.call(this.prevChild, child);
					this.prevChild = isNodeChild ? null : child;
					if (!this.firstChild) this.firstChild = child;
					if (isNodeChild) {
						if (!this.firstNodeChild) this.firstNodeChild = child;
						this.lastNodeChild = child;
					} else this.children.push(child);
					if (isComponent) ".CONST_CORE.".registerChildComponent.call(this.cmp, child);
				"
			),
			'includeTemplate' => array(
				'args' => array('item'),
				'body' => "
					var props = item['p'];
					if (isObject(props) && isObject(props['props'])) {
						var tempProps = props['props'];
						delete props['props'];
						for (var k in props) tempProps[k] = props[k];
						props = tempProps;
					}
					if (item['c']) {
						props = props || {};
						props['children'] = item['c'];
					}
					if (isNumber(item['tmp'])) item['tmp'] = ".CONST_GLOBAL.".get('i_' + item['tmp']);
					else if (isString(item['tmp'])) item['tmp'] = ".CONST_CORE.".getTemplateById.call(this.cmp, item['tmp']);
					if (isFunction(item['tmp'])) {		
						var items = item['tmp'].call(this.cmp, props, this.cmp);
						renderItems.call(this, items);
					}
				"
			),
			'renderComponent' => array(
				'args' => array('item', 'pe'),
				'body' => "
					pe = pe || this.parentElement;
					item['cmp'] = ".CONST_GLOBAL.".get(item['cmp']);
					if (isFunction(item['cmp'])) {
						var cmp = new item['cmp']();
						var ir = isFunction(item['p']);
						var i, k, p = ir ? item['p']() : item['p'];
						var props, data;
						if (isObject(p)) {
							if (p['p'] || p['ap']) props = initComponentProps.call(this, p['p'], p['ap']);
							if (isString(p['i'])) {
								".CONST_CORE.".setId.call(cmp, p['i']);
								var waiting = ".CONST_CORE.".getWaitingChild.call(this.cmp, p['i']);
								if (isArray(waiting)) {
									for (i = 0; i < waiting.length; i++) {
										waiting[i][0].set(waiting[i][1], cmp);
									}
								}
							}				
						}
						if (ir) createUpdater.call(this, ".CONST_GLOBAL.".get('ComponentUpdater'), cmp, item);
						if (isArray(item['w'])) {
							for (i = 0; i < item['w'].length; i += 2) {
								".CONST_CORE.".provideWithComponent.call(this.cmp, item['w'][i], item['w'][i + 1], cmp);
							}
						}
						if (item['c']) {
							props = props || {};
							props['children'] = item['c'];
						}
						".CONST_CORE.".initiate.call(cmp, props);
						cmp.render(pe);
						registerChild.call(this, cmp, true);
						if (isArray(item['e'])) {
							for (i = 0; i < item['e'].length; i++) {
								".CONST_CORE.".subscribe.call(cmp, item['e'][i], item['e'][i + 1], this.cmp);
								i++;	
							}
						}
						if (item['nm']) ".CONST_CORE.".registerControl.call(this.cmp, cmp, item['nm']);
					} else if (item && isObject(item)) {
						if (!item.isRendered()) item.render(pe);
						registerChild.call(this, item, true);
					}
				"
			),
			'initComponentProps' => array(
				'args' => array('p', 'ap'),
				'body' => "
					var props = {}, k;
					var f = function(pr) {
						if (isObject(pr)) {
							for (k in pr) props[k] = pr[k];
						}
					};
					f(p); f(ap);
					return props;
				"
			),
			'getElements' => array(
				'body' => "
					var elements = [];
					if (this.firstNodeChild && this.lastNodeChild) {
						var isAdding = false, p = this.parentElement;
						for (var i = 0; i < p.childNodes.length; i++) {
							if (p.childNodes[i] == this.firstNodeChild) isAdding = true;
							if (isAdding) elements.push(p.childNodes[i]);
							if (p.childNodes[i] == this.lastNodeChild) break;
						}
					}
					return elements;
				"
			),
			'disposeDom' => array(
				'args' => array(''),
				'body' => "
					var elementsToDispose = getElements.call(this);
					for (var i = 0; i < elementsToDispose.length; i++) this.parentElement.removeChild(elementsToDispose[i]);
					elementsToDispose = null;
				"
			)
		),
		'methods' => array(
			'render' => array(
				'args' => array('items', 'pe', 'pl', 'nsc'),
				'body' => "
					this.parentElement = pe;
					this.parentLevel = pl;
					this.nextSiblingChild = nsc;
					renderItems.call(this, items);
					this.prevChild = null;
					this.nextSiblingChild = null;
				"
			),
			'getParentElement' => array(
				'body' => "
					return this.parentElement;
				"
			),
			'getFirstNodeChild' => array(
				'body' => "
					if (isNode(this.firstChild)) return this.firstChild;
					var firstLevel = this.children[0];
					if (firstLevel instanceof ".CONST_GLOBAL.".get('Level')) {
						return ".CONST_CORE.".getParentElement.call(firstLevel);
					} else if (firstLevel) {
						return ".CONST_CORE.".getFirstNodeChild.call(firstLevel);
					}
					return null;
				"
			),
			'getComponent' => array(
				'body' => "
					return this.cmp;
				"
			),
			'setAppended' => array(
				'args' => array('isAppended', 'p'),
				'body' => "
					var isDetached = !isAppended;
					if (isDetached === !!this.detached) return;
					this.detached = isDetached;
					var elements = getElements.call(this);
					if (isDetached) {
						this.realParentElement = this.parentElement;
						this.parentElement = p || document.createElement('div'); 
						for (var i = 0; i < elements.length; i++) this.parentElement.appendChild(elements[i]);
					} else {
						this.nextSiblingChild = ".CONST_CORE.".getNextSiblingChild.call(this.parentLevel);
						this.parentElement = this.realParentElement;
						this.realParentElement = null;
						for (var i = 0; i < elements.length; i++) appendChild.call(this, elements[i]);
					}
				"
			),
			'placeTo' => array(
				'args' => array('element'),
				'body' => "
					this.setAppended(false, element);
				"
			),
			'dispose' => array(
				'body' => "
					if (this.updaters) {
						for (var i = 0; i < this.updaters.length; i++) {
							".CONST_CORE.".disposeUpdater.call(this.cmp, this.updaters[i], this.updaters[i + 1]);
							this.updaters[i + 1] = null;
							i++;
						}
					}
					for (var i = 0; i < this.children.length; i++) {
						if (isComponentLike(this.children[i])) {
							".CONST_CORE.".unregisterChildComponent.call(this.cmp, this.children[i]);
						}
						this.children[i].dispose();
						this.children[i] = null;
					}
					if (this.eventHandler) {
						this.eventHandler.dispose();
						this.eventHandler = null;
					}
					disposeDom.call(this);
					this.updaters = null;
					this.children = null;
					this.parentElement = null;
					this.parentLevel = null;
					this.firstChild = null;
					this.firstNodeChild = null;
					this.lastNodeChild = null;
					this.realParentElement = null;
					this.cmp = null;
				"
			)
		)
	);
?>