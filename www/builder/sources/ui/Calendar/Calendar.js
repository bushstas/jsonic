component Calendar

function initiate() {
	this.month = 0;
	this.year = 0;
}

function onRendered() {
	this.redraw(Dates.getMonth(), Dates.getYear());
}

function redraw(month, year) {
	var day       = this.isCurrentMonth() ? Dates.getDay() : 0,
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
		days.push({'num': prevDays - i, 'another': true});
	}
	days = days.reverse();
	for (var i = firstCell; i < curDays + firstCell; i++) {
		days.push({'num': count, 'current': count == day});
		lastCell = i;
		count++;
	}
	count = 1;
	for (var i = curDays + firstCell; i < 42; i++) {
		days.push({'num': count, 'another': true});
		if (days[34] && days[34]['num'] < curDays) {
			break;
		}
		count++;
	}

	this.set({
		'year' : Dates.getYear(),
		'month': Dates.getMonthName(),
		'days' : days
	});
}

function isCurrentMonth() {
	return this.month == 0 && this.year == 0;
}

function onPrevClick() {
	this.changeMonth(-1);
}

function onNextClick() {
	this.changeMonth(1);	
}

function changeMonth(value) {
	var	date = Dates.getDate();	
	if (!value) {
		this.month = date.month;
		this.year = date.year;
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
	this.redraw(this.month, this.year);
}