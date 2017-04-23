{{GLOBAL}}.set(function() {
	var target, eventHandler, event;
	var request, tooltipElement, timer;
	var tooltip = {{TOOLTIPCLASS}},
		tooltipApi = {{TOOLTIPAPI}};
		
	var tooltipClass = '->> tooltiped';
	var addClass, text, position, key,
		caption, delay, corrector;
	
	var onBodyMouseOver = function(e) {
		event = e;
		target = e.target;
		if (target.hasClass(tooltipClass)) {
			init();
			if (text) {
				if (delay) {
					showWithDelay()
				} else {
					show();
				}
			} else if (key) {
				load();
			}
		}
	};
	var init = function() {
		window.clearTimeout(timer);
		if (isFunction(tooltip)) {
			createPopup();
		}
		key = '';
		text = target.getData('text');
		addClass = target.getData('class');
		position = target.getData('position');
		caption = target.getData('caption');
		delay = target.getData('delay');
		corrector = target.getData('corrector');

		if (!text) {
			key = target.getData('key');
		}
		eventHandler.listenOnce(target, 'mouseleave', onLeave);
	};
	var createPopup = function() {
		tooltip = new tooltip();
		{{CORE}}.initiate.call(tooltip);
		tooltip.render(document.body);
		tooltipElement = tooltip.getElement();
	};
	var showWithDelay = function() {
		timer = window.setTimeout(show, 500);
	};
	var show = function() {
		tooltip.set({
			'shown': true,
			'corrector': corrector,
			'caption': caption,
			'text': text
		});
		var coords = getCoords();
		tooltip.set({
			'left': Math.round(coords.x),
			'top': Math.round(coords.y)
		});
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
			marginLeft = -tooltipRect.width - 10;
		} else if (position == 'top' || position == 'bottom') {
			marginLeft = -Math.round(tooltipRect.width / 2);
		} else if (position == 'right-top' || position == 'right-bottom') {
			marginLeft = -tooltipRect.width;
		} else if (position == 'left-top') {
			marginLeft = -rect.width;
		}
		if (position == 'top' || position == 'left-top' || position == 'right-top') {
			marginTop = -tooltipRect.height - 10;
		}
		
		if (rect.width < 30 && ['left-bottom', 'right-bottom', 'bottom', 'left-top', 'right-top', 'top'].indexOf(position) != -1) {
			coords.x -= 15;
		}
		coords.x += marginLeft;
		coords.y += marginTop;
		return coords;
	};
	var onLeave = function() {
		window.clearTimeout(timer);
		tooltip.set('shown', false);
	};
	var load = function() {
		if (isString(tooltipApi)) {
			if (isUndefined(request)) {
				request = new {{GLOBAL}}.get('AjaxRequest')(tooltipApi, onLoad);
			}
			request.execute({'name': key});
		}
	};
	var onLoad = function(data) {
		text = Objects.get(data, 'text');
		var cap = Objects.get(data, 'caption');
		if (cap && isString(cap)) {
			caption = cap;
			target.setData('caption', cap);
		}
		if (text && isString(text)) {
			target.setData('text', text);
			show();
		}
	};
	if (isFunction(tooltip)) {
		eventHandler = new EventHandler();
		var body = document.documentElement;
		body.addEventListener('mouseover', onBodyMouseOver, false);
	}
}, 'Tooltiper');