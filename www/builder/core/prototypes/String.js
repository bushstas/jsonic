String.prototype.isEmpty = function() {
	return !(/[^\s]/).test(this);
};
String.prototype.toArray = function(delimiter) {
	delimiter = delimiter || ',';
	var ar = [];
	var parts= this.split(delimiter);
	for (var i = 0; i < parts.length; i++) {
		if (parts[i]) ar.push(parts[i].trim());
	}
	return ar;
};