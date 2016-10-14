function Decliner() {
	var words = {{WORDS}};
	this.getCount = function(key, num) {
		if (isArray(num)) num = num.length;
		return num + ' ' + this.get(key, num);
	};
	this.get = function(key, num) {
		if (isArray(num)) num = num.length;
		if (!isNumber(num)) return '';
		return Objects.get(Objects.get(words, key, ''), getVariant(num), '');
	};
	var getVariant = function(num) {
		var n, m;
		num = num.toString();
		m = num.charAt(num.length - 1); 		
		if (num.length > 1) n = num.charAt(num.length - 2); 
		else n = 0;
		if (n == 1) return 2;
		else { 
			if (m == 1) return 0;
			else if (m > 1 && m < 5) return 1;
			else return 2;
		}
	};
}
Decliner = new Decliner();