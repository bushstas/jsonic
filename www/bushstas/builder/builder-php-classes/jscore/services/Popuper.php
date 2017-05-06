<?php

	$data = array(
		'name' => 'Popuper',
		'var' => 'Popuper',
		'define' => true,
		'mode' => 2,
		'before' => "
			var components, elements, skippedAll;
		",
		'after' => "
			reset();
			var body = document.documentElement;
			body.addEventListener('mousedown', onBodyMouseDown, false);
		",
		'privateMethods' => array(
			'reset' => array(
				'body' => "
					components = [];
					elements = [];
				"
			),
			'onBodyMouseDown' => array(
				'args' => array('e'),
				'body' => "
					if (skippedAll) return;
					var element;
					for (var i = 0; i < components.length; i++) {
						element = elements[i];
						if (!isElement(element) || !e.targetHasAncestor(element)) {
							components[i].hide();
							reset();
						}
					}	
				"
			)
		),
		'thisMethods' => array(
			'watch' => array(
				'args' => array('component', 'element'),
				'body' => "
					if (components.indexOf(component) == -1) {
						components.push(component);
						if (isString(element)) element = component.findElement(element);
						elements.push(element || component.getElement() || null);
					}
				"
			),
			'skipAll' => array(
				'args' => array('isSkipped'),
				'body' => "
					skippedAll = isSkipped;
				"
			)			
		)
	);
?>
