function SizeFixer() {
	var subscribers = [], bodySize;	
	this.subscribe = function(subscriber, options) {
		if (subscribers.indexOf(subscriber) == -1) {
			subscriber = [subscriber, options];
			subscribers.push(subscriber);
			initBodySize();
			resize(subscriber);
		}
	};
	var resize = function(subscriber) {
		var element = subscriber[0].findElement(subscriber[1]['selector']);
		if (isElement(element)) {
			
		}
	}
	var onResize = function() {
		for (var i = 0; i < subscribers.length; i++) {
			resize(subscribers[i]);
		}
	};
	var initBodySize = function() {
		bodySize = document.body.getRect();
	};
	window.addEventListener('resize', onResize, false);
}
SizeFixer = new SizeFixer();