component Calendar

function initiate() {
	this.month = Dates.getMonth();
	this.year = Dates.getYear();
}

function onRendered() {
	this.redraw();
}

function redraw() {
	var day       = this.isCurrentMonth() ? Dates.getDay() : 0,
		month     = this.month,
		year      = this.year,
		curDays   = Dates.getDays(month, year),
		prevMonth = month - 1 >= 0 ? month - 1 : 11,
		prevYear  = prevMonth < 12 ? year : year - 1,
		prevDays  = Dates.getDays(prevMonth, prevYear),
		firstDay  = Dates.getWeekDay(1, month, year),
		firstCell = firstDay > 0 ? firstDay - 1 : 6,
		count     = 1,
		lastCell  = 0, 
		days      = [];

	for (var i = 0; i < firstCell; i++) {
		days.push({num: prevDays - i, another: true});
	}
	days = days.reverse();
	for (var i = firstCell; i < curDays + firstCell; i++) {
		days.push({num: count, current: count == day, marked: this.isMarked(count, month, year)});
		lastCell = i;
		count++;
	}
	var len = days.length;
	var more =  len <= 35 ? 35 - len : 42 - len;		
	for (var i = 1; i <= more; i++) {
		days.push({num: i, another: true});
	}
	this.set({
		'year' : year,
		'month': Dates.getMonthName(month),
		'days' : days
	});
}

function isCurrentMonth() {
	return this.month == Dates.getMonth() && this.year == Dates.getYear();
}

function reset() {
	this.changeMonth();
}

function isMarked() {
	return false;
}

function onPrevClick() {
	this.changeMonth(-1);
}

function onNextClick() {
	this.changeMonth(1);	
}

function changeMonth(value) {
	if (!isNumber(value)) {
		this.initiate();
	} else {
		this.month += value;
		if (this.month == 12) {
			this.month = 0;
			this.year++;
		} else if (this.month == -1) {
			this.month = 11;
			this.year--;
		}
	}
	this.redraw();
}