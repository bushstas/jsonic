function Initialization() {
	var isMethodToInherit = function(method) {
		return method != __I && method != __GI;
	};
	var extendInitials = function(initials1, initials2) {
		if (isNull(initials1)) {
			initials1 = initials2;
		} else {
			for (var k in initials2) {
				if (isUndefined(initials1[k])) {
					initials1[k] = initials2[k];
				} else {
					if (isObject(initials1[k]) || isObject(initials2[k])) Objects.merge(initials1[k], initials2[k]);
					else if (isArray(initials1[k]) || isArray(initials2[k])) Objects.concat(initials1[k], initials2[k]);
				}
			}
		}
		return initials1;
	};
	var addProps = function(initialProps) {
		for (var k in initialProps)	{
			if (isUndefined(this.props[k])) {
				this.props[k] = initialProps[k];
			}
		}
	};
	this.processInitials = function() {
		var initials = this.initials;
		if (isObject(initials)) {
			for (var k in initials) {
				if (isArrayLike(initials[k])) {
					if (k == 'correctors') {
						for (var j in initials[k]) addCorrector.call(this, j, initials[k][j]);
					} else if (k == 'globals') {
						for (var j in initials[k]) Globals.subscribe(j, initials[k][j], this);
					} else if (k == 'followers') {
						for (var j in initials[k]) addFollower.call(this, j, initials[k][j]);
					} else if (k == 'controllers') {
						for (var i = 0; i < initials[k].length; i++) attachController.call(this, initials[k][i]);
					} else if (k == 'props') {
						addProps.call(this, initials[k]);
					} else if (k == 'options') {
						if (isObject(this.options)) Objects.merge(this.options, initials[k]);
						else this.options = initials[k];
					}
				}
			}
		}
	};
	var getInitial = function(initialName) {
		return Objects.get(this.initials, initialName);
	};
	var attachController = function(options) {
		if (isObject(options['on'])) {
			for (var k in options['on']) options.controller.subscribe(k, options['on'][k], this);
		} else options.controller.subscribe('', null, this);
	};
	var addCorrector = function(name, handler) {
		if (isFunction(handler)) {
			this.correctors = this.correctors || {};
			this.correctors[name] = handler;
		}
	};
	var addFollower = function(name, handler) {
		if (isFunction(handler)) {
			this.followers = this.followers || {};
			this.followers[name] = handler;
		}
	};
	this.processPostRenderInitials = function() {
		var helpers = getInitial.call(this, 'helpers');
		if (isArray(helpers)) {
			for (var i = 0; i < helpers.length; i++) subscribeToHelper.call(this, helpers[i]);
		}
	};
	var subscribeToHelper = function(options) {
		if (isObject(options['options'])) options['helper'].subscribe(this, options['options']);
	};
	this.inherits = function(list) {
		var children, parent, child, initials, sc;
		for (var k = 0; k < list.length; k++) {
			parent = list[k];
			children = list[++k];
			for (var i = 0; i < children.length; i++) {
				child = children[i];
				if (parent != Core) {
					if (!child.prototype.inheritedSuperClasses) {
						child.prototype.inheritedSuperClasses = [];
					}
					sc = child.prototype.inheritedSuperClasses;
					var cb = function(p) {
						if (sc.indexOf(p) == -1) sc.push(p);
						var psc = p.prototype.inheritedSuperClasses;
						if (isArray(psc)) {
							for (var n = 0; n < psc.length; n++) cb(psc[n]);
						}
					};
					cb(parent);
				}
				for (var method in parent.prototype) {
					if (!child.prototype[method] && isMethodToInherit(method)) {
						child.prototype[method] = parent.prototype[method];
					}
				}
			}
		}
	};
	this.initiate = function(props, args, opts) {
		var initials = null;
		if (isFunction(this.constructor.prototype.getInitials)) {
			initials = this.constructor.prototype.getInitials();
		}
		var initiateParental = function(superClasses, object) {
			var parentInitials;
			for (var i = 0; i < superClasses.length; i++) {
				if (isFunction(superClasses[i].prototype.initiate)) {
					superClasses[i].prototype.initiate.call(object);
				}
				if (isFunction(superClasses[i].prototype.getInitials)) {
					parentInitials = superClasses[i].prototype.getInitials();
					if (isObject(parentInitials)) {
						initials = extendInitials(initials, parentInitials);
					}
				}
				if (isArray(superClasses[i].prototype.inheritedSuperClasses)) {
					initiateParental(superClasses[i].prototype.inheritedSuperClasses, object);
				}
			}
		};
		if (isArray(this.inheritedSuperClasses)) {
			initiateParental(this.inheritedSuperClasses, this);
		}
		if (isObject(this.props)) Objects.merge(this.props, props);
		else this.props = props || {};
		if (isFunction(this.constructor.prototype.initiate)) {
			this.constructor.prototype.initiate.call(this);
		}
		this.initials = initials;
		this.args = args;
		if (opts) this.options = opts;
		Initialization.processInitials.call(this);
	};
	this.initiateControllers = function(controllers) {
		for (var i = 0; i < controllers.length; i++) Initialization.initiate.call(controllers[i]);
	};
}
Initialization = new Initialization();