var Dates = new (function() {
	var date;
	var months = ["\u042f\u043d\u0432\u0430\u0440\u044c","\u0424\u0435\u0432\u0440\u0430\u043b\u044c","\u041c\u0430\u0440\u0442","\u0410\u043f\u0440\u0435\u043b\u044c","\u041c\u0430\u0439","\u0418\u044e\u043d\u044c","\u0418\u044e\u043b\u044c","\u0410\u0432\u0433\u0443\u0441\u0442","\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044c","\u041e\u043a\u0442\u044f\u0431\u0440\u044c","\u041d\u043e\u044f\u0431\u0440\u044c","\u0414\u0435\u043a\u0430\u0431\u0440\u044c"];
	var months2 = ["\u044f\u043d\u0432\u0430\u0440\u044f","\u0444\u0435\u0432\u0440\u0430\u043b\u044f","\u043c\u0430\u0440\u0442\u0430","\u0430\u043f\u0440\u0435\u043b\u044f","\u043c\u0430\u044f","\u0438\u044e\u043d\u044f","\u0438\u044e\u043b\u044f","\u0430\u0432\u0433\u0443\u0441\u0442\u0430","\u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","\u043e\u043a\u0442\u044f\u0431\u0440\u044f","\u043d\u043e\u044f\u0431\u0440\u044f","\u0434\u0435\u043a\u0430\u0431\u0440\u044f"];
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
	this.getTimeStamp = function() {
		return new Date().getTime();
	};
	this.getDays = function(month, year) {
		return 33 - new Date(year, month, 33).getDate();
	};
	this.getWeekDay = function(day, month, year) {
		return new Date(year, month, day).getDay();
	};
	this.getFormattedDate = function(stringDate, format) {
		format = format.toLowerCase();
		stringDate = stringDate.split(/[ \.-]+/);
		var y, y2, ys, m2, d, d2;
		var s0 = ~~stringDate[0], m = ~~stringDate[1], s2 = ~~stringDate[2];
		if (s2) {
			m2 = m < 10 ? '0' + m : m;
			if (s2 > 100) {
				y = s2;
				d = s0;
			} else {
				d = s2;
				y = s0;
			}
			d2 = d < 10 ? '0' + d : d;
			ys = y + '';
			y2 = ys.charAt(2) + ys.charAt(3);
		} else {
			return stringDate;
		}
		format = format.replace(/y{4}/, y);
		format = format.replace(/y{2}/, y2);
		format = format.replace(/month/, months2[m - 1]);
		format = format.replace(/m{2}/, m2);
		format = format.replace(/m{1}/, m);
		format = format.replace(/d{2}/, d2);
		format = format.replace(/d{1}/, d);
		return format;
	};
	var get = function() {
		return new Date();
	};
})();
{{GLOBAL}}.set(Dates, 'Dates');