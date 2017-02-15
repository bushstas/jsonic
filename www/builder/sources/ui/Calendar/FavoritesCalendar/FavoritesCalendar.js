component FavoritesCalendar extends Calendar

initial controllers = [
	{
		'controller': Favorites,
		'on': {
			'load': this.onLoadFavorites
		}
	}
]

initial events = {
	'click': {
		'->> marked': this.onMarkedDayClick
	}
}

function onRendered() {
	this.month = Dates.getMonth();
	this.year = Dates.getYear();
}

function isMarked(d, m, y) {
	return Objects.has(this.tenderByDates, d + '.' + (m + 1) + '.' + y);
}

function onLoadFavorites(data) {
	var timestamp;
	this.tenderByDates = {};
	for (var i = 0; i < data.length; i++) {
		if (data[i]['phase_'] == 1) {
			timestamp = data[i]['finishdocdate'].replace(/\.(\d+)$/, ".20$1").replace(/0(?=\d\.)/g, '');
			this.tenderByDates[timestamp] = this.tenderByDates[timestamp] || [];
			this.tenderByDates[timestamp].push(data[i]);
		}
	}
	this.redraw();
}

function onMarkedDayClick(target) {
	var timestamp = target.innerHTML + '.' + (this.month + 1) + '.' + this.year;
	if (isArray(this.tenderByDates[timestamp])) {
		Dialoger.show(CalendarFavorites, {
			'title': @calendarFavoritesTitle + ' ' + Dates.getFormattedDate(timestamp, @calendarFavoritesDateFormat),
			'tenders': this.tenderByDates[timestamp]
		});
	}
}