<?php

	$data = array(
		'name' => 'Component',
		'condition' => '!this||this==window',
		'privateMethods' => array(
			'load' => array(
				'args' => array(''),
				'body' => "
					var loader = Objects.get(this.initials, 'loader');
					if (isObject(loader) && isObject(loader['controller'])) {
						this.preset('__loading', true);
						var isAsync = !!loader['async'];
						var options = loader['options'];
						if (isFunction(options)) options = options();
						loader['controller'].addSubscriber('load', {
							'initiator': this,
							'callback': onDataLoad.bind(this, isAsync)
						}, !!loader['private']);			
						loader['controller'].doAction(this, 'load', options);
						if (!isAsync) {
							renderTempPlaceholder.call(this);
							return;
						}
					}
					onReadyToRender.call(this);	
				"
			),
			'renderTempPlaceholder' => array(
				'body' => "
					this.tempPlaceholder = document.createElement('span');
					this.parentElement.appendChild(this.tempPlaceholder);
				"
			),
			'onDataLoad' => array(
				'args' => array('isAsync', 'data'),
				'body' => "
					this.toggle('__loading');
					this.onLoaded(data);
					var loader = this.initials['loader'];
					if (isFunction(loader['callback'])) {
						loader['callback'].call(this);
					}
					if (!isAsync) onReadyToRender.call(this);
				"
			),
			'onReadyToRender' => array(
				'body' => "
					if (!this.isRendered()) {
						doRendering.call(this);
						if (this.tempPlaceholder) {
							this.parentElement.removeChild(this.tempPlaceholder);
							this.tempPlaceholder = null;
						}
						{{".AUTOCRR_GLOBAL."}}.get('Core').processPostRenderInitials.call(this);
					}
				"
			),
			'doRendering' => array(
				'body' => "
					var lvl = {{".AUTOCRR_GLOBAL."}}.get('Level');
					this.level = new lvl(this);
					var content = this.getTemplateMain(this.props, this);
					if (content) this.level.render(content, this.parentElement, this, this.tempPlaceholder);
					this.rendered = true;
					this.onRendered();
					this.onRenderComplete();
					this.forEachChild(function(child) {
						if (isFunction(child.onParentRendered)) child.onParentRendered.call(child);
					});
					delete this.waiting;
				"
			),
			'propagatePropertyChange' => array(
				'args' => array('changedProps'),
				'body' => "
					if (!this.updaters) return;
					var updated = [];
					for (var k in changedProps) {
						if (this.updaters[k]) {
							for (var i = 0; i < this.updaters[k].length; i++) {
								if (updated.indexOf(this.updaters[k][i]) == -1) {
									this.updaters[k][i].react(changedProps);
									updated.push(this.updaters[k][i]);
								}
							}
						}
					}
					updated = null;
					callFollowers.call(this, changedProps);
				"
			),
			'callFollowers' => array(
				'args' => array('changedProps'),
				'body' => "
					for (var k in changedProps) {
						callFollower.call(this, k, changedProps[k]);
					}
				"
			),
			'callFollower' => array(
				'args' => array('propName', 'propValue'),
				'body' => "
					if (Objects.has(this.followers, propName)) this.followers[propName].call(this, propValue);	
				"
			),
			'updateForeach' => array(
				'args' => array('propName', 'index', 'item'),
				'body' => "
					var updaters = this.updaters[propName], o;
					if (isArray(updaters)) {
						for (var i = 0; i < updaters.length; i++) {
							if (updaters[i] instanceof {{".AUTOCRR_GLOBAL."}}.get('OperatorUpdater')) {
								o = updaters[i].getOperator();
								if (o instanceof {{".AUTOCRR_GLOBAL."}}.get('Foreach')) {
									if (!isUndefined(item)) o.add(item, index);
									else o.remove(index);
								}
							}
						}
					}
				"
			),
			'unrender' => array(
				'body' => "
					this.elements = null;
					{{".AUTOCRR_GLOBAL."}}.get('Core').disposeLinks.call(this);
					this.disposeInternal();
					for (var i = 0; i < this.inheritedSuperClasses.length - 1; i++) {
						this.inheritedSuperClasses[i].prototype.disposeInternal.call(this);
					}
					this.level.dispose();
					this.level = this.listeners = null;
				"
			),
		),
		'methods' => array(
			'render' => array(
				'args' => array('parentElement'),
				'body' => "
					this.parentElement = parentElement;
					load.call(this);
				"
			),
			'isDisabled' => array(
				'body' => "
					return !!this.disabled;					
				"
			),
			'isRendered' => array(
				'body' => "
					return !!this.rendered;
				"
			),
			'isDisposed' => array(
				'body' => "
					return !!this.disposed;
				"
			),
			'instanceOf' => array(
				'args' => array('classFunc'),
				'body' => "
					if (isString(classFunc)) classFunc = {{".AUTOCRR_GLOBAL."}}.get(classFunc);
					return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
				"
			),
			'disable' => array(
				'args' => array('isDisabled'),
				'body' => "
					this.disabled = isDisabled;
					this.addClass('->> disabled', !isDisabled);
				"
			),
			'dispatchEvent' => array(
				'args' => array('eventType'),
				'body' => "
					var args = Array.prototype.slice.call(arguments), l;
					args.splice(0, 1);
					if (isArray(this.listeners)) {
						for (var i = 0; i < this.listeners.length; i++) {
							l = this.listeners[i];
							if (isNumber(l['type'])) l['type'] = {{".AUTOCRR_EVENTTYPES."}}[l['type']];
							if (l['type'] == eventType) {
								l['handler'].apply(l['subscriber'], args);
							}
						}
					}	
				"
			),
			'addListener' => array(
				'args' => array('target', 'eventType', 'handler'),
				'body' => "
					if (isElement(target)) {
			 			var eh = {{".AUTOCRR_GLOBAL."}}.get('EventHandler');
			 			this.eventHandler = this.eventHandler || new eh();
			 			this.eventHandler.listen(target, eventType, handler.bind(this));
			 		} else target.subscribe(eventType, handler, this);
				"
			),
			'removeValueFrom' => array(
				'args' => array('propName', 'value'),
				'body' => "
					var prop = this.get(propName);
					if (isArray(prop)) this.removeByIndexFrom(propName, prop.indexOf(value));
				"
			),
			'removeByIndexFrom' => array(
				'args' => array('propName', 'index'),
				'body' => "
					var prop = this.get(propName);
					if (isString(index) && isNumeric(index)) index = ~~index;
					if (isArray(prop) && isNumber(index) && index > -1 && !isUndefined(prop[index])) {
						prop.splice(index, 1);
						updateForeach.call(this, propName, index);
						callFollower.call(this, propName, prop);
					}
				"
			),
			'plusTo' => array(
				'args' => array('propName', 'add', 'sign'),
				'body' => "
					var prop = this.get(propName);
					if (!sign || sign == '+') {
						if (isNumber(prop) || isString(prop)) this.set(propName, prop + add);
					} else 	if (isNumber(prop) && isNumber(add)) {
						var v;
						if (sign == '-') v = prop - add;
						else if (sign == '*') v =  prop * add;
						else if (sign == '/') v = prop / add;
						else if (sign == '%') v = prop % add;
						this.set(propName, v);
					}
				"
			),
			'addOneTo' => array(
				'args' => array('propName', 'item', 'index'),
				'body' => "
					this.addTo(propName, [item], index);
				"
			),
			'addTo' => array(
				'args' => array('propName', 'items', 'index'),
				'body' => "
					var prop = this.get(propName);
					if (!isArray(items)) items = [items];
					if (isArray(prop)) {
						for (var j = 0; j < items.length; j++) {
							if (!isNumber(index)) prop.push(items[j]);
							else if (index == 0) prop.unshift(items[j]);
							else prop.insertAt(items[j], index);
							updateForeach.call(this, propName, index, items[j]);
							if (isNumber(index)) index++;
						}
						callFollower.call(this, propName, prop);
					}
				"
			),
			'get' => array(
				'args' => array('propName'),
				'body' => "
					var prop = this.props[propName];
					if (isUndefined(arguments[1]) || !isArrayLike(prop)) return prop;
					var end;
					for (var i = 1; i < arguments.length; i++) {
						prop = prop[arguments[i]];
						if (isUndefined(prop)) return '';
						end = arguments.length == i + 1;
						if (end || !isArrayLike(prop)) break;
					}
					return end ? prop || '' : '';
				"
			),
			'setVisible' => array(
				'args' => array('isVisible'),
				'body' => "
					if (this.isRendered() && !this.isDisposed()) this.getElement().show(isVisible);
				"
			),
			'addClass' => array(
				'args' => array('className', 'isAdding'),
				'body' => "
					if (this.isRendered()) {
						if (isAdding || isUndefined(isAdding)) this.getElement().addClass(className);
						else this.getElement().removeClass(className);
					}	
				"
			),
			'each' => array(
				'args' => array('propName', 'callback'),
				'body' => "
					var ar = this.get(propName);
					if (isArrayLike(ar) && isFunction(callback)) {
						if (isArray(ar)) for (var i = 0; i < ar.length; i++) callback.call(this, ar[i], i, ar);
						else for (var k in ar) callback.call(this, ar[k], k, ar);
					}
				"
			),
			'toggle' => array(
				'args' => array('propName'),
				'body' => "
					this.set(propName, !this.get(propName));
				"
			),
			'set' => array(
				'args' => array('propName', 'propValue'),
				'body' => "
					this.props = this.props || {};
					var props;
					if (!isUndefined(propValue)) {
						props = {};
						props[propName] = propValue;
					} else if (isObject(propName)) {
						props = propName;
					} else return;

					var isChanged = false;
					var changedProps = {};
					var currentValue;
					for (var k in props) {
						if (Objects.has(this.correctors, k)) props[k] = this.correctors[k].call(this, props[k], props);
						currentValue = this.props[k];
						if (currentValue == props[k]) continue;
						if (isArray(currentValue) && isArray(props[k]) && Objects.equals(currentValue, props[k])) continue;
						isChanged = true;
						this.props[k] = props[k];
						changedProps[k] = props[k];
					}
					if (this.isRendered()) {
						if (isChanged) propagatePropertyChange.call(this, changedProps);
					}
					changedProps = null;
				"
			),
			'preset' => array(
				'args' => array('propName', 'propValue'),
				'body' => "
					this.props = this.props || {};
					this.props[propName] = propValue;
				"
			),
			'delay' => array(
				'args' => array('f', 'n', 'p'),
				'body' => "
					window.clearTimeout(this.timeout);
					if (isFunction(f)) this.timeout = window.setTimeout(f.bind(this, p), n || 200);
				"
			),
			'addChild' => array(
				'args' => array('child', 'parentElement'),
				'body' => "
					this.level.renderComponent(child, parentElement);
				"
			),
			'removeChild' => array(
				'args' => array('child'),
				'body' => "
					if (!child) return;
					var childId = child;
					if (isString(child)) child = this.getChild(child);
					else childId = Objects.getKey(this.children, child);
					if (isComponentLike(child)) child.dispose();
					if ((isString(childId) || isNumber(childId)) && isObject(this.children)) {
						this.children[childId] = null;
						delete this.children[childId];
					}
				"
			),
			'forEachChild' => array(
				'args' => array('callback'),
				'body' => "
					if (isArrayLike(this.children)) {
						var result;
						for (var k in this.children) {
							if (!this.children[k].isDisabled()) {
								result = callback.call(this, this.children[k], k);
								if (result) return result;
							}
						}
					}
				"
			),
			'forChildren' => array(
				'args' => array('className', 'callback'),
				'body' => "
					var children = this.getChildren(className), result;
					for (var i = 0; i < children.length; i++) {
						result = callback.call(this, children[i], i);
						if (result) return result;
					}
				"
			),
			'getControl' => array(
				'args' => array('name'),
				'body' => "
					return Objects.get(this.controls, name) || this.forEachChild(function(child) {
						return child.getControl(name);
					});
				"
			),
			'setControlValue' => array(
				'args' => array('name', 'value'),
				'body' => "
					var control = this.getControl(name);
					if (control) control.setValue(value);
				"
			),
			'enableControl' => array(
				'args' => array('name', 'isEnabled'),
				'body' => "
					var control = this.getControl(name);
					if (control) control.setEnabled(isEnabled);
				"
			),
			'forEachControl' => array(
				'args' => array('callback'),
				'body' => "
					if (isObject(this.controls)) Objects.each(this.controls, callback, this);
				"
			),
			'hasControls' => array(
				'body' => "
					return !Objects.empty(this.controls);
				"
			),
			'getControlsData' => array(
				'args' => array('data'),
				'body' => "
					data = data || {};
					this.forEachChild(function(child) {
						if (!isControl(child)) child.getControlsData(data);
						else data[child.getName()] = child.getValue();
					});
					return data;
				"
			),
			'setControlsData' => array(
				'args' => array('data'),
				'body' => "
					this.forEachChild(function(child) {
						if (!isControl(child)) child.setControlsData(data);
						else child.setValue(data[child.getName()]);
					});
					return data;
				"
			),
			'getChildAt' => array(
				'args' => array('index'),
				'body' => "
					return Objects.getByIndex(this.children, index);
				"
			),
			'getChildIndex' => array(
				'args' => array('child', 'same'),
				'body' => "
					var idx = -1;
					this.forEachChild(function(ch) {
						if (!same || (same && ch.constructor == child.constructor)) idx++;
						if (ch == child) return true;
					});
					return idx;
				"
			),
			'getChildren' => array(
				'args' => array('className'),
				'body' => "
					if (!isString(className)) return this.children;
					var children = [];
					this.forEachChild(function(child) {
						if (isComponentLike(child) && child.instanceOf(className)) children.push(child);
					});
					return children;
				"
			),
			'getChild' => array(
				'args' => array('id'),
				'body' => "
					return Objects.get(this.children, id);
				"
			),
			'getId' => array(
				'body' => "
					return this.componentId;	
				"
			),
			'getElement' => array(
				'args' => array('id'),
				'body' => "
					if (isString(id)) return Objects.get(this.elements, id);
					else return this.scope || this.parentElement;
				"
			),
			'findElement' => array(
				'args' => array('selector', 'scopeElement'),
				'body' => "
					return (scopeElement || this.getElement()).querySelector(selector);
				"
			),
			'findElements' => array(
				'args' => array('selector', 'scopeElement'),
				'body' => "
					return Array.prototype.slice.call((scopeElement || this.scope || this.parentElement).querySelectorAll(selector));
				"
			),
			'fill' => array(
				'args' => array('element', 'data'),
				'body' => "
					if (isString(element)) element = this.findElement(element);
					if (isElement(element)) {
						var callback = function(el) {
							for (var i = 0; i < el.childNodes.length; i++) {
								if (isElement(el.childNodes[i])) {
									callback(el.childNodes[i]);
								} else if (isText(el.childNodes[i]) && !isUndefined(data[el.childNodes[i].placeholderName])) {
									el.childNodes[i].textContent = data[el.childNodes[i].placeholderName];
								}
							}
						};
						callback(element);
					}
				"
			),
			'setAppended' => array(
				'args' => array('isAppended'),
				'body' => "
					if (this.level) this.level.setAppended(isAppended);
				"
			),
			'placeTo' => array(
				'args' => array('element'),
				'body' => "
					if (this.level) this.level.placeTo(element);
				"
			),
			'placeBack' => array(
				'body' => "
					this.setAppended(true);
				"
			),
			'appendChild' => array(
				'args' => array('child', 'isAppended'),
				'body' => "
					if (isString(child)) child = this.getChild(child);
					if (isComponentLike(child)) child.setAppended(isAppended);
				"
			),
			'setScope' => array(
				'args' => array('scope'),
				'body' => "
					this.scope = scope;
				"
			),
			'getUniqueId' => array(
				'body' => "
					return this.uniqueId = this.uniqueId || generateRandomKey();
				"
			),
			'dispose' => array(
				'args' => array(''),
				'body' => "
					{{".AUTOCRR_GLOBAL."}}.get('State').dispose(this);
					unrender.call(this);
					if (this.mouseHandler) {
						this.mouseHandler.dispose();
						this.mouseHandler = null;
					}
					this.updaters = null;
					this.parentElement = null;
					this.props = null;
					this.children = null;
					this.disposed = true;
					this.initials = null;
					this.followers = null;
					this.correctors = null;
					this.controls = null;
				"
			),
			'a' => array(
				'args' => array('n'),
				'body' => "
					return {{".AUTOCRR_GLOBAL."}}.get('State').get(n);
				",
				'after' => 'var f=function(){return};'
			),
			'initOptions' => array(
				'value' => 'f'
			),
			'onRendered' => array(
				'value' => 'f'
			),
			'onRenderComplete' => array(
				'value' => 'f'
			),
			'onLoaded' => array(
				'value' => 'f'
			),
			'getTemplateMain' => array(
				'value' => 'f'
			),
			'disposeInternal' => array(
				'value' => 'f'
			),
			'g' => array(
				'value' => "{{".AUTOCRR_PROTO."}}.get"
			),
			'd' => array(
				'value' => "{{".AUTOCRR_PROTO."}}.dispatchEvent"
			)
		),
		'overridableMethods' => array(),
		'templateCallableMethods' => array(),
		'checker' => array(
			'render' => 'onRendered',
			'dispose' => 'onDisposed'
		)
	);

?>