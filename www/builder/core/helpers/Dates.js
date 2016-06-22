function Dates() {
	var date;
	var months = ["\u042f\u043d\u0432\u0430\u0440\u044c","\u0424\u0435\u0432\u0440\u0430\u043b\u044c","\u041c\u0430\u0440\u0442","\u0410\u043f\u0440\u0435\u043b\u044c","\u041c\u0430\u0439","\u0418\u044e\u043d\u044c","\u0418\u044e\u043b\u044c","\u0410\u0432\u0433\u0443\u0441\u0442","\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044c","\u041e\u043a\u0442\u044f\u0431\u0440\u044c","\u041d\u043e\u044f\u0431\u0440\u044c","\u0414\u0435\u043a\u0430\u0431\u0440\u044c"];
	this.getYear = function() {
		return get().getFullYear();
	};
	this.getDay = function() {
		return get().getDate();
	};
	this.getDate = function() {
		var date = get();
		return {day: date.getDate(), month: date.getMonth(), year: date.getFullYear()};
	};
	this.getMonth = function() {
		return get().getMonth();
	};
	this.getMonthName = function() {
		if (isNumber(arguments[0])) {
			return months[arguments[0]];
		}
		return months[this.getMonth()];
	};
	this.getDays = function(month, year) {
		return 33 - new Date(year, month, 33).getDate();
	};
	this.getWeekDay = function(day, month, year) {
		return new Date(year, month, day).getDay();
	};
	var get = function() {
		return new Date();
	};
}
Dates = new Dates();