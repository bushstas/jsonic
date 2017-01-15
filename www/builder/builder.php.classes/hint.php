<?php

	$topic = $_GET['topic'];

	$items = array();
	switch ($topic) {
		case 'config':
			$items[] = true;
			$items[] = 'Приложение';
			$items[] = false;

			$items[] = "<b>indexPage</b> - Название индексного файла страниц приложения. Компилятор автоматически генерирует индексные файлы
			<xmp>\"indexPage\": \"index.html\"</xmp>";
			$items[] = "<b>title</b> - Заголовок страницы. Компилятор автоматически вставляет это значение в HTML код страницы
			<xmp>\"title\": \"My JavaScript application\"</xmp>";
			$items[] = "<b>charset</b> - Кодировка страницы. Компилятор автоматически вставляет это значение в HTML код страницы
			<xmp>\"charset\": \"UTF-8\"</xmp>";
			$items[] = "<b>jsFolder</b> - Директория относительно корня сайта, где будет храниться скомпелированный JS файл.
			<xmp>\"jsFolder\": \"js\"</xmp>";
			$items[] = "<b>cssFolder</b> - Директория относительно корня сайта, где будет храниться скомпелированный CSS файл.
			<xmp>\"cssFolder\": \"css\"</xmp>";
			$items[] = "<b>imagesFolder</b> - Директория относительно корня сайта, где будут храниться фоновые изображения. Неоходимо для css переменных <b>imgsrc</b>. Для более подробной информации, смотрите подсказку по <b>css файлам</b>
			<xmp>\"imagesFolder\": \"images\"</xmp>";
			$items[] = "<b>compiledJs</b> - Название скомпелированного JS файла.
			<xmp>\"compiledJs\": \"base\"</xmp>";
			$items[] = "<b>compiledCss</b> - Название скомпелированного CSS файла.
			<xmp>\"compiledCss\": \"styles\"</xmp>";
			$items[] = "<b>pathToApi</b> - Путь к директории, где располагаются серверные api приложения, относительно корня сайта
			<xmp>\"pathToApi\": \"api\"</xmp>";
			$items[] = "<b>container</b> - Класс DOM элемента, куда будут отрендерены классы с типом view. Сам элемент должен присутстовать в шаблоне класса указанного в параметре <b>entry</b>. При его отсутствии, будет сгенерирован пустой элемент в самом низу DOM
			<xmp>\"container\": \"app-view-container\"</xmp>";


			$items[] = true;
			$items[] = 'Исходные коды';
			$items[] = false;

			$items[] = "<b>entry</b> - Название класса точки входа с типом application. Располагайте в одноименной директории
			<xmp>\"entry\": \"App\"</xmp>";
			$items[] = "<b>scope</b> - Путь к директории, в пределах которой будет производиться поиск файлов источников, относительно директории builder
			<xmp>\"scope\": \"./sources\"</xmp>";
			$items[] = "<b>core</b> - Путь к директории, где располагается ядро приложения, относительно директории builder
			<xmp>\"core\": \"./core\"</xmp>";
			$items[] = "<b>tests</b> - Путь к директории, где располагаются описания тестов приложения, относительно директории builder
			<xmp>\"tests\": \"./tests\"</xmp>";
			$items[] = "<b>blanks</b> - Путь к директории, где располагаются заготовки для гененрации HTML и JS кода, относительно директории builder. Там вы можете настроить, как будет выглядеть ваш индексный файл
			<xmp>\"blanks\": \"./blanks\"</xmp>";
			$items[] = "<b>scripts</b> - Путь к директории, где располагаются сторонние JS скрипты, которые нужно включить в приложение, относительно директории builder
			<xmp>\"scripts\": \"./scripts\"</xmp>";
			$items[] = "<b>views</b> - Название директории, в которой будут сгенерированы классы с типом view. Генерация производится для быстрого создания нужной инфраструктуры с нуля, активируется специальным чекбоксом справа. Для генерации используются данные указанные в поле <b>routes</b> параметра <b>router</b>. Директория будет также сгенерирована, если ее нет, в папке определенной в параметре <b>scope</b>. Сгенерированы будут только те классы, которых еще нет, существующие не будут затронуты. Для настройки заготовки классов с типом view, смотрите параметр <b>blanks</b>
			<xmp>\"views\": \"views\"</xmp>";
			
			$items[] = true;
			$items[] = 'Роутер';
			$items[] = false;

			$items[] = "<b>router</b> - Описание классов с типом view, символизирующих страницы приложения, и путей к ним
			<xmp>\"router\": \"{ ... }\"</xmp>
			<ul>
				<li>
					<b>routes</b> - Массив, содержащий список классов с типом view. Не включает в себя классы для обработки ошибок (404, 401, 403, ...)
					<xmp>\"routes\": \"[ {...}, {...}, ... ]\"</xmp>
					<ul>
						<li>
							<b>name</b> - Обозначение маршрута / Название директории, обязательно с маленькой буквы
							<xmp>\"name\": \"main\"</xmp>
						</li>
						<li>
							<b>view</b> - Название view класса, обязательно с большой буквы
							<xmp>\"view\": \"Main\"</xmp>
						</li>
						<li>
							<b>title</b> - Заголовок страницы
							<xmp>\"title\": \"Main page\"</xmp>
						</li>
						<li>
							<b>accessLevel</b> - Минимальный уровень доступа, которым должен обладать пользователь для просмотра данной страницы
							<xmp>\"accessLevel\": 10</xmp>
						</li>
						<li>
							<b>accessLevelOnly</b> - Точный уровень доступа, которым должен обладать пользователь для просмотра данной страницы
							<xmp>\"accessLevelOnly\": 11</xmp>
						</li>
						<li>
							<b>load</b> - Список классов контроллеров, которые необходимо загрузить при рендеринге страницы
							<xmp>\"load\": [\"Favorites\", \"Filters\"]</xmp>
						</li>
						<li>
							<b>children</b> - Список дочерних маршрутов, имеющий такую же структуру, как и <b>routes</b>
							<xmp>\"children\": [ ... ]</xmp>
						</li>
						<li>
							<b>params</b> - Параметры страницы, которые будут переданы непосредственно классу view. Символ $ говорит, что необходимо передать в качестве поля id часть url, например если url = /item/34, то $1 = items, а $2 = 34
							<xmp>\"params\": {\"a\": \"some text\", \"id\": \"$2\"}</xmp>
						</li>
					</ul>
				</li>
				<li>
					<b>menu</b> - Классы с типом menu через запятую, которые будут привязаны непосредственно к роутеру и реагировать на смену страницы. Для этого для каждого элемента <b>&lt;a&gt;</b> задайте атрибут role равный обозначению маршрута <b>&lt;a href=\"...\" role=\"main\"&gt;</b>
					<xmp>\"menu\": \"TopMenu,SideMenu\"</xmp>
				</li>
				<li>
					<b>hash</b> - Чтобы ваш роутер работал в пределах одной страницы, используя url'ы вида http://site.com/#catalog, установите данный параметр в TRUE
					<xmp>\"hash\": true</xmp>
				</li>
				<li>
					<b>indexRoute</b> - Здесь указывается какой из маршрутов является индексным. Нужен для обработки случаев (редиректа), когда у пользователя не хватает прав для просмотра одной из страниц и при разлогинивании. 
					<xmp>\"indexRoute\": \"main\"</xmp>
					<xmp>\"indexRoute\": null</xmp>
				</li>
				<li>
					<b>defaultRoute</b> - Здесь указывается какой из маршрутов будет использоваться по-умолчанию, если введен неправильный адрес страницы. Отменяет редирект на страницу ошибки 404
					<xmp>\"defaultRoute\": \"main\"</xmp>
					<xmp>\"defaultRoute\": null</xmp>
				</li>
				<li>
					<b>404</b> - Здесь указывается класс view для обработки ошибки 404. При заданном параметре <b>defaultRoute</b> данный параметр не имеет смысла
					<xmp>\"404\": \"Error404\"</xmp>
					<xmp>\"404\": null</xmp>
				</li>
				<li>
					<b>401</b> - Здесь указывается класс view для обработки ошибки 401 (пользователь не авторизован)
					<xmp>\"401\": \"Error401\"</xmp>
					<xmp>\"401\": null</xmp>
				</li>
				<li>
					<b>403</b> - Здесь указывается класс view для обработки ошибки 403 (не хватает прав доступа к запрашиваему ресурсу)
					<xmp>\"403\": \"Error403\"</xmp>
					<xmp>\"403\": null</xmp>
				</li>		
				<li>
					<b>generateTree</b> - Если данный параметр установлен в TRUE и параметр <b>hash</b> != TRUE, то при компиляции с активированной опцией <b>Создать окружение</b>,  будут сгенерированы папки в корневой директории для каждого маршрута со своими индексными файлами, дублирующими основной
					<xmp>\"generateTree\": true</xmp>
				</li>
				
			</ul>";

			$items[] = true;
			$items[] = 'Пользователь';
			$items[] = false;
			
			$items[] = "<b>login</b> - путь к api авторизации относительно директории указанной в параметре <b>pathToApi</b>
			<xmp>\"login\": \"user/login.php\"</xmp>";
			$items[] = "<b>logout</b> - путь к api деавторизации относительно директории указанной в параметре <b>pathToApi</b>
			<xmp>\"logout\": \"user/logout.php\"</xmp>";
			$items[] = "<b>save</b> - путь к api сохранения данных пользователя относительно директории указанной в параметре <b>pathToApi</b>
			<xmp>\"save\": \"user/save.php\"</xmp>";
			$items[] = "<b>fullAccess</b> - минимальный уровень доступа пользователя, который может считаться полным. Необходим для работы метода <b>User.hasFullAccess</b>
			<xmp>\"fullAccess\": 50</xmp>";
			$items[] = "<b>adminAccess</b> - минимальный уровень доступа пользователя, который может считаться админом. Необходим для работы метода <b>User.isAdmin</b>
			<xmp>\"adminAccess\": 100</xmp>";


			$items[] = true;
			$items[] = 'Прочее';
			$items[] = false;

			$items[] = "<b>pathToDictionary</b> - путь к api получения словаря относительно директории указанной в параметре <b>pathToApi</b>
			<xmp>\"pathToDictionary\": \"dictionary/get.php\"</xmp>";
			$items[] = "<b>tooltipApi</b> - путь к api получения текстов всплывающих подсказок относительно директории указанной в параметре <b>pathToApi</b>
			<xmp>\"tooltipApi\": \"tooltip/get.php\"</xmp>";
			$items[] = "<b>tooltipClass</b> - Класс для показа всплывающих подсказок. Смотрите документацию по хелперам, а именно <b>Tooltiper</b>
			<xmp>\"tooltipClass\": \"TooltipPopup\"</xmp>";

		break;


		case 'user':
			$items[] = 'Для настройки авторизации смотрите подсказки по <b>конфигу приложения</b>. Без указанных в конфиге путей к api авторизации, ее не будет как таковой';
			$items[] = 'Если пути прописаны верно, то данные пользователя автоматичеки загрузятся в качестве первого шага работы приложения';
			$items[] = 'Если вам не требуется аторизация, просто не указывайте эти пути, тогда приложение перейдет сразу непосредственно к рендерингу';
			$items[] = 'Если вы не знаете, как увязать авторизацию с конкретными страницами вашего приложения, смотрите подскажку по <b>конфигу приложения</b>, а именно по параметру <b>router</b>';
			$items[] = 'Для авторизации пользователя и работы с его данными существует класс <b>User</b>';
			$items[] = 'Для доступа к данному классу используйте простую запись: <xmp>var isAuth = User.isAuthorized();</xmp>';
			$items[] = 'В случае, когда вам нужно вывести информацию, зависящую от каких-либо данных пользователя, используйте в шаблоне вашего компонента запись вида:
<xmp>
{if User.isAuthorized()}
	<div class="user-info">
		...
	</div>
{/if}
</xmp>';
		$items[] = true;
		$items[] = 'Класс User';
		$items[] = false;
		$items[] = '<b>isAuthorized</b> - Возвращает TRUE или FALSE. Авторизован ли пользователь или нет<xmp>var isAuth = User.isAuthorized();</xmp>';
		$items[] = '<b>hasFullAccess</b> - Возвращает TRUE или FALSE. Имеет ли пользователь полный доступ или нет<xmp>var hasFullAccess = User.hasFullAccess();</xmp>';
		$items[] = '<b>isAdmin</b> - Возвращает TRUE или FALSE. Является ли пользователь админом или нет<xmp>var isAdmin = User.isAdmin();</xmp>';
		$items[] = '<b>hasType</b> - Возвращает TRUE или FALSE. Имеет ли пользователь указанный тип<xmp>var isDealer = User.hasType(\'dealer\');</xmp>';
		$items[] = '<b>hasAccessLevel</b> - Возвращает TRUE или FALSE. Имеет ли пользователь указанный уровень доступа. Второй аргумент говорит о том, что пользователь должен иметь именно такой уровень, а не больший или равный<xmp>var hasAccess = User.hasAccessLevel(40);</xmp><xmp>var hasExactAccess = User.hasAccessLevel(10, true);</xmp>';
		$items[] = '<b>isBlocked</b> - Возвращает TRUE или FALSE. Заблокирован пользователь или нет<xmp>var isBlocked = User.isBlocked();</xmp>';
		$items[] = '<b>getBlockReason</b> - Возвращает причину блокировки пользователя<xmp>var whyBlocked = User.getBlockReason();</xmp>';
		$items[] = '<b>getAttributes</b> - Возвращает все атрибуты пользователя<xmp>var userData = User.getAttributes();</xmp>';
		$items[] = '<b>getAttribute</b> - Возвращает указанный атрибут пользователя<xmp>var username = User.getAttribute(\'name\');</xmp>';
		$items[] = '<b>setAttribute</b> - Устанавливает указанный атрибут пользователя, если третий аргумент == TRUE и в конфиге пользователя (смотрите подсказку по <b>конфигу приложения</b>) задан параметр <b>save</b> атрибут будет сохранен в БД<xmp>User.setAttribute(\'type\', \'usual\', true);</xmp>';
		$items[] = '<b>setAttributes</b> - То же самое, только первым аргументом передается объект с полями атрибутами, а второй метка сохранять или нет в БД
		<xmp>User.setAttributes({\'type\': \'usual\', ... });</xmp>';

		$items[] = '<b>getSettings</b> - Возвращает все персональные настройки пользователя<xmp>var userSettings = User.getSettings();</xmp>';
		$items[] = '<b>getSetting</b> - Возвращает указанную персональную настройку пользователя<xmp>var myFontSize = User.getSetting(\'fontSize\');</xmp>';
		$items[] = '<b>setSetting</b> - Сохраняет указанную настройку локально и на сервере (если указан корректный параметр в конфиге <b>save</b>)<xmp>User.setSetting(\'fontSize\', 18);</xmp>';

		$items[] = true;
		$items[] = 'Login API';
		$items[] = false;
		$items[] = 'Для настройки пути к данной api смотрите подсказку по <b>конфигу приложения</b>';
		$items[] = "Данная api должна возвращать JSON вида:";
		$items[] = "<b>status</b> - Здесь содержаться данные о том, какими правами обладает пользователь и его текущем статусе
		<xmp>\"status\": \"{ ... }\"</xmp>
			<ul>
				<li>
					<b>type</b> Необязательное поле, тип пользователя, например тип клиента или его тариф
					<xmp>\"type\": \"minimal\"</xmp>
				</li>
				<li>
					<b>accessLevel</b> Необязательное поле, уровень доступа пользователя выраженный целым числом
					<xmp>\"accessLevel\": 10</xmp>
				</li>
				<li>
					<b>isBlocked</b> Необязательное поле, метка о том, что пользователь заблокирован
					<xmp>\"isBlocked\": true</xmp>
				</li>
				<li>
					<b>blockReason</b> Необязательное поле, причина, по которой пользователь заблокирован
					<xmp>\"blockReason\": \"You were banned because of breaking the rules of the site\"</xmp>
				</li>
			</ul>";
		$items[] = "<b>attributes</b> - Здесь содержатся данные пользователя: имя, телефон, email и т.д. Данный список атрибутов свободный
		<xmp>\"attributes\": \"{ ... }\"</xmp>";

		$items[] = "<b>settings</b> - Здесь содержатся персональные настройки пользователя относящиеся к работе клиента, например цвет фона сайта или размер шрифта.
		<xmp>\"settings\": \"{ ... }\"</xmp>";

		


		break;

		case 'shortcut':
			$items[] = true;
			$items[] = 'Функции';
			$items[] = false;
			$items[] = "<b>super(ParentClass)</b> - Вызов одноименного метода супер-класса с заданными параметрами или без, где первый аргумент имя родительского класса без кавычек
			<xmp>function doSome() {\n\tsuper(ParentClass, arg1, arg2);\n}</xmp>преобразуется в:
			<xmp>ChildClass.prototype.doSome = function() {\n\tParentClass.prototype.doSome.call(this, arg1, arg2);\n}</xmp>";

			$items[] = "<b>super(ParentClass.doSomeOther)</b> - Вызов другого метода супер-класса с заданными параметрами или без, где первый аргумент имя родительского класса без кавычек
			<xmp>function doSome() {\n\tsuper(ParentClass.doSomeOther, arg1, arg2);\n}</xmp>преобразуется в:
			<xmp>ChildClass.prototype.doSome = function() {\n\tParentClass.prototype.doSomeOther.call(this, arg1, arg2);\n}</xmp>";

			$items[] = "<b>function a(b:Corrector) {</b> - Применение корректора к аргументу функции, в данному случае корректора <b>DigitFilter</b>
			<xmp>function setParams(data:DigitFilter) {\n\t...\n}</xmp>преобразуется в:
			<xmp>Component.prototype.setParams = function(data) {\n\tdata = Corrector.correct('DigitFilter', data);\n\t...\n}</xmp>Подробнее о корректорах смотрите подсказку по классам корректорам";

			$items[] = true;
			$items[] = 'Реактивные переменные';
			$items[] = false;

			$items[] = '<b>$value = countedValue</b> - Устанавливает значение реактивной переменной
			<xmp>this.set(\'value\', countedValue);</xmp>';

			$items[] = '<b>var countedValue = $value</b> - Присваивает переменной значение реактивной переменной
			<xmp>var countedValue = this.get(\'value\');</xmp>';

			$items[] = '<b>$value=></b> - Устанавливает значение реактивной переменной из одноименной переменной
			<xmp>this.set(\'value\', value);</xmp>';

			$items[] = '<b>$isActive!</b> - Меняет значение (boolean) реактивной переменной на противоположное
			<xmp>this.toggle(\'isActive\');</xmp>';

			$items[] = '<b>get options, value</b> - Обычно используется в начале функции. Автоматически получает значения реактивных переменных и записывает их в одноименные переменные.
			<xmp>var options = this.get(\'options\');</xmp><xmp>var value = this.get(\'value\');</xmp>';

			$items[] = '<b>var *$items = []</b> - Означает, что данная переменная должна установить значение одноименной реактивной переменной в конце функции
			<xmp>var *$items = [];</xmp><xmp>items.push(1);</xmp><xmp>items.push(2);</xmp>Реальный код будет выглядеть так:
			<xmp>var items = [];</xmp><xmp>items.push(1);</xmp><xmp>items.push(2);</xmp><xmp>this.set(\'items\', items);</xmp>';

			$items[] = '<b>$value++</b> - Изменяет значение реактивной переменной на 1
			<xmp>this.plusTo(\'value\', 1);</xmp>';

			$items[] = '<b>$value--</b> - Изменяет значение реактивной переменной на -1
			<xmp>this.plusTo(\'value\', -1);</xmp>';

			$items[] = '<b>$value *= 5</b> - Умножает значение реактивной переменной на 5
			<xmp>this.plusTo(\'value\', 5, \'*\');</xmp>';

			$items[] = '<b>$value /= 10</b> - Делит значение реактивной переменной на 10
			<xmp>this.plusTo(\'value\', 10, \'/\');</xmp>';

			$items[] = '<b>$value %= 2</b> - Присваивает реактивной переменной значение равное остатку от деления его на 2
			<xmp>this.plusTo(\'value\', 2, \'%\');</xmp>';

			$items[] = '<b>$items.add([\'apple\', \'banana\'])</b> - Добавляет элемент или несколько элементов в конец реактивной переменной и вызывает обновление компонента
			<xmp>this.addTo(\'items\', [\'apple\', \'banana\']);</xmp>';

			$items[] = '<b>$items.add(\'apple\', 0)</b> - Вставляет элемент или несколько элементов в реактивную переменную в заданную позицию и вызывает обновление компонента
			<xmp>this.addTo(\'items\', \'apple\', 0);</xmp>';

			$items[] = '<b>$items.addOne([\'apple\'])</b> - Добавляет элемент в конец реактивной переменной и вызывает обновление компонента. Даже если переданный аргумент является массивом будет добавлен только один элемент - сам массив
			<xmp>this.addOneTo(\'items\', [\'apple\']);</xmp>';

			$items[] = '<b>$items.addOne(\'apple\', 0)</b> - Вставляет элемент в реактивную переменную в заданную позицию и вызывает обновление компонента. Даже если переданный аргумент является массивом будет добавлен только один элемент - сам массив
			<xmp>this.addOneTo(\'items\', \'apple\', 0);</xmp>';

			$items[] = '<b>$items.removeAt(2)</b> - Удаляет элемент с индексом 2 из реактивной переменной и вызывает обновление компонента
			<xmp>this.removeByIndexFrom(\'items\', 2);</xmp>';

			$items[] = '<b>$items.remove(\'apple\')</b> - Удаляет элемент с заданным значением из реактивной переменной и вызывает обновление компонента
			<xmp>this.removeValueFrom(\'items\', \'apple\');</xmp>';

			$items[] = '<b>$items.each(function() {})</b> - Вызывает функцию обработчик для каждого элемента реактивной переменной
			<xmp>this.each(\'items\', function() {});</xmp>';

			$items[] = true;
			$items[] = 'Объекты DOM';
			$items[] = false;

			$items[] = '<b>&lt;&gt;</b> - Возвращает элемент компонента помеченный как scope или элемент, в котором компонент отредерен
			<xmp>var scope = <>;</xmp><xmp>var scope = this.getElement();</xmp>
			<xmp>var r = <>.getRect();</xmp><xmp>var r = this.getElement().getRect();</xmp>';

			$items[] = '<b>&lt;input&gt;</b> - Возвращает первый элемент в пределах компонента с тегом input
			<xmp>var input = <input>;</xmp><xmp>var input = this.findElement(\'input\');</xmp>
			<xmp>var v = <input>.value;</xmp><xmp>var v = this.findElement(\'input\').value;</xmp>
			<xmp><input>.clear();</xmp><xmp>this.findElement(\'input\').clear();</xmp>';

			$items[] = '<b>&lt;#container&gt;</b> - Возвращает первый элемент в пределах компонента, имеющий id = container
			<xmp>var cnt = <#container>;</xmp><xmp>var cnt = this.findElement(\'#container\');</xmp>
			<xmp>var h = <#container>.innerHTML;</xmp><xmp>var h = this.findElement(\'#container\').innerHTML;</xmp>
			<xmp><#container>.hide();</xmp><xmp>this.findElement(\'#container\').hide();</xmp>';

			$items[] = '<b>&lt;.item&gt;</b> - Возвращает первый элемент в пределах компонента, имеющий class = item
			<xmp>var i = <.item>;</xmp><xmp>var i = this.findElement(\'.item\');</xmp>
			<xmp>var h = <.item>.innerHTML;</xmp><xmp>var h = this.findElement(\'.item\').innerHTML;</xmp>
			<xmp><.item>.show();</xmp><xmp>this.findElement(\'.item\').show();</xmp>';

			$items[] = "<b>&lt;.@name&gt;</b> - Возвращает первый элемент в пределах компонента, имеющий сокращенный class = @name<br>
			Сокращенное имя класса состоит из имени класса компонента и дополнительного слова (в данном случае <b>name</b>)<br>
			Сокращения имен классов элементов подменяются на полные имена до парсинга самого кода шаблона<br><br>
			Элементы с сокращенными именами класса в шаблоне компонента <b>CatalogItem</b>:
			<xmp><div class=\"@\">\n   <div class=\"@name\">Item name</div>\n   <div class=\"@price\">2 000</div>\n</div></xmp>
			После преобразований код шаблона будет выглядеть так:
			<xmp><div class=\"catalog-item\">\n   <div class=\"catalog-item_name\">Item name</div>\n   <div class=\"catalog-item_price\">2 000</div>\n</div></xmp>
			Соответственно сокращения в JS коде выглядят так:
			<xmp><.@></xmp>
			<xmp><.@name></xmp>
			<xmp><.@price></xmp>";

			$items[] = '<b>&lt;.block[]&gt;</b> - Возвращает все элементы в пределах компонента, имеющие class = block
			<xmp>var blocks = <.block[]>;</xmp><xmp>var blocks = this.findElements(\'.block\');</xmp>
			<xmp>var firstBlock = <.block[]>[0];</xmp><xmp>var firstBlock = this.findElements(\'.block\')[0];</xmp>';

			$items[] = '<b>&lt;.item[2]&gt;</b> - Возвращает элемент с заданным индексом в пределах компонента, имеющий class = item
			<xmp>var i = <.item[2]>;</xmp><xmp>var i = this.findElements(\'.item\')[2];</xmp>
			<xmp>var h = <.item[2]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[2].innerHTML;</xmp>
			<xmp><.item[2]>.show();</xmp><xmp>this.findElements(\'.item\')[2].show();</xmp>';

			$items[] = '<b>&lt;.item[idx]&gt;</b> - Возвращает элемент с индексом из переменной в пределах компонента, имеющий class = item
			<xmp>var i = <.item[idx]>;</xmp><xmp>var i = this.findElements(\'.item\')[idx];</xmp>
			<xmp>var h = <.item[idx]>.innerHTML;</xmp><xmp>var h = this.findElements(\'.item\')[idx].innerHTML;</xmp>
			<xmp><.item[idx]>.show();</xmp><xmp>this.findElements(\'.item\')[idx].show();</xmp>';

			$items[] = '<b>&lt;:list&gt;</b> - Возвращает уже закешированный элемент в пределах компонента, имеющий атрибут as = list
			<xmp><ul class="items-list" as="list">'."\n\t<li>list item</li>\n".'</ul></xmp>
			<xmp>var l = <:list>;</xmp><xmp>var l = this.getElement(\'list\');</xmp>
			<xmp>var h = <:list>.innerHTML;</xmp><xmp>var h = this.getElement(\'list\').innerHTML;</xmp>
			<xmp><:list>.show();</xmp><xmp>this.getElement(\'list\').show();</xmp>';

			$items[] = '<b>&lt;::userInfo&gt;</b> - Возвращает уже закешированный дочерний компонент, имеющий атрибут as = userInfo
			<xmp><component class="UserInfo" as="userInfo"></xmp>
			<xmp>var ui = <::userInfo>;</xmp><xmp>var ui = this.getChild(\'userInfo\');</xmp>
			<xmp>var uie = <::userInfo>.getElement();</xmp><xmp>var uie = this.getChild(\'userInfo\').getElement();</xmp>';

			$items[] = '<b>&lt;::userInfo&gt;&lt;&gt;</b> - Возвращает главный элемент дочернего компонента, имеющего атрибут as = userInfo
			<xmp><component class="UserInfo" as="userInfo"></xmp>
			<xmp>var uie = <::userInfo><>;</xmp><xmp>var uie = this.getChild(\'userInfo\').getElement();</xmp>';


			$items[] = true;
			$items[] = 'События';
			$items[] = false;

			$items[] = '<b>--> change</b> - Инициирует событие (в пределах компонента) change 
			<xmp>--> change;</xmp><xmp>this.dispatchEvent(\'change\');</xmp>
			<xmp>-->change(param, ...);</xmp><xmp>this.dispatchEvent(\'change\', param, ...);</xmp>';
			
			$items[] = '<b>==> everythingReady</b> - Инициирует локальное (в пределах текущего маршрута) событие everythingReady 
			<xmp>==> everythingReady;</xmp><xmp>LocalState.dispatchEvent(\'everythingReady\');</xmp>
			<xmp>==> everythingReady (param, ...);</xmp><xmp>LocalState.dispatchEvent(\'everythingReady\', param, ...);</xmp>';

			$items[] = '<b>===> everythingReady</b> - Инициирует глобальное (в пределах всего приложения) событие everythingReady
			<xmp>===> everythingReady;</xmp><xmp>GlobalState.dispatchEvent(\'everythingReady\');</xmp>
			<xmp>===> everythingReady (param, ...);</xmp><xmp>GlobalState.dispatchEvent(\'everythingReady\', param, ...);</xmp>';

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
			$items[] = '<b>scope</b> - говорит о том, что элемент является главным контейнером, и поиск по компоненту будет производиться в его пределах';
			$items[] = '<b>test</b> - содержит список событий через запятую, которые нужно воссоздать при тестировании приложения. Игнорируется в продакшн-версии<xmp><button test="click,mouseover,mouseout"></xmp>';
			$items[] = true;
			$items[] = 'Атрибуты шаблонов';
			$items[] = false;
			$items[] = '<b>tmpid</b> - говорит, что шаблон будет включен по его id, например динамически<xmp><template tmpid="{&tmpid}"></xmp>';

			$items[] = true;
			$items[] = 'Атрибуты компонетов';
			$items[] = false;
			$items[] = '<b>сmpid</b> - задает компоненту его id, чтобы его можно было найти<xmp><component as="mainMenu"></xmp><xmp>this.getChild(\'mainMenu\');</xmp>';
			$items[] = '<b>class</b> - обязательный атрибут, который использует компилятор, показывает к какому классу принадлежит компонент<xmp><component class="Tooltip" as="tooltip"></xmp>';
			$items[] = '<b>name</b> - обязательный атрибут для компонентов с типом control, нужен для поиска контролов, сборки данных форм или просто вложенных контролов<xmp><control class="Select" name="color"></xmp><xmp>this.getControl(\'color\');</xmp>';

		break;

		case 'events':
			$items[] = "События применимы как в элементам DOM так и к компонентам";
			$items[] = "Чтобы добавить событие к элементу добавьте соответствующий атрибут в его тег<xmp><div onClick=\"handleClick\"></xmp>";
			$items[] = "Чтобы добавить событие к компоненту также добавьте соответствующий атрибут в его тег<xmp><Input onChange=\"handleInputChange\"></xmp>";
			$items[] = "Заметьте, что для определения функции обработчика используется обычная строка, а именно имя метода класса";
			$items[] = "Любой атрибут тега, удовлетворяющий паттерну <b>^on[A-Z]\w+$</b>, воспринимается компилятором как обработчик события";
			$items[] = "Не волнуйтесь о контексте вызываемого метода, на обработчик автоматически будет добавлен <b>bind</b>";
			$items[] = "Если элемент или компонент находится внутри другого дочернего компонента, то обработчик будет иметь контекст этого компонента, в данном случае класса <b>Container</b><xmp><Container>\n\t<Textarea onChange=\"onChangeTextarea\">\n</Container></xmp>";
			$items[] = "Итак в данном случае будет вызван метод <b>onChangeTextarea</b> класса <b>Container</b>";
			$items[] = "Чтобы вызвать метод класса, в шаблоне которого все это описывается, добавьте к имени обработчика ключевое слово <b>this</b><xmp><Container>\n\t<Textarea onChange=\"this.onChangeTextarea\">\n</Container></xmp>";
			$items[] = "Ключевое слово <b>this</b> добавляет к обработчику текущий контекст";
			$items[] = "Для того, чтобы пробросить событие вверх, не вызывая при этом отдельный метод, используйте восклицательный знак и имя события, которое будет инициировано текущим компонентом<xmp><Select onChange=\"!change\"></xmp>";
			$items[] = "В этом случае вложенность компонетов не имеет значения, событие будет инициировано у текущего компонента";
			$items[] = "Чтобы просто вызвать у события <b>stopPropagation</b> и не создавать при этом отдельный метод, используйте ключевое слово <b>stop</b><xmp><div onClick=\":stop\"></xmp>";
			$items[] = "Для вызова <b>preventDefault</b>, используйте ключевое слово <b>prevent</b><xmp><div onClick=\":prevent\"></xmp>";
		break;

		case 'jsobfus':
			$items[] = "JS обфускация необходима для обфусцирования названий переменных, полей, функций";
			$items[] = "Обфусцировав данные, компилятор создает карту соответствий";
			$items[] = "Специальный PHP класс, используя карту, обфусцирует входящие и исходящие данные на сервере";
			$items[] = "Для того, чтобы название было обфусцировано, используйте специальный маркер <b><<-</b><xmp>var name = data.userLastName <<-</xmp><xmp>var key = data['key<<-'] <<-</xmp><xmp>var n = data <<- .numbers <<- .first <<-</xmp>";
			$items[] = "После обфускации строка будет выглядеть на подобие этого:<xmp>var name = data.r</xmp><xmp>var key = data['df']</xmp><xmp>var n = a.t.j</xmp>";
			$items[] = "Между названием, которое должно быть обфусцировано, и маркером допускается наличие пробелов";
			$items[] = "После маркера также допускаются пробелы, как в третьем примере выше";
			$items[] = "Каждую компиляцию названиям присваиваются случайные сокращения. Для избежания проблем с кешированием на стороне клиента, используйте опцию случайного имени скомпилированного файла";
			$items[] = "Будьте внимательны, следите, чтобы маркеры были добавлены везде, например в коде шаблонов<xmp>\$filters <<- = data['filters <<-'];</xmp>В шаблоне код будет выглядеть примерно так:<xmp><div class=\"filter-items\">\n\t{foreach \$filters <<- as &filter}\n\t\t<div class=\"filter-item\">\n\t\t\t{&filter.name <<-}\n\t\t</div>\n\t{/foreach}\n</div></xmp>";

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