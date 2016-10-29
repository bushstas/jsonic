function ComponentUpdater(c, p) {
	this.getKeys = function() {
		return [];
	}
	this.react = function(d) {

	};
	this.dispose = function() {
		c = p = null;
	};
}