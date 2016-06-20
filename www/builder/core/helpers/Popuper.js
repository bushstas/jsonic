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
			elements.push(element || null);
		}
	};
	var onBodyMouseDown = function(e) {
		for (var i = 0; i < components.length; i++) {
			if (!isElement(elements[i]) || !e.targetHasAncestor(elements[i])) {
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