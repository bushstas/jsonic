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
			$items[] = '�������� ������ ������ ������ ���������� <b>main</b>';
			$items[] = '���� � ����� ����������� ������ ���� ������, �� �� ������������� �������� ������� ��������, � ����� ���������� ����� ����� ����� ��� <xmp><div class="container"></div></xmp>';
			$items[] = '��������� �� ����������� ������ ����� �������� ������������ ��������� � ������� �������, ������� ������� ����� ���������� ��� � ������ ��� � � ����, � ����� ������ ��� ������������ ��������� ����� ���, � ������� �� ����������';
			$items[] = '�� ������ ������ ������� ��������� ������� ��� ������� <b>scope</b> � ����� �������� ���������, �������� <xmp><div class="container" scope="1"></div></xmp>';
			$items[] = '������� ����������� ������ ����� ��������� ������� �������������� �������, � ����� DOM ��������� ����� ������������� ������������� � ��� ��������';
			$items[] = '��������� ����� �� ����� �������, � ����� ������ �� �� ����� ��������� ������������';
			$items[] = '�� ���� ����� ��������, ��������� ��������� �� �� ������������ �������. ��� ���, ���� ������� ���������� ���������, �� ���������� �� ���������������';
			$items[] = '��� ������� ������������� ��� ������������ ������� ������� ������ � ������������� ������, � �������������� ������� �������� � ������ ������� �������� � ������ <b>content</b>';
			$items[] = "��� ��������� �������� �������� � ���� �������� ����������� ���:<xmp><template content value=\"1\" text=\"some text\"></xmp> ��� <b>value</b> � <b>text</b> �������� ���������<xmp>{template .content}\n\t<option value=\"{~value}\">{~text}</option>\n{/template}</xmp>";
			$items[] = '��� ������ �������� ���������� ����������� ��� <b>{~argumentName}</b> ��� <b>{~argumentName.param}</b> ��� <b>{~argumentName[\'param\']</b> ��� <b>{~argumentName[0]}</b>';
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