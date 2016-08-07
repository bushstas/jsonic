<?php

	$topic = $_GET['topic'];

	$items = array();
	switch ($topic) {
		case 'tmp':
			$items[] = '����� ������������ �� �����, �� ����� ������������ ������� � ��� �� ������, ��� � JS ����� ������� (����� ����������)';
			$items[] = '��� �����, ����������� ��������� �� ��� ��, ��� � JS �����, �������� <b>UICheckbox</b>';
			$items[] = '���� ������� ����������� ������ ����� ���������� <b>template</b>';
			$items[] = '���� ������� ����� ��������� � ���� ��������� ��������';
			$items[] = "��� ������� ������ ����� ���<xmp>{template .templateName}\n\t<div class=\"container\"></div>\n{/template}</xmp>";
			$items[] = "��� ������ <xmp>{template .templateName}\n<div class=\"container\"></div></xmp>";
			$items[] = '�������� ������ ������ ������ ���������� <b>main</b>';
			$items[] = '���� � ����� ����������� ������ ���� ������, �� �� ������������� �������� ������� ��������, � ����� ���������� ����� ����� ����� ��� <xmp><div class="container"></div></xmp>';
			$items[] = '��������� �� ����������� ������ ����� �������� ������������ ��������� � ������� �������, ������� ������� ����� ���������� ��� � ������ ��� � � ����, � ����� ������ ��� ������������ ��������� ����� ���, � ������� �� ����������';
			$items[] = '�� ������ ������ ������� ��������� ������� ��� ������� <b>scope</b>, �������� <xmp><div class="container" scope></div></xmp>';
			$items[] = '������� ����������� ������ ����� ��������� ������� �������������� �������, � ����� DOM ��������� ����� ������������� ������������� � ��� ��������';
			$items[] = '��������� ����� �� ����� �������, � ����� ������ �� �� ����� ��������� ������������';
			$items[] = '�� ���� ����� ��������, ��������� ��������� �� �� ������������ �������. ��� ���, ���� ������� ���������� ���������, �� ���������� �� ���������������';
			$items[] = '��� ������� ������������� ��� ������������ ������� ������� ������ � ������������� ������, � �������������� ������� �������� � ������ ������� �������� � ������ <b>content</b>';
			$items[] = "��� ��������� �������� �������� � ���� �������� ����������� ���:<xmp><template content value=\"1\" text=\"some text\"></xmp> ��� <b>value</b> � <b>text</b> �������� ���������<xmp>{template .content}\n\t<option value=\"{~value}\">{~text}</option>\n{/template}</xmp>";
			$items[] = '��� ������ �������� ���������� ����������� ��� <b>{~argumentName}</b> ��� <b>{~argumentName.param}</b> ��� <b>{~argumentName[\'param\']</b> ��� <b>{~argumentName[0]}</b>';
			$items[] = "��� ��������� �������� �����������, �������� � �����, �������� ������� ��� id ��������:<xmp>{template .templateName as .name}\n\t<div class=\"container\"></div>\n{/template}</xmp>";
			$items[] = "����� ������ ������� ����� ��������� ���:<xmp><template tmpid=\"name\" value=\"1\" text=\"some text\"></xmp>";
			$items[] = "����� ������������ ����� ����� ����� ���:<xmp>{foreach ~columns as &column}\n\t<template tmpid=\"{&column['name']}\" data=\"{&column['data']}\">\n{/foreach}</xmp>";
		break;

		case 'tmpcode':
			$items[] = "����� �������� ����������<xmp>{template .example1}\n\t<div class=\"container\">{~text}</div>\n{/template}</xmp>";
			$items[] = "����� ���������� ���������� ������<xmp>{template .example2}\n\t<div class=\"container\">{\$content}</div>\n{/template}</xmp>";
			$items[] = "����� ������������ � ������� (������ <b>foreach</b> � ����� <b>let</b>) ���������� <xmp>{template .example3}\n\t<div class=\"container\">{&var}</div>\n{/template}</xmp>";
			$items[] = "����� ���������� ������ ������ ����������<xmp>{template .example4}\n\t<div class=\"container\">{.calculateSome(~a, &b, \$c)}</div>\n{/template}</xmp>";
			$items[] = "����� ���������� ������ �������<xmp>{template .example5}\n\t<div class=\"container\">{someFunction(~a, &b, \$c)}</div>\n{/template}</xmp>";
			$items[] = "�������� If
							<xmp>{template .example6}\n\t{if ~value == 2}\n\t\t<div class=\"container\">{~text}</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example7}\n\t{if isNumber(\$value)}\n\t\t<div class=\"container\">{~text}</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example8}\n\t{if !!&isValid}\n\t\t<div class=\"container\">{.getContent}</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example8}\n\t{if \$visible && \$hasAccess}\n\t\t<div class=\"container\">{.getContent}</div>\n\t{else}\n\t\t<div class=\"unavailable\">You dont have access</div>\n\t{/if}\n{/template}</xmp>
							<xmp>{template .example8}\n\t<div class=\"container\" if=\"{!!~text}\" else=\"No text\">{~text}</div>\n{/template}</xmp>
						";
						$items[] = "��������� ��������� ��� ������ �������� � ����������� ��������� �����
							<xmp>{template .example9}\n\t<div class=\"container\">{&index == 0 ? 'first' : 'notfirst'}</div>\n{/template}</xmp>
							<xmp>{template .example10}\n\t<div class=\"container\" data-value=\"{\$value?\$value:'none'}\"></div>\n{/template}</xmp>
							<xmp>{template .example11}\n\t<div class=\"container\" text=\"{\$text?\$text}\"></div>\n{/template}</xmp>
						";

						
		break;


		case 'attr':
			$items[] = true;
			$items[] = '����� ��������';
			$items[] = false;
			$items[] = '<b>if</b> - �������� ��� ���� {~value == 1} ��� {isArray($list)}, � �������� ��� ������� ����� ���������� ������ ���� ������� �����������';
			$items[] = '<b>else</b> - �������� ������ � ���� � ��������� if, � �������� ����� ��� ��� ������������ �����, ������� ����� ������� ������ ��������';
			$items[] = true;
			$items[] = '�������� ���������';
			$items[] = false;
			$items[] = '<b>scope</b> - ������� � ���, ��� ������� �������� ������� �����������, � ����� �� ���������� ����� ������������� � ��� ���������';
			$items[] = '<b>test</b> - �������� ������ ������� ����� �������, ������� ����� ���������� ��� ������������ ����������. ������������ � ��������-������<xmp><button test="click,mouseover,mouseout"></xmp>';
			$items[] = true;
			$items[] = '�������� ��������';
			$items[] = false;
			$items[] = '<b>tmpid</b> - �������, ��� ������ ����� ������� �� ��� id, �������� �����������<xmp><template tmpid="{&tmpid}"></xmp>';

			$items[] = true;
			$items[] = '�������� ����������';
			$items[] = false;
			$items[] = '<b>�mpid</b> - ������ ���������� ��� id, ����� ��� ����� ���� �����<xmp><component cmpid="mainMenu"></xmp><xmp>this.getChild(\'mainMenu\');</xmp>';
			$items[] = '<b>class</b> - ������������ �������, ������� ���������� ����������, ���������� � ������ ������ ����������� ���������<xmp><component class="Tooltip" cmpid="tooltip"></xmp>';
			$items[] = '<b>name</b> - ������������ ������� ��� ����������� � ����� control, ����� ��� ������ ���������, ������ ������ ���� ��� ������ ��������� ���������<xmp><control class="Select" name="color"></xmp><xmp>this.getControl(\'color\');</xmp>';

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