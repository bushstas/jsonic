<?php

	$data = array(
		'name' => CONST_DECLINER,
		'var' => CONST_DECLINER,
		'define' => true,
		'mode' => 2,
		'privateMethods' => array(
			'getVariant' => array(
				'args' => array('num'),
				'body' => "
					var n, m;
					num = num.toString();
					m = num.charAt(num.length - 1);
					if (num.length > 1) n = num.charAt(num.length - 2); 
					else n = 0;
					if (n == 1) return 2;
					else { 
						if (m == 1) return 0;
						else if (m > 1 && m < 5) return 1;
						else return 2;
					}
				"
			)
		),
		'thisMethods' => array(
			'getCount' => array(
				'args' => array('key', 'num'),
				'body' => "
					if (isArray(num)) num = num.length;
					return num + ' ' + this.get(key, num);
				"
			),
			'get' => array(
				'args' => array('key', 'num'),
				'body' => "
					if (isArray(num)) num = num.length;
					if (!isNumber(num)) return '';
					return ".CONST_OBJECTS.".get(".CONST_OBJECTS.".get(".CONST_WORDS.", key, ''), getVariant(num), '');
				"
			),
		)
	);
?>