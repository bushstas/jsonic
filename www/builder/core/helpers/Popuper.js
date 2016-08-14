function Popuper() {
	var components;
	var elements;
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
	var onBodyMouseDown = function(e) {
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