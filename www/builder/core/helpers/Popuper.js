function Popuper() {
	var components, elements, skippedAll;
	var reset = function() {
		components = [];
		elements = [];
	};
	this.watch = function(component, element) {
		if (components.indexOf(component) == -1) {
			components.push(component);
			if (isString(element)) element = component.findElement(element);
			elements.push(element || component.getElement() || null);
		}
	};
	this.skipAll = function(isSkipped) {
		skippedAll = isSkipped;
	};
	var onBodyMouseDown = function(e) {
		if (skippedAll) return;
		var element;
		for (var i = 0; i < components.length; i++) {
			element = elements[i];
			if (!isElement(element) || !e.targetHasAncestor(element)) {
				components[i].hide();
				reset();
			}
		}
	};
	reset();
	var body = document.documentElement;
	body.addEventListener('mousedown', onBodyMouseDown, false);
}
Popuper = new Popuper();