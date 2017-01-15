<?php

	$topic = $_GET['topic'];

	$items = array();
	switch ($topic) {
		case 'config':
			$items[] = true;
			$items[] = '����������';
			$items[] = false;

			$items[] = "<b>indexPage</b> - �������� ���������� ����� ������� ����������. ���������� ������������� ���������� ��������� �����
			<xmp>\"indexPage\": \"index.html\"</xmp>";
			$items[] = "<b>title</b> - ��������� ��������. ���������� ������������� ��������� ��� �������� � HTML ��� ��������
			<xmp>\"title\": \"My JavaScript application\"</xmp>";
			$items[] = "<b>charset</b> - ��������� ��������. ���������� ������������� ��������� ��� �������� � HTML ��� ��������
			<xmp>\"charset\": \"UTF-8\"</xmp>";
			$items[] = "<b>jsFolder</b> - ���������� ������������ ����� �����, ��� ����� ��������� ���������������� JS ����.
			<xmp>\"jsFolder\": \"js\"</xmp>";
			$items[] = "<b>cssFolder</b> - ���������� ������������ ����� �����, ��� ����� ��������� ���������������� CSS ����.
			<xmp>\"cssFolder\": \"css\"</xmp>";
			$items[] = "<b>imagesFolder</b> - ���������� ������������ ����� �����, ��� ����� ��������� ������� �����������. ��������� ��� css ���������� <b>imgsrc</b>. ��� ����� ��������� ����������, �������� ��������� �� <b>css ������</b>
			<xmp>\"imagesFolder\": \"images\"</xmp>";
			$items[] = "<b>compiledJs</b> - �������� ����������������� JS �����.
			<xmp>\"compiledJs\": \"base\"</xmp>";
			$items[] = "<b>compiledCss</b> - �������� ����������������� CSS �����.
			<xmp>\"compiledCss\": \"styles\"</xmp>";
			$items[] = "<b>pathToApi</b> - ���� � ����������, ��� ������������� ��������� api ����������, ������������ ����� �����
			<xmp>\"pathToApi\": \"api\"</xmp>";
			$items[] = "<b>container</b> - ����� DOM ��������, ���� ����� ����������� ������ � ����� view. ��� ������� ������ ������������� � ������� ������ ���������� � ��������� <b>entry</b>. ��� ��� ����������, ����� ������������ ������ ������� � ����� ���� DOM
			<xmp>\"container\": \"app-view-container\"</xmp>";


			$items[] = true;
			$items[] = '�������� ����';
			$items[] = false;

			$items[] = "<b>entry</b> - �������� ������ ����� ����� � ����� application. ������������ � ����������� ����������
			<xmp>\"entry\": \"App\"</xmp>";
			$items[] = "<b>scope</b> - ���� � ����������, � �������� ������� ����� ������������� ����� ������ ����������, ������������ ���������� builder
			<xmp>\"scope\": \"./sources\"</xmp>";
			$items[] = "<b>core</b> - ���� � ����������, ��� ������������� ���� ����������, ������������ ���������� builder
			<xmp>\"core\": \"./core\"</xmp>";
			$items[] = "<b>tests</b> - ���� � ����������, ��� ������������� �������� ������ ����������, ������������ ���������� builder
			<xmp>\"tests\": \"./tests\"</xmp>";
			$items[] = "<b>blanks</b> - ���� � ����������, ��� ������������� ��������� ��� ���������� HTML � JS ����, ������������ ���������� builder. ��� �� ������ ���������, ��� ����� ��������� ��� ��������� ����
			<xmp>\"blanks\": \"./blanks\"</xmp>";
			$items[] = "<b>scripts</b> - ���� � ����������, ��� ������������� ��������� JS �������, ������� ����� �������� � ����������, ������������ ���������� builder
			<xmp>\"scripts\": \"./scripts\"</xmp>";
			$items[] = "<b>views</b> - �������� ����������, � ������� ����� ������������� ������ � ����� view. ��������� ������������ ��� �������� �������� ������ �������������� � ����, ������������ ����������� ��������� ������. ��� ��������� ������������ ������ ��������� � ���� <b>routes</b> ��������� <b>router</b>. ���������� ����� ����� �������������, ���� �� ���, � ����� ������������ � ��������� <b>scope</b>. ������������� ����� ������ �� ������, ������� ��� ���, ������������ �� ����� ���������. ��� ��������� ��������� ������� � ����� view, �������� �������� <b>blanks</b>
			<xmp>\"views\": \"views\"</xmp>";
			
			$items[] = true;
			$items[] = '������';
			$items[] = false;

			$items[] = "<b>router</b> - �������� ������� � ����� view, ��������������� �������� ����������, � ����� � ���
			<xmp>\"router\": \"{ ... }\"</xmp>
			<ul>
				<li>
					<b>routes</b> - ������, ���������� ������ ������� � ����� view. �� �������� � ���� ������ ��� ��������� ������ (404, 401, 403, ...)
					<xmp>\"routes\": \"[ {...}, {...}, ... ]\"</xmp>
					<ul>
						<li>
							<b>name</b> - ����������� �������� / �������� ����������, ����������� � ��������� �����
							<xmp>\"name\": \"main\"</xmp>
						</li>
						<li>
							<b>view</b> - �������� view ������, ����������� � ������� �����
							<xmp>\"view\": \"Main\"</xmp>
						</li>
						<li>
							<b>title</b> - ��������� ��������
							<xmp>\"title\": \"Main page\"</xmp>
						</li>
						<li>
							<b>accessLevel</b> - ����������� ������� �������, ������� ������ �������� ������������ ��� ��������� ������ ��������
							<xmp>\"accessLevel\": 10</xmp>
						</li>
						<li>
							<b>accessLevelOnly</b> - ������ ������� �������, ������� ������ �������� ������������ ��� ��������� ������ ��������
							<xmp>\"accessLevelOnly\": 11</xmp>
						</li>
						<li>
							<b>load</b> - ������ ������� ������������, ������� ���������� ��������� ��� ���������� ��������
							<xmp>\"load\": [\"Favorites\", \"Filters\"]</xmp>
						</li>
						<li>
							<b>children</b> - ������ �������� ���������, ������� ����� �� ���������, ��� � <b>routes</b>
							<xmp>\"children\": [ ... ]</xmp>
						</li>
						<li>
							<b>params</b> - ��������� ��������, ������� ����� �������� ��������������� ������ view. ������ $ �������, ��� ���������� �������� � �������� ���� id ����� url, �������� ���� url = /item/34, �� $1 = items, � $2 = 34
							<xmp>\"params\": {\"a\": \"some text\", \"id\": \"$2\"}</xmp>
						</li>
					</ul>
				</li>
				<li>
					<b>menu</b> - ������ � ����� menu ����� �������, ������� ����� ��������� ��������������� � ������� � ����������� �� ����� ��������. ��� ����� ��� ������� �������� <b>&lt;a&gt;</b> ������� ������� role ������ ����������� �������� <b>&lt;a href=\"...\" role=\"main\"&gt;</b>
					<xmp>\"menu\": \"TopMenu,SideMenu\"</xmp>
				</li>
				<li>
					<b>hash</b> - ����� ��� ������ ������� � �������� ����� ��������, ��������� url'� ���� http://site.com/#catalog, ���������� ������ �������� � TRUE
					<xmp>\"hash\": true</xmp>
				</li>
				<li>
					<b>indexRoute</b> - ����� ����������� ����� �� ��������� �������� ���������. ����� ��� ��������� ������� (���������), ����� � ������������ �� ������� ���� ��� ��������� ����� �� ������� � ��� ��������������. 
					<xmp>\"indexRoute\": \"main\"</xmp>
					<xmp>\"indexRoute\": null</xmp>
				</li>
				<li>
					<b>defaultRoute</b> - ����� ����������� ����� �� ��������� ����� �������������� ��-���������, ���� ������ ������������ ����� ��������. �������� �������� �� �������� ������ 404
					<xmp>\"defaultRoute\": \"main\"</xmp>
					<xmp>\"defaultRoute\": null</xmp>
				</li>
				<li>
					<b>404</b> - ����� ����������� ����� view ��� ��������� ������ 404. ��� �������� ��������� <b>defaultRoute</b> ������ �������� �� ����� ������
					<xmp>\"404\": \"Error404\"</xmp>
					<xmp>\"404\": null</xmp>
				</li>
				<li>
					<b>401</b> - ����� ����������� ����� view ��� ��������� ������ 401 (������������ �� �����������)
					<xmp>\"401\": \"Error401\"</xmp>
					<xmp>\"401\": null</xmp>
				</li>
				<li>
					<b>403</b> - ����� ����������� ����� view ��� ��������� ������ 403 (�� ������� ���� ������� � ������������ �������)
					<xmp>\"403\": \"Error403\"</xmp>
					<xmp>\"403\": null</xmp>
				</li>		
				<li>
					<b>generateTree</b> - ���� ������ �������� ���������� � TRUE � �������� <b>hash</b> != TRUE, �� ��� ���������� � �������������� ������ <b>������� ���������</b>,  ����� ������������� ����� � �������� ���������� ��� ������� �������� �� ������ ���������� �������, ������������ ��������
					<xmp>\"generateTree\": true</xmp>
				</li>
				
			</ul>";

			$items[] = true;
			$items[] = '������������';
			$items[] = false;
			
			$items[] = "<b>login</b> - ���� � api ����������� ������������ ���������� ��������� � ��������� <b>pathToApi</b>
			<xmp>\"login\": \"user/login.php\"</xmp>";
			$items[] = "<b>logout</b> - ���� � api ������������� ������������ ���������� ��������� � ��������� <b>pathToApi</b>
			<xmp>\"logout\": \"user/logout.php\"</xmp>";
			$items[] = "<b>save</b> - ���� � api ���������� ������ ������������ ������������ ���������� ��������� � ��������� <b>pathToApi</b>
			<xmp>\"save\": \"user/save.php\"</xmp>";
			$items[] = "<b>fullAccess</b> - ����������� ������� ������� ������������, ������� ����� ��������� ������. ��������� ��� ������ ������ <b>User.hasFullAccess</b>
			<xmp>\"fullAccess\": 50</xmp>";
			$items[] = "<b>adminAccess</b> - ����������� ������� ������� ������������, ������� ����� ��������� �������. ��������� ��� ������ ������ <b>User.isAdmin</b>
			<xmp>\"adminAccess\": 100</xmp>";


			$items[] = true;
			$items[] = '������';
			$items[] = false;

			$items[] = "<b>pathToDictionary</b> - ���� � api ��������� ������� ������������ ���������� ��������� � ��������� <b>pathToApi</b>
			<xmp>\"pathToDictionary\": \"dictionary/get.php\"</xmp>";
			$items[] = "<b>tooltipApi</b> - ���� � api ��������� ������� ����������� ��������� ������������ ���������� ��������� � ��������� <b>pathToApi</b>
			<xmp>\"tooltipApi\": \"tooltip/get.php\"</xmp>";
			$items[] = "<b>tooltipClass</b> - ����� ��� ������ ����������� ���������. �������� ������������ �� ��������, � ������ <b>Tooltiper</b>
			<xmp>\"tooltipClass\": \"TooltipPopup\"</xmp>";

		break;


		case 'user':
			$items[] = '��� ��������� ����������� �������� ��������� �� <b>������� ����������</b>. ��� ��������� � ������� ����� � api �����������, �� �� ����� ��� �������';
			$items[] = '���� ���� ��������� �����, �� ������ ������������ ������������ ���������� � �������� ������� ���� ������ ����������';
			$items[] = '���� ��� �� ��������� ����������, ������ �� ���������� ��� ����, ����� ���������� �������� ����� ��������������� � ����������';
			$items[] = '���� �� �� ������, ��� ������� ����������� � ����������� ���������� ������ ����������, �������� ��������� �� <b>������� ����������</b>, � ������ �� ��������� <b>router</b>';
			$items[] = '��� ����������� ������������ � ������ � ��� ������� ���������� ����� <b>User</b>';
			$items[] = '��� ������� � ������� ������ ����������� ������� ������: <xmp>var isAuth = User.isAuthorized();</xmp>';
			$items[] = '� ������, ����� ��� ����� ������� ����������, ��������� �� �����-���� ������ ������������, ����������� � ������� ������ ���������� ������ ����:
<xmp>
{if User.isAuthorized()}
	<div class="user-info">
		...
	</div>
{/if}
</xmp>';
		$items[] = true;
		$items[] = '����� User';
		$items[] = false;
		$items[] = '<b>isAuthorized</b> - ���������� TRUE ��� FALSE. ����������� �� ������������ ��� ���<xmp>var isAuth = User.isAuthorized();</xmp>';
		$items[] = '<b>hasFullAccess</b> - ���������� TRUE ��� FALSE. ����� �� ������������ ������ ������ ��� ���<xmp>var hasFullAccess = User.hasFullAccess();</xmp>';
		$items[] = '<b>isAdmin</b> - ���������� TRUE ��� FALSE. �������� �� ������������ ������� ��� ���<xmp>var isAdmin = User.isAdmin();</xmp>';
		$items[] = '<b>hasType</b> - ���������� TRUE ��� FALSE. ����� �� ������������ ��������� ���<xmp>var isDealer = User.hasType(\'dealer\');</xmp>';
		$items[] = '<b>hasAccessLevel</b> - ���������� TRUE ��� FALSE. ����� �� ������������ ��������� ������� �������. ������ �������� ������� � ���, ��� ������������ ������ ����� ������ ����� �������, � �� ������� ��� ������<xmp>var hasAccess = User.hasAccessLevel(40);</xmp><xmp>var hasExactAccess = User.hasAccessLevel(10, true);</xmp>';
		$items[] = '<b>isBlocked</b> - ���������� TRUE ��� FALSE. ������������ ������������ ��� ���<xmp>var isBlocked = User.isBlocked();</xmp>';
		$items[] = '<b>getBlockReason</b> - ���������� ������� ���������� ������������<xmp>var whyBlocked = User.getBlockReason();</xmp>';
		$items[] = '<b>getAttributes</b> - ���������� ��� �������� ������������<xmp>var userData = User.getAttributes();</xmp>';
		$items[] = '<b>getAttribute</b> - ���������� ��������� ������� ������������<xmp>var username = User.getAttribute(\'name\');</xmp>';
		$items[] = '<b>setAttribute</b> - ������������� ��������� ������� ������������, ���� ������ �������� == TRUE � � ������� ������������ (�������� ��������� �� <b>������� ����������</b>) ����� �������� <b>save</b> ������� ����� �������� � ��<xmp>User.setAttribute(\'type\', \'usual\', true);</xmp>';
		$items[] = '<b>setAttributes</b> - �� �� �����, ������ ������ ���������� ���������� ������ � ������ ����������, � ������ ����� ��������� ��� ��� � ��
		<xmp>User.setAttributes({\'type\': \'usual\', ... });</xmp>';

		$items[] = '<b>getSettings</b> - ���������� ��� ������������ ��������� ������������<xmp>var userSettings = User.getSettings();</xmp>';
		$items[] = '<b>getSetting</b> - ���������� ��������� ������������ ��������� ������������<xmp>var myFontSize = User.getSetting(\'fontSize\');</xmp>';
		$items[] = '<b>setSetting</b> - ��������� ��������� ��������� �������� � �� ������� (���� ������ ���������� �������� � ������� <b>save</b>)<xmp>User.setSetting(\'fontSize\', 18);</xmp>';

		$items[] = true;
		$items[] = 'Login API';
		$items[] = false;
		$items[] = '��� ��������� ���� � ������ api �������� ��������� �� <b>������� ����������</b>';
		$items[] = "������ api ������ ���������� JSON ����:";
		$items[] = "<b>status</b> - ����� ����������� ������ � ���, ������ ������� �������� ������������ � ��� ������� �������
		<xmp>\"status\": \"{ ... }\"</xmp>
			<ul>
				<li>
					<b>type</b> �������������� ����, ��� ������������, �������� ��� ������� ��� ��� �����
					<xmp>\"type\": \"minimal\"</xmp>
				</li>
				<li>
					<b>accessLevel</b> �������������� ����, ������� ������� ������������ ���������� ����� ������
					<xmp>\"accessLevel\": 10</xmp>
				</li>
				<li>
					<b>isBlocked</b> �������������� ����, ����� � ���, ��� ������������ ������������
					<xmp>\"isBlocked\": true</xmp>
				</li>
				<li>
					<b>blockReason</b> �������������� ����, �������, �� ������� ������������ ������������
					<xmp>\"blockReason\": \"You were banned because of breaking the rules of the site\"</xmp>
				</li>
			</ul>";
		$items[] = "<b>attributes</b> - ����� ���������� ������ ������������: ���, �������, email � �.�. ������ ������ ��������� ���������
		<xmp>\"attributes\": \"{ ... }\"</xmp>";

		$items[] = "<b>settings</b> - ����� ���������� ������������ ��������� ������������ ����������� � ������ �������, �������� ���� ���� ����� ��� ������ ������.
		<xmp>\"settings\": \"{ ... }\"</xmp>";

		


		break;

		case 'shortcut':
			$items[] = true;
			$items[] = '�������';
			$items[] = false;
			$items[] = "<b>super(ParentClass)</b> - ����� ������������ ������ �����-������ � ��������� ����������� ��� ���, ��� ������ �������� ��� ������������� ������ ��� �������
			<xmp>function doSome() {\n\tsuper(ParentClass, arg1, arg2);\n}</xmp>������������� �:
			<xmp>ChildClass.prototype.doSome = function() {\n\tParentClass.prototype.doSome.call(this, arg1, arg2);\n}</xmp>";

			$items[] = "<b>super(ParentClass.doSomeOther)</b> - ����� ������� ������ �����-������ � ��������� ����������� ��� ���, ��� ������ �������� ��� ������������� ������ ��� �������
			<xmp>function doSome() {\n\tsuper(ParentClass.doSomeOther, arg1, arg2);\n}</xmp>������������� �:
			<xmp>ChildClass.prototype.doSome = function() {\n\tParentClass.prototype.doSomeOther.call(this, arg1, arg2);\n}</xmp>";

			$items[] = "<b>function a(b:Corrector) {</b> - ���������� ���������� � ��������� �������, � ������� ������ ���������� <b>DigitFilter</b>
			<xmp>function setParams(data:DigitFilter) {\n\t...\n}</xmp>������������� �:
			<xmp>Component.prototype.setParams = function(data) {\n\tdata = Corrector.correct('DigitFilter', data);\n\t...\n}</xmp>��������� � ����������� �������� ��������� �� ������� �����������";

			$items[] = true;
			$items[] = '���������� ����������';
			$items[] = false;

			$items[] = '<b>$value = countedValue</b> - ������������� �������� ���������� ����������
			<xmp>this.set(\'value\', countedValue);</xmp>';

			$items[] = '<b>var countedValue = $value</b> - ����������� ���������� �������� ���������� ����������
			<xmp>var countedValue = this.get(\'value\');</xmp>';

			$items[] = '<b>$value=></b> - ������������� �������� ���������� ���������� �� ����������� ����������
			<xmp>this.set(\'value\', value);</xmp>';

			$items[] = '<b>$isActive!</b> - ������ �������� (boolean) ���������� ���������� �� ���������������
			<xmp>this.toggle(\'isActive\');</xmp>';

			$items[] = '<b>get options, value</b> - ������ ������������ � ������ �������. ������������� �������� �������� ���������� ���������� � ���������� �� � ����������� ����������.
			<xmp>var options = this.get(\'options\');</xmp><xmp>var value = this.get(\'value\');</xmp>';

			$items[] = '<b>var *$items = []</b> - ��������, ��� ������ ���������� ������ ���������� �������� ����������� ���������� ���������� � ����� �������
			<xmp>var *$items = [];</xmp><xmp>items.push(1);</xmp><xmp>items.push(2);</xmp>�������� ��� ����� ��������� ���:
			<xmp>var items = [];</xmp><xmp>items.push(1);</xmp><xmp>items.push(2);</xmp><xmp>this.set(\'items\', items);</xmp>';

			$items[] = '<b>$value++</b> - �������� �������� ���������� ���������� �� 1
			<xmp>this.plusTo(\'value\', 1);</xmp>';

			$items[] = '<b>$value--</b> - �������� �������� ���������� ���������� �� -1
			<xmp>this.plusTo(\'value\', -1);</xmp>';

			$items[] = '<b>$value *= 5</b> - �������� �������� ���������� ���������� �� 5
			<xmp>this.plusTo(\'value\', 5, \'*\');</xmp>';

			$items[] = '<b>$value /= 10</b> - ����� �������� ���������� ���������� �� 10
			<xmp>this.plusTo(\'value\', 10, \'/\');</xmp>';

			$items[] = '<b>$value %= 2</b> - ����������� ���������� ���������� �������� ������ ������� �� ������� ��� �� 2
			<xmp>this.plusTo(\'value\', 2, \'%\');</xmp>';

			$items[] = '<b>$items.add([\'apple\', \'banana\'])</b> - ��������� ������� ��� ��������� ��������� � ����� ���������� ���������� � �������� ���������� ����������
			<xmp>this.addTo(\'items\', [\'apple\', \'banana\']);</xmp>';

			$items[] = '<b>$items.add(\'apple\', 0)</b> - ��������� ������� ��� ��������� ��������� � ���������� ���������� � �������� ������� � �������� ���������� ����������
			<xmp>this.addTo(\'items\', \'apple\', 0);</xmp>';

			$items[] = '<b>$items.addOne([\'apple\'])</b> - ��������� ������� � ����� ���������� ���������� � �������� ���������� ����������. ���� ���� ���������� �������� �������� �������� ����� �������� ������ ���� ������� - ��� ������
			<xmp>this.addOneTo(\'items\', [\'apple\']);</xmp>';

			$items[] = '<b>$items.addOne(\'apple\', 0)</b> - ��������� ������� � ���������� ���������� � �������� ������� � �������� ���������� ����������. ���� ���� ���������� �������� �������� �������� ����� �������� ������ ���� ������� - ��� ������
			<xmp>this.addOneTo(\'items\', \'apple\', 0);</xmp>';

			$items[] = '<b>$items.removeAt(2)</b> - ������� ������� � �������� 2 �� ���������� ���������� � �������� ���������� ����������
			<xmp>this.removeByIndexFrom(\'items\', 2);</xmp>';

			$items[] = '<b>$items.remove(\'apple\')</b> - ������� ������� � �������� ��������� �� ���������� ���������� � �������� ���������� ����������
			<xmp>this.removeValueFrom(\'items\', \'apple\');</xmp>';

			$items[] = '<b>$items.each(function() {})</b> - �������� ������� ���������� ��� ������� �������� ���������� ����������
			<xmp>this.each(\'items\', function() {});</xmp>';

			$items[] = true;
			$items[] = '������� DOM';
			$items[] = false;

			$items[] = '<b>&lt;&gt;</b> - ���������� ������� ���������� ���������� ��� scope ��� �������, � ������� ��������� ���������
			<xmp>var scope = <>;</xmp><xmp>var scope = this.getElement();</xmp>
			<xmp>var r = <>.getRect();</xmp><xmp>var r = this.getElement().getRect();</xmp>';

			$items[] = '<b>&lt;input&gt;</b> - ���������� ������ ������� � �������� ���������� � ����� input
			<xmp>var input = <input>;</xmp><xmp>var input = this.findElement(\'input\');</xmp>
			<xmp>var v = <input>.value;</xmp><xmp>var v = this.findElement(\'input\').value;</xmp>
			<xmp><input>.clear();</xmp><xmp>this.findElement(\'input\').clear();</xmp>';

			$items[] = '<b>&lt;#container&gt;</b> - ���������� ������ ������� � �������� ����������, ������� id = container
			<xmp>var cnt = <#container>;</xmp><xmp>var cnt = this.findElement(\'#container\');</xmp>
			<xmp>var h = <#container>.innerHTML;</xmp><xmp>var h = this.findElement(\'#container\').innerHTML;</xmp>
			<xmp><#container>.hide();</xmp><xmp>this.findElement(\'#container\').hide();</xmp>';

			$items[] = '<b>&lt;.item&gt;</b> - ���������� ������ ������� � �������� ����������, ������� class = item
			<xmp>var i = <.item>;</xmp><xmp>var i = this.findElement(\'.item\');</xmp>
			<xmp>var h = <.item>.innerHTML;</xmp><xmp>var h = this.findElement(\'.item\').innerHTML;</xmp>
			<xmp><.item>.show();</xmp><xmp>this.findElement(\'.item\').show();</xmp>';

			$items[] = "<b>&lt;.@name&gt;</b> - ���������� ������ ������� � �������� ����������, ������� ����������� class = @name<br>
			����������� ��� ������ ������� �� ����� ������ ���������� � ��������������� ����� (� ������ ������ <b>name</b>)<br>
			���������� ���� ������� ��������� ����������� �� ������ ����� �� �������� ������ ���� �������<br><br>
			�������� � ������������ ������� ������ � ������� ���������� <b>CatalogItem</b>:
			<xmp><div class=\"@\">\n   <div class=\"@name\">Item name</div>\n   <div class=\"@price\">2 000</div>\n</div></xmp>
			����� �������������� ��� ������� ����� ��������� ���:
			<xmp><div class=\"catalog-item\">\n   <div class=\"catalog-item_name\">Item name</div>\n   <div class=\"catalog-item_price\">2 000</div>\n</div></xmp>
			�������������� ���������� � JS ���� �������� ���:
			<xmp><.@></xmp>
			<xmp><.@name></xmp>
			<xmp><.@price></xmp>";

			$items[] = '<b>&lt;.block[]&gt;</b> - ���������� ��� �������� � �������� ����������, ������� class = block
			<xmp>var blocks = <.block[]>;</xmp><xmp>var blocks = this.findElements(\'.block\');</xmp>
			<xmp>var firstBlock = <.block[]>[0];</xmp><xmp>var firstBlock = this.findElements(\'.block\')[0];</xmp>';

			$items[] = '<b>&lt;.item[2]&gt;</b> - ���������� ������� � �������� �������� � �������� ����������, ������� class = item
			<xmp>var i = <.item[2]>;</xmp><xmp>var i = this.findElements(\'.item\')[2];</xmp>
			<xmp>var h = <.item[2]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[2].innerHTML;</xmp>
			<xmp><.item[2]>.show();</xmp><xmp>this.findElements(\'.item\')[2].show();</xmp>';

			$items[] = '<b>&lt;.item[idx]&gt;</b> - ���������� ������� � �������� �� ���������� � �������� ����������, ������� class = item
			<xmp>var i = <.item[idx]>;</xmp><xmp>var i = this.findElements(\'.item\')[idx];</xmp>
			<xmp>var h = <.item[idx]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[idx].innerHTML;</xmp>
			<xmp><.item[idx]>.show();</xmp><xmp>this.findElements(\'.item\')[idx].show();</xmp>';

			$items[] = '<b>&lt;:list&gt;</b> - ���������� ��� �������������� ������� � �������� ����������, ������� ������� as = list
			<xmp><ul class="items-list" as="list">'."\n\t<li>list item</li>\n".'</ul></xmp>
			<xmp>var l = <:list>;</xmp><xmp>var l = this.getElement(\'list\');</xmp>
			<xmp>var h = <:list>.innerHTML;</xmp><xmp>var h = this.getElement(\'list\').innerHTML;</xmp>
			<xmp><:list>.show();</xmp><xmp>this.getElement(\'list\').show();</xmp>';

			$items[] = '<b>&lt;::userInfo&gt;</b> - ���������� ��� �������������� �������� ���������, ������� ������� as = userInfo
			<xmp><component class="UserInfo" as="userInfo"></xmp>
			<xmp>var ui = <::userInfo>;</xmp><xmp>var ui = this.getChild(\'userInfo\');</xmp>
			<xmp>var uie = <::userInfo>.getElement();</xmp><xmp>var uie = this.getChild(\'userInfo\').getElement();</xmp>';

			$items[] = '<b>&lt;::userInfo&gt;&lt;&gt;</b> - ���������� ������� ������� ��������� ����������, �������� ������� as = userInfo
			<xmp><component class="UserInfo" as="userInfo"></xmp>
			<xmp>var uie = <::userInfo><>;</xmp><xmp>var uie = this.getChild(\'userInfo\').getElement();</xmp>';


			$items[] = true;
			$items[] = '�������';
			$items[] = false;

			$items[] = '<b>--> change</b> - ���������� ������� (� �������� ����������) change 
			<xmp>--> change;</xmp><xmp>this.dispatchEvent(\'change\');</xmp>
			<xmp>-->change(param, ...);</xmp><xmp>this.dispatchEvent(\'change\', param, ...);</xmp>';
			
			$items[] = '<b>==> everythingReady</b> - ���������� ��������� (� �������� �������� ��������) ������� everythingReady 
			<xmp>==> everythingReady;</xmp><xmp>LocalState.dispatchEvent(\'everythingReady\');</xmp>
			<xmp>==> everythingReady (param, ...);</xmp><xmp>LocalState.dispatchEvent(\'everythingReady\', param, ...);</xmp>';

			$items[] = '<b>===> everythingReady</b> - ���������� ���������� (� �������� ����� ����������) ������� everythingReady
			<xmp>===> everythingReady;</xmp><xmp>GlobalState.dispatchEvent(\'everythingReady\');</xmp>
			<xmp>===> everythingReady (param, ...);</xmp><xmp>GlobalState.dispatchEvent(\'everythingReady\', param, ...);</xmp>';

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
			$items[] = '<b>scope</b> - ������� � ���, ��� ������� �������� ������� �����������, � ����� �� ���������� ����� ������������� � ��� ��������';
			$items[] = '<b>test</b> - �������� ������ ������� ����� �������, ������� ����� ���������� ��� ������������ ����������. ������������ � ��������-������<xmp><button test="click,mouseover,mouseout"></xmp>';
			$items[] = true;
			$items[] = '�������� ��������';
			$items[] = false;
			$items[] = '<b>tmpid</b> - �������, ��� ������ ����� ������� �� ��� id, �������� �����������<xmp><template tmpid="{&tmpid}"></xmp>';

			$items[] = true;
			$items[] = '�������� ����������';
			$items[] = false;
			$items[] = '<b>�mpid</b> - ������ ���������� ��� id, ����� ��� ����� ���� �����<xmp><component as="mainMenu"></xmp><xmp>this.getChild(\'mainMenu\');</xmp>';
			$items[] = '<b>class</b> - ������������ �������, ������� ���������� ����������, ���������� � ������ ������ ����������� ���������<xmp><component class="Tooltip" as="tooltip"></xmp>';
			$items[] = '<b>name</b> - ������������ ������� ��� ����������� � ����� control, ����� ��� ������ ���������, ������ ������ ���� ��� ������ ��������� ���������<xmp><control class="Select" name="color"></xmp><xmp>this.getControl(\'color\');</xmp>';

		break;

		case 'events':
			$items[] = "������� ��������� ��� � ��������� DOM ��� � � �����������";
			$items[] = "����� �������� ������� � �������� �������� ��������������� ������� � ��� ���<xmp><div onClick=\"handleClick\"></xmp>";
			$items[] = "����� �������� ������� � ���������� ����� �������� ��������������� ������� � ��� ���<xmp><Input onChange=\"handleInputChange\"></xmp>";
			$items[] = "��������, ��� ��� ����������� ������� ����������� ������������ ������� ������, � ������ ��� ������ ������";
			$items[] = "����� ������� ����, ��������������� �������� <b>^on[A-Z]\w+$</b>, �������������� ������������ ��� ���������� �������";
			$items[] = "�� ���������� � ��������� ����������� ������, �� ���������� ������������� ����� �������� <b>bind</b>";
			$items[] = "���� ������� ��� ��������� ��������� ������ ������� ��������� ����������, �� ���������� ����� ����� �������� ����� ����������, � ������ ������ ������ <b>Container</b><xmp><Container>\n\t<Textarea onChange=\"onChangeTextarea\">\n</Container></xmp>";
			$items[] = "���� � ������ ������ ����� ������ ����� <b>onChangeTextarea</b> ������ <b>Container</b>";
			$items[] = "����� ������� ����� ������, � ������� �������� ��� ��� �����������, �������� � ����� ����������� �������� ����� <b>this</b><xmp><Container>\n\t<Textarea onChange=\"this.onChangeTextarea\">\n</Container></xmp>";
			$items[] = "�������� ����� <b>this</b> ��������� � ����������� ������� ��������";
			$items[] = "��� ����, ����� ���������� ������� �����, �� ������� ��� ���� ��������� �����, ����������� ��������������� ���� � ��� �������, ������� ����� ������������ ������� �����������<xmp><Select onChange=\"!change\"></xmp>";
			$items[] = "� ���� ������ ����������� ���������� �� ����� ��������, ������� ����� ������������ � �������� ����������";
			$items[] = "����� ������ ������� � ������� <b>stopPropagation</b> � �� ��������� ��� ���� ��������� �����, ����������� �������� ����� <b>stop</b><xmp><div onClick=\":stop\"></xmp>";
			$items[] = "��� ������ <b>preventDefault</b>, ����������� �������� ����� <b>prevent</b><xmp><div onClick=\":prevent\"></xmp>";
		break;

		case 'jsobfus':
			$items[] = "JS ���������� ���������� ��� �������������� �������� ����������, �����, �������";
			$items[] = "������������ ������, ���������� ������� ����� ������������";
			$items[] = "����������� PHP �����, ��������� �����, ����������� �������� � ��������� ������ �� �������";
			$items[] = "��� ����, ����� �������� ���� �������������, ����������� ����������� ������ <b><<-</b><xmp>var name = data.userLastName <<-</xmp><xmp>var key = data['key<<-'] <<-</xmp><xmp>var n = data <<- .numbers <<- .first <<-</xmp>";
			$items[] = "����� ���������� ������ ����� ��������� �� ������� �����:<xmp>var name = data.r</xmp><xmp>var key = data['df']</xmp><xmp>var n = a.t.j</xmp>";
			$items[] = "����� ���������, ������� ������ ���� �������������, � �������� ����������� ������� ��������";
			$items[] = "����� ������� ����� ����������� �������, ��� � ������� ������� ����";
			$items[] = "������ ���������� ��������� ������������� ��������� ����������. ��� ��������� ������� � ������������ �� ������� �������, ����������� ����� ���������� ����� ����������������� �����";
			$items[] = "������ �����������, �������, ����� ������� ���� ��������� �����, �������� � ���� ��������<xmp>\$filters <<- = data['filters <<-'];</xmp>� ������� ��� ����� ��������� �������� ���:<xmp><div class=\"filter-items\">\n\t{foreach \$filters <<- as &filter}\n\t\t<div class=\"filter-item\">\n\t\t\t{&filter.name <<-}\n\t\t</div>\n\t{/foreach}\n</div></xmp>";

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