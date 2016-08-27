<?php

	$topic = $_GET['topic'];

	$items = array();
	switch ($topic) {
		case 'shortcut':
			$items[] = true;
			$items[] = 'Объекты DOM';
			$items[] = false;

			$items[] = '<b>&lt;&gt;</b> - Возвращает элемент компонента помеченный как scope или элемент, в котором компонент отредерен
			<xmp>var scope = <>;</xmp><xmp>var scope = this.getElement();</xmp>
			<xmp>var r = <>.getRect();</xmp><xmp>var r = this.getElement().getRect();</xmp>';

			$items[] = '<b>&lt;input&gt;</b> - Возвращает первый элемент в переделах компонента с тегом input
			<xmp>var input = <input>;</xmp><xmp>var input = this.findElement(\'input\');</xmp>
			<xmp>var v = <input>.value;</xmp><xmp>var v = this.findElement(\'input\').value;</xmp>
			<xmp><input>.clear();</xmp><xmp>this.findElement(\'input\').clear();</xmp>';

			$items[] = '<b>&lt;#container&gt;</b> - Возвращает первый элемент в переделах компонента, имеющий id = container
			<xmp>var cnt = <#container>;</xmp><xmp>var cnt = this.findElement(\'#container\');</xmp>
			<xmp>var h = <#container>.innerHTML;</xmp><xmp>var h = this.findElement(\'#container\').innerHTML;</xmp>
			<xmp><#container>.hide();</xmp><xmp>this.findElement(\'#container\').hide();</xmp>';

			$items[] = '<b>&lt;.item&gt;</b> - Возвращает первый элемент в переделах компонента, имеющий class = item
			<xmp>var i = <.item>;</xmp><xmp>var i = this.findElement(\'.item\');</xmp>
			<xmp>var h = <.item>.innerHTML;</xmp><xmp>var h = this.findElement(\'.item\').innerHTML;</xmp>
			<xmp><.item>.show();</xmp><xmp>this.findElement(\'.item\').show();</xmp>';

			$items[] = '<b>&lt;.block[]&gt;</b> - Возвращает все элементы в переделах компонента, имеющие class = block
			<xmp>var blocks = <.block[]>;</xmp><xmp>var blocks = this.findElements(\'.block\');</xmp>
			<xmp>var firstBlock = <.block[]>[0];</xmp><xmp>var firstBlock = this.findElements(\'.block\')[0];</xmp>';

			$items[] = '<b>&lt;.item[2]&gt;</b> - Возвращает элемент с заданным индексом в переделах компонента, имеющий class = item
			<xmp>var i = <.item[2]>;</xmp><xmp>var i = this.findElements(\'.item\')[2];</xmp>
			<xmp>var h = <.item[2]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[2].innerHTML;</xmp>
			<xmp><.item[2]>.show();</xmp><xmp>this.findElements(\'.item\')[2].show();</xmp>';

			$items[] = '<b>&lt;.item[idx]&gt;</b> - Возвращает элемент с индексом из переменной в переделах компонента, имеющий class = item
			<xmp>var i = <.item[idx]>;</xmp><xmp>var i = this.findElements(\'.item\')[idx];</xmp>
			<xmp>var h = <.item[idx]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[idx].innerHTML;</xmp>
			<xmp><.item[idx]>.show();</xmp><xmp>this.findElements(\'.item\')[idx].show();</xmp>';

			$items[] = '<b>&lt;:list&gt;</b> - Возвращает уже закешированный элемент в переделах компонента, имеющий eid = list
			<xmp><ul class="items-list" eid="list">'."\n\t<li>list item</li>\n".'</ul></xmp>
			<xmp>var l = <:list>;</xmp><xmp>var l = this.getElement(\'list\');</xmp>
			<xmp>var h = <:list>.innerHTML;</xmp><xmp>var h = this.getElement(\'list\').innerHTML;</xmp>
			<xmp><:list>.show();</xmp><xmp>this.getElement(\'list\').show();</xmp>';

			$items[] = '<b>&lt;::userInfo&gt;</b> - Возвращает уже закешированный дочерний компонент, имеющий cmpid = userInfo
			<xmp><component class="UserInfo" cmpid="userInfo"></xmp>
			<xmp>var ui = <::userInfo>;</xmp><xmp>var ui = this.getChild(\'userInfo\');</xmp>
			<xmp>var uie = <::userInfo>.getElement();</xmp><xmp>var uie = this.getChild(\'userInfo\').getElement();</xmp>

			<br><b>Во всех вышеуказанных записях пробелы не допускаются</b>
			<br><b>Далее они могут иметь место</b>';


			$items[] = true;
			$items[] = 'События';
			$items[] = false;

			$items[] = '<b>--> change</b> - Инициирует событие change 
			<xmp>--> change;</xmp><xmp>this.dispatchEvent(\'change\');</xmp>
			<xmp>-->change(a, b);</xmp><xmp>this.dispatchEvent(\'change\', a, b);</xmp>';
			$items[] = '<b>==> everythingReady</b> - Инициирует глобальное событие everythingReady 
			<xmp>==> everythingReady;</xmp><xmp>Globals.dispatchEvent(\'everythingReady\');</xmp>
			<xmp>==> everythingReady (a[0], b[0]);</xmp><xmp>Globals.dispatchEvent(\'everythingReady\', (a[0], b[0]));</xmp>';

			$items[] = true;
			$items[] = 'Диалоговые окна';
			$items[] = false;

			$items[] = '<b>+> OrderFormDialog</b> - Возвращает диалоговое окно с классом OrderFormDialog 
			<xmp>var dialog = +> OrderFormDialog;</xmp><xmp>var dialog = Dialoger.get(OrderFormDialog);</xmp>
			<xmp>+>OrderFormDialog(\'someUniqueId\').doSomeMethod();</xmp><xmp>Dialoger.get(OrderFormDialog, \'someUniqueId\').doSomeMethod();</xmp>';

			$items[] = '<b>++> OrderFormDialog</b> - Показывает диалоговое окно с классом OrderFormDialog 
			<xmp>++> OrderFormDialog;</xmp><xmp>Dialoger.show(OrderFormDialog);</xmp>
			<xmp>++>OrderFormDialog(\'someUniqueId\');</xmp><xmp>Dialoger.show(OrderFormDialog, \'someUniqueId\');</xmp>';

			$items[] = '<b><++ OrderFormDialog</b> - Скрывает диалоговое окно с классом OrderFormDialog 
			<xmp><++ OrderFormDialog;</xmp><xmp>Dialoger.hide(OrderFormDialog);</xmp>
			<xmp><++OrderFormDialog(\'someUniqueId\');</xmp><xmp>Dialoger.hide(OrderFormDialog, \'someUniqueId\');</xmp>';

			$items[] = true;
			$items[] = 'Утилиты';
			$items[] = false;
			$items[] = '<b>object{ \'key\' }</b> - Не вызывая ошибок возвращает поле объекта или массива, если переменная определена и имеет один из этих типов 
			<xmp>var item = object{ \'key\' }</xmp><xmp>var item = Objects.get(object, \'key\');</xmp>';

			$items[] = '<b>list{ 45, defaultValue }</b> - Не вызывая ошибок пытается получить элемент массива с индексом 45, если переменная list не массив или она не имеет указанного индекса, возвращается второй аргумент defaultValue
			<xmp>var item = list{ 45, defaultValue }</xmp><xmp>var item = Objects.get(list, 45, defaultValue);</xmp>';

		break;


		case 'tmp':
			$items[] = 'Место расположения не важно, но лучше располагайте шаблоны в тех же папках, что и JS файлы классов (далее компоненты)';
			$items[] = 'Имя важно, обязательно называйте их так же, как и JS файлы, например <b>UICheckbox</b>';
			$items[] = 'Файл шаблона обязательно должен иметь расширение <b>template</b>';
			$items[] = 'Файл шаблона может содержать в себе множество шаблонов';
			$items[] = "Код шаблона должен иметь вид<xmp>{template .templateName}\n\t<div class=\"container\"></div>\n{/template}</xmp>";
			$items[] = "или просто <xmp>{template .templateName}\n<div class=\"container\"></div></xmp>";
			$items[] = 'Основной шаблон класса должен называться <b>main</b>';
			$items[] = 'Если в файле содержиться только один шаблон, то он автоматически является главным шаблоном, и тогда содержимое файла может иметь вид <xmp><div class="container"></div></xmp>';
			$items[] = 'Компонент не обязательно должен иметь основной родительский контейнер в главном шаблоне, контент шаблона может начинаться как с текста так и с тега, в таком случае его родительский контейнер будет тот, в котором он отрендерен';
			$items[] = 'Вы можете задать главный контейнер добавив ему атрибут <b>scope</b>, например <xmp><div class="container" scope></div></xmp>';
			$items[] = 'Главным контейнером класса будет последний имеющий вышеупомянутый атрибут, а поиск DOM элементов будет производиться исключительно в его пределах';
			$items[] = 'Компонент может не иметь шаблоны, в таком случае он не будет визуально отображаться';
			$items[] = 'Не имея своих шаблонов, компонент наследует их от родительских классов. Так что, если шаблоны называются одинакого, то происходит их переопределение';
			$items[] = 'Для большей эффективности при наследовании делайте главный шаблон у родительского класса, а индивидуальный контент выносите в другие шаблоны например с именем <b>content</b>';
			$items[] = "Для включения дочерних шаблонов в тело текущего используйте код:<xmp><template content value=\"1\" text=\"some text\"></xmp> где <b>value</b> и <b>text</b> входящие аргументы<xmp>{template .content}\n\t<option value=\"{~value}\">{~text}</option>\n{/template}</xmp>";
			$items[] = 'Для вывода входящих аргументов используйте код <b>{~argumentName}</b> или <b>{~argumentName.param}</b> или <b>{~argumentName[\'param\']</b> или <b>{~argumentName[0]}</b>';
			$items[] = "Для включения шаблонов динамически, например в цикле, добавьте шаблону его id параметр:<xmp>{template .templateName as .name}\n\t<div class=\"container\"></div>\n{/template}</xmp>";
			$items[] = "Вызов такого шаблона будет выглядеть так:<xmp><template tmpid=\"name\" value=\"1\" text=\"some text\"></xmp>";
			$items[] = "Тогда динамический вызов будет иметь вид:<xmp>{foreach ~columns as &column}\n\t<template tmpid=\"{&column['name']}\" data=\"{&column['data']}\">\n{/foreach}</xmp>";
		break;

		case 'tmpcode':
			$items[] = "Вывод входящих аргументов<xmp>{template .example1}\n\t<div class=\"container\">{~text}</div>\n{/template}</xmp>";
			$items[] = "Вывод реактивных переменных класса<xmp>{template .example2}\n\t<div class=\"container\">{\$content}</div>\n{/template}</xmp>";
			$items[] = "Вывод определенных в шаблоне (внутри <b>foreach</b> и после <b>let</b>) переменных <xmp>{template .example3}\n\t<div class=\"container\">{&var}</div>\n{/template}</xmp>";
			$items[] = "Вывод результата работы метода компонента<xmp>{template .example4}\n\t<div class=\"container\">{.calculateSome(~a, &b, \$c)}</div>\n{/template}</xmp>";
			$items[] = "Вывод результата работы функции<xmp>{template .example5}\n\t<div class=\"container\">{someFunction(~a, &b, \$c)}</div>\n{/template}</xmp>";
			$items[] = "Оператор If
							<xmp>{template .example6}\n\t{if ~value == 2}\n\t\t<div class=\"container\">{~text}</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example7}\n\t{if isNumber(\$value)}\n\t\t<div class=\"container\">{~text}</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example8}\n\t{if !!&isValid}\n\t\t<div class=\"container\">{.getContent}</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example8}\n\t{if \$visible && \$hasAccess}\n\t\t<div class=\"container\">{.getContent}</div>\n\t{else}\n\t\t<div class=\"unavailable\">You dont have access</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example8}\n\t<div class=\"container\" if=\"{!!~text}\" else=\"No text\">{~text}</div>\n{/template}</xmp>
						";
						$items[] = "Тернарные операторы для вывода контента и определения атрибутов тегов
							<xmp>{template .example9}\n\t<div class=\"container\">{&index == 0 ? 'first' : 'notfirst'}</div>\n{/template}</xmp>
							<xmp>{template .example10}\n\t<div class=\"container\" data-value=\"{\$value?\$value:'none'}\"></div>\n{/template}</xmp>
							<xmp>{template .example11}\n\t<div class=\"container\" text=\"{\$text?\$text}\"></div>\n{/template}</xmp>
						";

						
		break;


		case 'attr':
			$items[] = true;
			$items[] = 'Общие атрибуты';
			$items[] = false;
			$items[] = '<b>if</b> - содержит код вида {~value == 1} или {isArray($list)}, и означает что элемент будет отрендерен только если условие выполниться';
			$items[] = '<b>else</b> - работает только в паре с атрибутом if, и содержит текст или код возвращающий текст, который будет показан вместо элемента';
			$items[] = true;
			$items[] = 'Атрибуты элементов';
			$items[] = false;
			$items[] = '<b>scope</b> - говорит о том, что элемент является главным контейнером, и поиск по компоненту будет производиться в его переделах';
			$items[] = '<b>test</b> - содержит список событий через запятую, которые нужно воссоздать при тестировании приложения. Игнорируется в продакшн-версии<xmp><button test="click,mouseover,mouseout"></xmp>';
			$items[] = true;
			$items[] = 'Атрибуты шаблонов';
			$items[] = false;
			$items[] = '<b>tmpid</b> - говорит, что шаблон будет включен по его id, например динамически<xmp><template tmpid="{&tmpid}"></xmp>';

			$items[] = true;
			$items[] = 'Атрибуты компонетов';
			$items[] = false;
			$items[] = '<b>сmpid</b> - задает компоненту его id, чтобы его можно было найти<xmp><component cmpid="mainMenu"></xmp><xmp>this.getChild(\'mainMenu\');</xmp>';
			$items[] = '<b>class</b> - обязательный атрибут, который использует компилятор, показывает к какому классу принадлежит компонент<xmp><component class="Tooltip" cmpid="tooltip"></xmp>';
			$items[] = '<b>name</b> - обязательный атрибут для компонентов с типом control, нужен для поиска контролов, сборки данных форм или просто вложенных контролов<xmp><control class="Select" name="color"></xmp><xmp>this.getControl(\'color\');</xmp>';

		break;		
	}
	include_once 'header.php';
	$start = 0;
	if ($items[0] === true && $items[2] === false) {
		echo '<h3>'.$items[1].'</h3>';
		$start = 3;
	}

	echo '<ul style="margin-top: 20px;">';
	$caption = false;
	for ($i = $start; $i < count($items); $i++) {
		if ($items[$i] === true) {
			$caption = true;
			echo '</ul><h3>';
		} elseif ($items[$i] === false) {
			$caption = false;
			echo '</h3><ul>';
		} elseif (!$caption) {
			echo '<li style="padding: 5px 0">'.$items[$i].'</li>';
		} else {
			echo $items[$i];
		}
	}
	echo '</ul>';
	include_once 'footer.php';