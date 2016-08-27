<?php

	$topic = $_GET['topic'];

	$items = array();
	switch ($topic) {
		case 'shortcut':
			$items[] = true;
			$items[] = '������� DOM';
			$items[] = false;

			$items[] = '<b>&lt;&gt;</b> - ���������� ������� ���������� ���������� ��� scope ��� �������, � ������� ��������� ���������
			<xmp>var scope = <>;</xmp><xmp>var scope = this.getElement();</xmp>
			<xmp>var r = <>.getRect();</xmp><xmp>var r = this.getElement().getRect();</xmp>';

			$items[] = '<b>&lt;input&gt;</b> - ���������� ������ ������� � ��������� ���������� � ����� input
			<xmp>var input = <input>;</xmp><xmp>var input = this.findElement(\'input\');</xmp>
			<xmp>var v = <input>.value;</xmp><xmp>var v = this.findElement(\'input\').value;</xmp>
			<xmp><input>.clear();</xmp><xmp>this.findElement(\'input\').clear();</xmp>';

			$items[] = '<b>&lt;#container&gt;</b> - ���������� ������ ������� � ��������� ����������, ������� id = container
			<xmp>var cnt = <#container>;</xmp><xmp>var cnt = this.findElement(\'#container\');</xmp>
			<xmp>var h = <#container>.innerHTML;</xmp><xmp>var h = this.findElement(\'#container\').innerHTML;</xmp>
			<xmp><#container>.hide();</xmp><xmp>this.findElement(\'#container\').hide();</xmp>';

			$items[] = '<b>&lt;.item&gt;</b> - ���������� ������ ������� � ��������� ����������, ������� class = item
			<xmp>var i = <.item>;</xmp><xmp>var i = this.findElement(\'.item\');</xmp>
			<xmp>var h = <.item>.innerHTML;</xmp><xmp>var h = this.findElement(\'.item\').innerHTML;</xmp>
			<xmp><.item>.show();</xmp><xmp>this.findElement(\'.item\').show();</xmp>';

			$items[] = '<b>&lt;.block[]&gt;</b> - ���������� ��� �������� � ��������� ����������, ������� class = block
			<xmp>var blocks = <.block[]>;</xmp><xmp>var blocks = this.findElements(\'.block\');</xmp>
			<xmp>var firstBlock = <.block[]>[0];</xmp><xmp>var firstBlock = this.findElements(\'.block\')[0];</xmp>';

			$items[] = '<b>&lt;.item[2]&gt;</b> - ���������� ������� � �������� �������� � ��������� ����������, ������� class = item
			<xmp>var i = <.item[2]>;</xmp><xmp>var i = this.findElements(\'.item\')[2];</xmp>
			<xmp>var h = <.item[2]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[2].innerHTML;</xmp>
			<xmp><.item[2]>.show();</xmp><xmp>this.findElements(\'.item\')[2].show();</xmp>';

			$items[] = '<b>&lt;.item[idx]&gt;</b> - ���������� ������� � �������� �� ���������� � ��������� ����������, ������� class = item
			<xmp>var i = <.item[idx]>;</xmp><xmp>var i = this.findElements(\'.item\')[idx];</xmp>
			<xmp>var h = <.item[idx]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[idx].innerHTML;</xmp>
			<xmp><.item[idx]>.show();</xmp><xmp>this.findElements(\'.item\')[idx].show();</xmp>';

			$items[] = '<b>&lt;:list&gt;</b> - ���������� ��� �������������� ������� � ��������� ����������, ������� eid = list
			<xmp><ul class="items-list" eid="list">'."\n\t<li>list item</li>\n".'</ul></xmp>
			<xmp>var l = <:list>;</xmp><xmp>var l = this.getElement(\'list\');</xmp>
			<xmp>var h = <:list>.innerHTML;</xmp><xmp>var h = this.getElement(\'list\').innerHTML;</xmp>
			<xmp><:list>.show();</xmp><xmp>this.getElement(\'list\').show();</xmp>';

			$items[] = '<b>&lt;::userInfo&gt;</b> - ���������� ��� �������������� �������� ���������, ������� cmpid = userInfo
			<xmp><component class="UserInfo" cmpid="userInfo"></xmp>
			<xmp>var ui = <::userInfo>;</xmp><xmp>var ui = this.getChild(\'userInfo\');</xmp>
			<xmp>var uie = <::userInfo>.getElement();</xmp><xmp>var uie = this.getChild(\'userInfo\').getElement();</xmp>

			<br><b>�� ���� ������������� ������� ������� �� �����������</b>
			<br><b>����� ��� ����� ����� �����</b>';


			$items[] = true;
			$items[] = '�������';
			$items[] = false;

			$items[] = '<b>--> change</b> - ���������� ������� change 
			<xmp>--> change;</xmp><xmp>this.dispatchEvent(\'change\');</xmp>
			<xmp>-->change(a, b);</xmp><xmp>this.dispatchEvent(\'change\', a, b);</xmp>';
			$items[] = '<b>==> everythingReady</b> - ���������� ���������� ������� everythingReady 
			<xmp>==> everythingReady;</xmp><xmp>Globals.dispatchEvent(\'everythingReady\');</xmp>
			<xmp>==> everythingReady (a[0], b[0]);</xmp><xmp>Globals.dispatchEvent(\'everythingReady\', (a[0], b[0]));</xmp>';

			$items[] = true;
			$items[] = '���������� ����';
			$items[] = false;

			$items[] = '<b>+> OrderFormDialog</b> - ���������� ���������� ���� � ������� OrderFormDialog 
			<xmp>var dialog = +> OrderFormDialog;</xmp><xmp>var dialog = Dialoger.get(OrderFormDialog);</xmp>
			<xmp>+>OrderFormDialog(\'someUniqueId\').doSomeMethod();</xmp><xmp>Dialoger.get(OrderFormDialog, \'someUniqueId\').doSomeMethod();</xmp>';

			$items[] = '<b>++> OrderFormDialog</b> - ���������� ���������� ���� � ������� OrderFormDialog 
			<xmp>++> OrderFormDialog;</xmp><xmp>Dialoger.show(OrderFormDialog);</xmp>
			<xmp>++>OrderFormDialog(\'someUniqueId\');</xmp><xmp>Dialoger.show(OrderFormDialog, \'someUniqueId\');</xmp>';

			$items[] = '<b><++ OrderFormDialog</b> - �������� ���������� ���� � ������� OrderFormDialog 
			<xmp><++ OrderFormDialog;</xmp><xmp>Dialoger.hide(OrderFormDialog);</xmp>
			<xmp><++OrderFormDialog(\'someUniqueId\');</xmp><xmp>Dialoger.hide(OrderFormDialog, \'someUniqueId\');</xmp>';

			$items[] = true;
			$items[] = '�������';
			$items[] = false;
			$items[] = '<b>object{ \'key\' }</b> - �� ������� ������ ���������� ���� ������� ��� �������, ���� ���������� ���������� � ����� ���� �� ���� ����� 
			<xmp>var item = object{ \'key\' }</xmp><xmp>var item = Objects.get(object, \'key\');</xmp>';

			$items[] = '<b>list{ 45, defaultValue }</b> - �� ������� ������ �������� �������� ������� ������� � �������� 45, ���� ���������� list �� ������ ��� ��� �� ����� ���������� �������, ������������ ������ �������� defaultValue
			<xmp>var item = list{ 45, defaultValue }</xmp><xmp>var item = Objects.get(list, 45, defaultValue);</xmp>';

		break;


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