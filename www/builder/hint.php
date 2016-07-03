<?php

	$topic = $_GET['topic'];

	$items = array();
	switch ($topic) {
		case 'tmp':
			$items[] = 'Место расположения не важно, но лучше располагайте шаблоны в тех же папках, что и JS файлы классов (далее компоненты)';
			$items[] = 'Имя важно, обязательно называйте их так же, как и JS файлы, например <b>UICheckbox</b>';
			$items[] = 'Файл шаблона обязательно должен иметь расширение <b>template</b>';
			$items[] = 'Файл шаблона может содержать в себе множество шаблонов';
			$items[] = "Код шаблона должен иметь вид<xmp>{template .templateName}\n\t<div class=\"container\"></div>\n{/template}</xmp>";
			$items[] = 'Основной шаблон класса должен называться <b>main</b>';
			$items[] = 'Если в файле содержиться только один шаблон, то он автоматически является главным шаблоном, и тогда содержимое файла может иметь вид <xmp><div class="container"></div></xmp>';
			$items[] = 'Компонент не обязательно должен иметь основной родительский контейнер в главном шаблоне, контент шаблона может начинаться как с текста так и с тега, в таком случае его родительский контейнер будет тот, в котором он отрендерен';
			$items[] = 'Вы можете задать главный контейнер добавив ему атрибут <b>scope</b> с любым непустым значением, например <xmp><div class="container" scope="1"></div></xmp>';
			$items[] = 'Главным контейнером класса будет последний имеющий вышеупомянутый атрибут, а поиск DOM элементов будет производиться исключительно в его пределах';
			$items[] = 'Компонент может не иметь шаблоны, в таком случае он не будет визуально отображаться';
			$items[] = 'Не имея своих шаблонов, компонент наследует их от родительских классов. Так что, если шаблоны называются одинакого, то происходит их переопределение';
			$items[] = 'Для большей эффективности при наследовании делайте главный шаблон у родительского класса, а индивидуальный контент выносите в другие шаблоны например с именем <b>content</b>';
			$items[] = "Для включения дочерних шаблонов в тело текущего используйте код:<xmp><template content value=\"1\" text=\"some text\"></xmp> где <b>value</b> и <b>text</b> входящие аргументы<xmp>{template .content}\n\t<option value=\"{~value}\">{~text}</option>\n{/template}</xmp>";
			$items[] = 'Для вывода входящих аргументов используйте код <b>{~argumentName}</b> или <b>{~argumentName.param}</b> или <b>{~argumentName[\'param\']</b> или <b>{~argumentName[0]}</b>';
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
						";

						
		break;
	}
	include_once 'header.php';
	echo '<ul style="margin-top: 20px;">';
	foreach ($items as $item) {
		echo '<li style="padding: 5px 0">'.$item.'</li>';
	}
	echo '</ul>';
	include_once 'footer.php';