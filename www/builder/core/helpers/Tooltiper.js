function Tooltiper() {
	var target, eventHandler, event;
	var request, tooltipElement;
	var tooltip = __TC;
	var tooltipApi = __TA;
	var tooltipClass = '->> tooltiped';
	var addClass, text, position, key, caption;
	
	var onBodyMouseOver = function(e) {
		event = e;
		target = e.target;
		if (target.hasClass(tooltipClass)) {
			init();
			if (text) {
				show();
			} else if (key) {
				load();
			}
		}
	};
	var init = function() {
		if (isFunction(tooltip)) {
			createPopup();
		}
		key = '';
		text = target.attr('txt');
		addClass = target.attr('cls');
		position = target.attr('pos');
		caption = target.attr('cap');

		if (!text) {
			key = target.attr('key');
		}
	};
	var createPopup = function() {
		tooltip = new tooltip();
		tooltip.render(document.body);
		tooltipElement = tooltip.getElement();
	};
	var show = function() {
		tooltip.set('shown', true);
		var coords = getCoords();
		tooltip.set({
			'caption': caption,
			'text': text,
			'left': coords.x,
			'top': coords.y
		});
		eventHandler.listenOnce(target, 'mouseleave', onLeave);
	};
	var getCoords = function() {
		var marginLeft = 0, marginTop = 0;		
		var rect = target.getRect();

		var tooltipRect = tooltipElement.getRect();
		var coordX  = rect.left;
		var coordY  = rect.top;
		var coords = {x: coordX, y: coordY};

		switch (position) {
			case 'left': 
				coords.y += Math.round(rect.height / 2) - 20;
			break;
			case 'bottom': 
				coords.x += Math.round(rect.width / 2);
				coords.y += rect.height + 5;
			break;
			case 'top': 
				coords.x += Math.round(rect.width / 2);
			break;
			case 'left-bottom': 
				coords.y += rect.height + 5;
			break;
			case 'right-bottom': 
				coords.x += rect.width;
				coords.y += rect.height + 5;
			break;
			case 'left-top': 
				coords.x += rect.width;
			break;
			case 'right-top': 
				coords.x += rect.width;
			break;
			default:
				coords.x += rect.width + 15;
				coords.y += Math.round(rect.height / 2) - 20;
		}
		
		if (position == 'left') {
			marginLeft = -tooltipRect.width - 10 + 'px';
		} else if (position == 'top' || position == 'bottom') {
			marginLeft = -Math.round(tooltipRect.width / 2) + 'px';
		} else if (position == 'right-top' || position == 'right-bottom') {
			marginLeft = -tooltipRect.width + 'px';
		} else if (position == 'left-top') {
			marginLeft = -rect.width + 'px';
		}
		if (position == 'top' || position == 'left-top' || position == 'right-top') {
			marginTop = -tooltipRect.height - 10 + 'px';
		}
		
		if (rect.width < 30 && ['left-bottom', 'right-bottom', 'bottom', 'left-top', 'right-top', 'top'].indexOf(position) != -1) {
			coords.x -= 15;
		}

		coords.x += marginLeft;
		coords.y += marginTop;
		return coords;		
	};
	var onLeave = function() {
		tooltip.set('shown', false);
	};
	var load = function() {
		if (isString(tooltipApi)) {
			if (isUndefined(request)) {
				request = new AjaxRequest(tooltipApi, onLoad);
			}
			request.execute({'name': key});
		}
	};
	var onLoad = function(data) {
		text = Objects.get(data, 'text');
		var cap = Objects.get(data, 'caption');
		if (cap && isString(cap)) {
			caption = cap;
			target.attr('cap', cap);
		}
		if (text && isString(text)) {
			target.attr('txt', text);
			show();
		}
	};
	if (isFunction(tooltip)) {
		eventHandler = new EventHandler();
		var body = document.documentElement;
		body.addEventListener('mouseover', onBodyMouseOver, false);
	}
}
Tooltiper = new Tooltiper();