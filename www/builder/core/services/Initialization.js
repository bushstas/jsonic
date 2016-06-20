function Initialization() {
	this.inherits = function(list) {
		var children, parent, child, initials;
		for (var k = 0; k < list.length; k++) {
			parent = list[k];
			children = list[++k];
			for (var i = 0; i < children.length; i++) {
				child = children[i];
				if (parent != Core) {
					if (!child.prototype.inheritedSuperClasses) {
						child.prototype.inheritedSuperClasses = [];
					}
					child.prototype.inheritedSuperClasses.push(parent);
				}
				for (var method in parent.prototype) {
					if (!child.prototype[method] && isMethodToInherit(method)) {
						child.prototype[method] = parent.prototype[method];
					}
				}
			}
		}
	};
	this.initiate = function(props) {
		var initials = null;
		var initiateParental = function(superClasses, object) {
			for (var i = 0; i < superClasses.length; i++) {
				if (isFunction(superClasses[i].prototype.initiate)) {
					superClasses[i].prototype.initiate.call(object);
				}
				if (isFunction(superClasses[i].prototype.getInitials)) {
					var parentInitials = superClasses[i].prototype.getInitials();
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
		this.props = props || {};
		if (isFunction(this.constructor.prototype.initiate)) {
			this.constructor.prototype.initiate.call(this);
		}
		if (isFunction(this.constructor.prototype.getInitials)) {
			var ownInitials = this.constructor.prototype.getInitials();
			if (isNull(initials)) {
				initials = ownInitials;
			} else if (isObject(ownInitials)) {
				initials = extendInitials(initials, ownInitials);
			}
		}
		this.initials = initials;
	};
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
					Objects.merge(initials1[k], initials2[k]);
				}
			}
		}
		return initials1;
	};
}
Initialization = new Initialization();