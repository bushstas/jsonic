<?php

	$data = array(
		'mode' => 3,
		'name' => 'Tooltiper',
		'before' => "
			var target, eventHandler, event,
				request, tooltipElement, timer,
				tooltipClass = '->>tooltiped',
				addClass, text, position, key,
				caption, delay, corrector, tooltip;
		",
		'after' => "
			if (isFunction(".CONST_TOOLTIPCLASS.")) {
				var eh = ".CONST_GLOBAL.".get('EventHandler');
				eventHandler = new eh();
				document.documentElement.addEventListener('mouseover', onBodyMouseOver, false);

				tooltip = new ".CONST_TOOLTIPCLASS."();
				".CONST_CORE.".initiate.call(tooltip);
				tooltip.render(document.body);
				tooltipElement = tooltip.getElement();
			}
		",
		'privateMethods' => array(
			'onBodyMouseOver' => array(
				'args' => array('e'),
				'body' => "
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
				"
			),
			'init' => array(
				'body' => "
					window.clearTimeout(timer);
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
				"
			),
			'showWithDelay' => array(
				'body' => "
					timer = window.setTimeout(show, 500);
				"
			),
			'show' => array(
				'body' => "
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
				"
			),
			'getCoords' => array(
				'body' => "
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
				"
			),
			'onLeave' => array(
				'body' => "
					window.clearTimeout(timer);
					tooltip.set('shown', false);
				"
			),
			'load' => array(
				'body' => "
					if (isString(".CONST_TOOLTIPAPI.")) {
						if (isUndefined(request)) {
							var ajr = ".CONST_GLOBAL.".get('AjaxRequest');
							request = new ajr(".CONST_TOOLTIPAPI.", onLoad);
						}
						request.execute({'name': key});
					}
				"
			),
			'onLoad' => array(
				'args' => array('data'),
				'body' => "
					text = ".CONST_OBJECTS.".get(data, 'text');
					var cap = ".CONST_OBJECTS.".get(data, 'caption');
					if (cap && isString(cap)) {
						caption = cap;
						target.setData('caption', cap);
					}
					if (text && isString(text)) {
						target.setData('text', text);
						show();
					}
				"
			)
		)
	);
?>