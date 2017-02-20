{{GLOBAL}}.set(({{COMPONENT}} = function() {	
	if (!this || this == window) {
		var load = function() {
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
		};
		var renderTempPlaceholder = function() {
			this.tempPlaceholder = document.createElement('span');
			this.parentElement.appendChild(this.tempPlaceholder);
		};
		var onDataLoad = function(isAsync, data) {
			this.toggle('__loading');
			this.onLoaded(data);
			var loader = this.initials['loader'];
			if (isFunction(loader['callback'])) {
				loader['callback'].call(this);
			}
			if (!isAsync) onReadyToRender.call(this);
		};
		var onReadyToRender = function() {
			if (!this.isRendered()) {
				doRendering.call(this);
				if (this.tempPlaceholder) {
					this.parentElement.removeChild(this.tempPlaceholder);
					this.tempPlaceholder = null;
				}
				{{GLOBAL}}.get('Core').processPostRenderInitials.call(this);
			}
		};
		var doRendering = function() {
			var lvl = {{GLOBAL}}.get('Level');
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
		};
		var propagatePropertyChange = function(changedProps) {
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
		};
		var callFollowers = function(changedProps) {
			for (var k in changedProps) {
				callFollower.call(this, k, changedProps[k]);
			}
		};
		var callFollower = function(propName, propValue) {
			if (Objects.has(this.followers, propName)) this.followers[propName].call(this, propValue);
		};
		var updateForeach = function(propName, index, item) {
			var updaters = this.updaters[propName], o;
			if (isArray(updaters)) {
				for (i = 0; i < updaters.length; i++) {
					if (updaters[i] instanceof {{GLOBAL}}.get('OperatorUpdater')) {
						o = updaters[i].getOperator();
						if (o instanceof {{GLOBAL}}.get('Foreach')) {
							if (!isUndefined(item)) o.add(item, index);
							else o.remove(index);
						}
					}
				}
			}
		};
		var unrender = function() {
			this.elements = null;
			{{GLOBAL}}.get('Core').disposeLinks.call(this);
			this.disposeInternal();
			this.level.dispose();
			this.level = this.listeners = null;
		};
		{{PROTO}}={{COMPONENT}}.prototype;
		{{PROTO}}.render = function(parentElement) {
			this.parentElement = parentElement;
			load.call(this);
		};

		{{PROTO}}.isDisabled = function() {
			return !!this.disabled;
		};

		{{PROTO}}.isRendered = function() {
			return !!this.rendered;
		};

		{{PROTO}}.isDisposed = function() {
			return !!this.disposed;
		};

		{{PROTO}}.instanceOf = function(classFunc) {
			if (isString(classFunc)) classFunc = {{GLOBAL}}.get(classFunc);
			return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
		};

		{{PROTO}}.disable = function(isDisabled) {
			this.disabled = isDisabled;
			this.addClass('->> disabled', !isDisabled);
		};

		{{PROTO}}.dispatchEvent = function(eventType) {
			var args = Array.prototype.slice.call(arguments), l;
			args.splice(0, 1);
			if (isArray(this.listeners)) {
				for (var i = 0; i < this.listeners.length; i++) {
					l = this.listeners[i];
					if (isNumber(l['type'])) l['type'] = {{EVENTTYPES}}[l['type']];
					if (l['type'] == eventType) {
						l['handler'].apply(l['subscriber'], args);
					}
				}
			}
		};

		{{PROTO}}.addListener = function(target, eventType, handler) {
	 		if (isElement(target)) {
	 			var eh = {{GLOBAL}}.get('EventHandler');
	 			this.eventHandler = this.eventHandler || new eh();
	 			this.eventHandler.listen(target, eventType, handler.bind(this));
	 		} else target.subscribe(eventType, handler, this);
	 	};

		{{PROTO}}.removeValueFrom = function(propName, value) {
			var prop = this.get(propName);
			if (isArray(prop)) this.removeByIndexFrom(propName, prop.indexOf(value));
		};

		{{PROTO}}.removeByIndexFrom = function(propName, index) {
			var prop = this.get(propName);
			if (isString(index) && isNumeric(index)) index = ~~index;
			if (isArray(prop) && isNumber(index) && index > -1 && !isUndefined(prop[index])) {
				prop.splice(index, 1);
				updateForeach.call(this, propName, index);
				callFollower.call(this, propName, prop);
			}
		};

		{{PROTO}}.plusTo = function(propName, add, sign) {
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
		};

		{{PROTO}}.addOneTo = function(propName, item, index) {
			this.addTo(propName, [item], index);
		};

		{{PROTO}}.addTo = function(propName, items, index) {
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
		};

		{{PROTO}}.get = function(propName) {
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
		};

		{{PROTO}}.showElement = function(element, isShown) {
			if (isString(element)) element = this.findElement(element);
			if (isElement(element)) element.show(isShown);
		};

		{{PROTO}}.setStyle = function(styles) {
			if (this.isRendered()) this.getElement().css(styles);
		};

		{{PROTO}}.setPosition = function(x, y) {
			this.setStyle({'top': y + 'px', 'left': x + 'px'});
		};

		{{PROTO}}.setVisible = function(isVisible) {
			if (this.isRendered() && !this.isDisposed()) this.getElement().show(isVisible);
		};

		{{PROTO}}.addClass = function(className, isAdding) {
			if (this.isRendered()) {
				if (isAdding || isUndefined(isAdding)) this.getElement().addClass(className);
				else this.getElement().removeClass(className);
			}
		};

		{{PROTO}}.each = function(propName, callback) {
			var ar = this.get(propName);
			if (isArrayLike(ar) && isFunction(callback)) {
				if (isArray(ar)) for (var i = 0; i < ar.length; i++) callback.call(this, ar[i], i, ar);
				else for (var k in ar) callback.call(this, ar[k], k, ar);
			}
		};

		{{PROTO}}.toggle = function(propName) {
			this.set(propName, !this.get(propName));
		};

		{{PROTO}}.set = function(propName, propValue) {
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
		};

		{{PROTO}}.preset = function(propName, propValue) {
			this.props = this.props || {};
			this.props[propName] = propValue;
		};

		{{PROTO}}.delay = function(f, n, p) {
			window.clearTimeout(this.timeout);
			if (isFunction(f)) this.timeout = window.setTimeout(f.bind(this, p), n || 200);
		};

		{{PROTO}}.addChild = function(child, parentElement) {
			this.level.renderComponent(child, parentElement);
		};

		{{PROTO}}.removeChild = function(child) {
			if (!child) return;
			var childId = child;
			if (isString(child)) child = this.getChild(child);
			else childId = Objects.getKey(this.children, child);
			if (isComponentLike(child)) child.dispose();
			if ((isString(childId) || isNumber(childId)) && isObject(this.children)) {
				this.children[childId] = null;
				delete this.children[childId];
			}
		 };

		{{PROTO}}.forEachChild = function(callback) {
			if (isArrayLike(this.children)) {
				var result;
				for (var k in this.children) {
					if (!this.children[k].isDisabled()) {
						result = callback.call(this, this.children[k], k);
						if (result) return result;
					}
				}
			}
		};
		
		{{PROTO}}.forChildren = function(className, callback) {
			var children = this.getChildren(className), result;
			for (var i = 0; i < children.length; i++) {
				result = callback.call(this, children[i], i);
				if (result) return result;
			}
		};

		{{PROTO}}.getControl = function(name) {
			return Objects.get(this.controls, name) || this.forEachChild(function(child) {
				return child.getControl(name);
			});
		};

		{{PROTO}}.setControlValue = function(name, value) {
			var control = this.getControl(name);
			if (control) control.setValue(value);
		};

		{{PROTO}}.enableControl = function(name, isEnabled) {
			var control = this.getControl(name);
			if (control) control.setEnabled(isEnabled);
		};

		{{PROTO}}.forEachControl = function(callback) {
			if (isObject(this.controls)) Objects.each(this.controls, callback, this);
		};

		{{PROTO}}.hasControls = function() {
			return !Objects.empty(this.controls);
		};

		{{PROTO}}.getControlsData = function(data) {
			data = data || {};
			this.forEachChild(function(child) {
				if (!isControl(child)) child.getControlsData(data);
				else data[child.getName()] = child.getValue();
			});
			return data;
		};

		{{PROTO}}.setControlsData = function(data) {
			this.forEachChild(function(child) {
				if (!isControl(child)) child.setControlsData(data);
				else child.setValue(data[child.getName()]);
			});
			return data;
		};

		{{PROTO}}.getChildAt = function(index) {
			return Objects.getByIndex(this.children, index);
		};

		{{PROTO}}.getChildIndex = function(child, same) {
			var idx = -1;
			this.forEachChild(function(ch) {
				if (!same || (same && ch.constructor == child.constructor)) idx++;
				if (ch == child) return true;
			});
			return idx;
		};

		{{PROTO}}.getChildren = function(className) {
			if (!isString(className)) return this.children;
			var children = [];
			this.forEachChild(function(child) {
				if (isComponentLike(child) && child.instanceOf(className)) children.push(child);
			});
			return children;
		};

		{{PROTO}}.getChild = function(id) {
			return Objects.get(this.children, id);
		};

		{{PROTO}}.setId = function(id) {
			this.componentId = id;
		};

		{{PROTO}}.getId = function() {
			return this.componentId;
		};

		{{PROTO}}.getElement = function(id) {
			if (isString(id)) return Objects.get(this.elements, id);
			else return this.scope || this.parentElement;
		};

		{{PROTO}}.findElement = function(selector, scopeElement) {
			return (scopeElement || this.getElement()).querySelector(selector);
		};

		{{PROTO}}.findElements = function(selector, scopeElement) {
			return Array.prototype.slice.call((scopeElement || this.scope || this.parentElement).querySelectorAll(selector));
		};

		{{PROTO}}.fill = function(element, data) {
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
		};

		{{PROTO}}.setAppended = function(isAppended) {
			if (this.level) this.level.setAppended(isAppended);
		};

		{{PROTO}}.placeTo = function(element) {
			if (this.level) this.level.placeTo(element);
		};

		{{PROTO}}.placeBack = function() {
			this.setAppended(true);
		};

		{{PROTO}}.appendChild = function(child, isAppended) {
			if (isString(child)) child = this.getChild(child);
			if (isComponentLike(child)) child.setAppended(isAppended);
		};

		{{PROTO}}.setScope = function(scope) {
			this.scope = scope;
		};

		{{PROTO}}.getUniqueId = function() {
			return this.uniqueId = this.uniqueId || generateRandomKey();
		};

		{{PROTO}}.dispose = function() {
			{{GLOBAL}}.get('State').dispose(this);
			unrender.call(this);
			if (this.mouseHandler) {
				this.mouseHandler.dispose();
				this.mouseHandler = null;
			}
			this.updaters = null;
			this.parentElement = null;
			this.props = null;
			this.provider = null;
			this.children = null;
			this.disposed = true;
			this.initials = null;
			this.followers = null;
			this.correctors = null;
			this.controls = null;
		};
		{{PROTO}}.a = function(n) {
			return {{GLOBAL}}.get('State').get(n);
		};
		var f = function(){return};
		{{PROTO}}.initOptions=f;
		{{PROTO}}.onRendered=f;
		{{PROTO}}.onRenderComplete=f;
		{{PROTO}}.onLoaded=f;
		{{PROTO}}.getTemplateMain=f;
		{{PROTO}}.disposeInternal=f;
		{{PROTO}}.g={{PROTO}}.get;
		{{PROTO}}.d={{PROTO}}.dispatchEvent;
		return {{COMPONENT}};
	}
})(), 'Component');