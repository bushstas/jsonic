function Dates() {
	var date;
	var months = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
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