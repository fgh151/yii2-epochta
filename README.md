Yii2 epochta
===================
Модуль для работы с смс шлюзом epochta.
Основан на оригинальных классах.

Установка
------------

Предпочтительный способ установки через [composer](http://getcomposer.org/download/).


```
php composer.phar require --prefer-dist fgh151/yii2-epochta "*"
```

или добавить

```
"fgh151/yii2-epochta": "*"
```

в секцию require в файле `composer.json` .


Использование
-----

После установки необходимо внести правки в файлы конфигурации  :

Basic шаблон ```config/web.php```

Advanced шаблон ```[backend|frontend|common]/config/main.php```

>
        'modules'    => [
            'smsGate' => [
                'class' => 'fgh151\modules\epochta\Module',
                'sms_key_private' => 'Ваш приватный ключ',
                'sms_key_public' => 'Ваш публичный ключ',
                'testMode' => true, //Включение тестового режима
                'URL_GAREWAY' => 'URL шлюза, можно не указывать'
            ],
            ...
            ...
        ],


API
-----

####Регистрауия имени отправителя

```php
$gate = new smsGate()
$gate->registerSender('testName');
```

параметры:
имя

####Создание адресной книги

```php
$gate = new smsGate()
$gate->createAddressBook('test address book');
```

параметры:
название новой книги

####Добавление телефона в адресную книгу

```php
$gate = new smsGate()
$gate->addPhoneToBook(1, '79010000002', 'Сергей;Вершинин;');
```

параметры:
id книги
телефон
имя получателя

####Проверка можно лиотправить сообщение по адресатам книги

```php
$gate = new smsGate()
$gate->testCampaign("testName", "Тестируем отправку смс сообщения через ePochta SMS", 1);
```

параметры:
название кампании
текст сообщения
id адресной книги

####Отправка сообщения

```php
$gate = new smsGate()
$gate->createCampaign("testName", "Тестируем отправку смс сообщения через ePochta SMS", 1);
```

параметры:
название кампании
текст сообщения
id адресной книги

####Проверка статуса

```php
$gate = new smsGate()
$gate->getStatus(1);
```

параметры:
id кампании