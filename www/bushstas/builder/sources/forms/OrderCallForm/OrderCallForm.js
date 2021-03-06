form OrderCallForm

initial props = {
	'action': Api.orderCall.send,
	'method': 'POST',
	'className': '->> app-order-call',
	'controls': [
		#nameInput,
		#emailInput,
		#phoneInput,
		{
			'caption': @callTopic,
			'class': '->> half-width',
			'controlClass': Select,
			'controlProps': {
				'name': 'topic',
				'options': Dictionary.orderCallTopics
			}			
		},
		{
			'caption': @callDate,
			'class': '->> half-width',
			'controlClass': Select,
			'controlProps': {
				'name': 'date',
				'options': this.getDateOptions()
			}			
		},
		{
			'caption': @callTime,
			'class': '->> half-width',
			'controlClass': Select,
			'controlProps': {
				'name': 'time',
				'options': Dictionary.timeOptions
			}
		},
		{
			'caption': @descr,
			'controlClass': Textarea,
			'controlProps': {
				'name': 'comment'
			}
		}
	],
	'submit': {
		'value': @orderCall,
		'class': '->> standart-button ->> green-button ->> send-button'
	}
};

function onRendered() {
	this.setControlValue('name', User.getAttribute('name'));
	this.setControlValue('phone', User.getAttribute('phone'));

	var email = User.getAttribute('email');
	if (email) {
		this.setControlValue('email', email);
		this.enableControl('email', false);
	}
}

function getDateOptions() {
	var monthNames = Dictionary.get('monthNames'),
		date  = new Date(),
		year  = date.getFullYear(),
		time  = date.getHours,
		month = date.getMonth() + 1,
		day   = date.getDate(),
		days  = 33 - new Date(year, month - 1, 33).getDate(),
		dates = [], d, m = month, dayInWeek, count = 0, index = 0, txt;
	var prev = 0;
	while (count < 10) {
		d = day + index;
		if (day + index > days) {
			d = d - days;
			m = month + 1;
			if (m > 12) {
				break;
			}
		}
		dayInWeek = new Date(year, m - 1, d).getDay();
		if (dayInWeek == 0 || dayInWeek > 5) {
			index++;
			continue;
		}	
		dayInWeek = Dictionary.get('dayNames')[dayInWeek];
		txt = count > 1 || (!!prev &&prev != d - 1) ? d + ' ' + monthNames[m] + ', ' + dayInWeek : (count == 0 ? @today : @tomorrow) + ', ' + d + ' ' + monthNames[m];
		dates.push({'value': txt, 'title': txt});
		count++;
		index++;
		prev = d;
	}
	date = day + ' ' + monthNames[month];
	return dates;
}

function validateTime() {
	var dateSelect = this.getControl('date');
	var timeSelect = this.getControl('time');
	var dateValue = dateSelect.getValue();
    var isToday = (new RegExp(@today)).test(dateValue);
    if (isToday) {
 		var d = new Date();
	    var hours = [11, 13, 15];
	    var minutes = [0, 0, 30];
	    var moscowTime = d.getUTCHours() + 3;
	    var minute = d.getMinutes();
	    var disabledIndexes = [];
	    for (var i = 0; i < hours.length; i++) {
	    	if (moscowTime > hours[i] || (moscowTime == hours[i] && minute >= minutes[i])) {
	    		disabledIndexes.push(i);
	    	}
	    }
	    if (disabledIndexes.length == hours.length) {
	    	dateSelect.enableOption(0, false);
	    } else {
	    	for (i = 0; i < disabledIndexes.length; i++) {
	    		timeSelect.enableOption(disabledIndexes[i], false);
	    	}
	    }
	} else {
		timeSelect.enableOption(0, true);
		timeSelect.enableOption(1, true);
		timeSelect.enableOption(2, true);
	}
}