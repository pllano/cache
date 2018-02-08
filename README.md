# Кеширование для вашего проекта
## Демо и тесты
- Демо - https://xti.com.ua/
- Тест скорости - https://developers.google.com/speed/pagespeed/insights/?url=https://xti.com.ua
## Поддерживаемые типы кеша
- `memcached` - стабильно
- `memcache` - стабильно
- `filesystem` - стабильно
- `json` - стабильно
- `predis` - стабильно
- `redis` - стабильно
- `mongodb` - стабильно
- `elasticsearch` - в разработке
- `apcu` - дорабатывается (обнаружены ошибки в `cache/cache`)
- `apc` - дорабатывается (обнаружены ошибки в `cache/cache`)
- `array` - дорабатывается
- `illuminate` - дорабатывается
- `doctrine` - дорабатывается
## Конфигурация
Передать конфигурацию можно двумя способами:
- Из фала [`cache_config.json`](https://github.com/pllano/cache/blob/master/src/cache_config.json)
- Массивом в конструктор `$config = [];`
## Использование
```php
use Pllano\Caching\Cache;
 
// Передать конфигурацию в конструктор
// Если передать пустой массив [] возмет конфигурацию из файла cache_config.json
$cache_config = [];
$key = 'site/index'; // Передать url или ключ без кодирования
// $key = 'https://example.com/to/patch?param=data/lang=ru'; // При мультиязычности рекомендуется добавлять язык
$cache_lifetime = 30*24*60*60; // Установить время жизни кеша. в примере установлено 30 дней.
 
// Контент для сохранения передается в виде массива
$content = [];
 
// Подключить класс
$cache = new Cache($cache_config);
// Установить путь к файлу конфигурации
// $path = __DIR__ . '/../configs/';
// $cache->set_config($path);
// Проверяем статус кеширования и наличие кеша
if ($cache->run($key, $cache_lifetime) === null) {
    $content = []; // Получаем массив данных из базы
    // Если кеширование включено сохраняем кеш
    if ((int)$cache->state() == 1) {
        $cache->set($content, $key);
    }
} else {
    // Если кеширование включено и кеш существует вернет массив данных из кеша
    $content = $cache->get($key);
}
```
### Передать html код 
```php
// $cache->run_html();
// $cache->set_html();
// $cache->get_html();
 
if ($cache->run_html($key, $cache_lifetime) === null) {
    if ((int)$cache->state() == 1) {
        $cache->set_html($content, $key);
    }
} else {
    $content = $cache->get_html($key);
}
```
## Установка
### Подключить с помощью Composer
```diff
"require" {
    ...
-    "pllano/cache": "1.0.1",
+    "pllano/cache": "1.0.2",
    ...
}
```
### Подключить с помощью [AutoRequire](https://github.com/pllano/auto-require)
```json
{
  "require": [{
    "namespace": "Pllano\\Caching",
      "dir": "/pllano/cache/src",
      "link": "https://github.com/pllano/cache/archive/master.zip",
      "git": "https://github.com/pllano/cache",
      "name": "cache",
      "version": "master",
      "vendor": "pllano",
      "state": "1",
      "system_package": "1"
    }, {
      "namespace": "Cache",
      "dir": "/cache/cache/src",
      "link": "https://github.com/php-cache/cache/archive/1.0.0.zip",
      "git": "https://github.com/php-cache/cache",
      "name": "cache",
      "version": "1.0.0",
      "vendor": "cache",
      "state": "1",
      "system_package": "0"
    }, {
      "namespace": "Predis",
      "dir": "/predis/predis/src",
      "link": "https://github.com/nrk/predis/archive/v1.1.1.zip",
      "git": "https://github.com/nrk/predis",
      "name": "predis",
      "version": "1.1.1",
      "vendor": "predis",
      "state": "1",
      "system_package": "0"
    }, {
      "namespace": "MongoDB",
      "dir": "/mongodb/mongo-php-library/src",
      "link": "https://github.com/mongodb/mongo-php-library/archive/1.2.0.zip",
      "git": "https://github.com/mongodb/mongo-php-library",
      "name": "mongo-php-library",
      "version": "1.2.0",
      "vendor": "mongodb",
      "state": "1",
      "system_package": "0"
    }, {
      "namespace": "League\\Flysystem",
      "dir": "/league/flysystem/src",
      "link": "https://github.com/thephpleague/flysystem/archive/1.0.42.zip",
      "git": "https://github.com/thephpleague/flysystem",
      "name": "flysystem",
      "version": "1.0.42",
      "vendor": "league",
      "state": "1",
      "system_package": "0"
  }, {
      "namespace": "Psr\\SimpleCache",
      "dir": "/psr/simple-cache/src",
      "link": "https://github.com/php-fig/simple-cache/archive/1.0.0.zip",
      "git": "https://github.com/php-fig/simple-cache",
      "name": "simple-cache",
      "version": "1.0.0",
      "vendor": "psr",
      "state": "1",
      "system_package": "1"
    }, {
      "namespace": "Psr\\Log",
      "dir": "/psr/log/Psr/Log",
      "link": "https://github.com/php-fig/log/archive/1.0.2.zip",
      "git": "https://github.com/php-fig/log",
      "name": "log",
      "version": "1.0.2",
      "vendor": "psr",
      "state": "1",
      "system_package": "1"
    }, {
      "namespace": "Doctrine\\Common\\Cache",
      "dir": "/doctrine/cache/lib/Doctrine/Common/Cache",
      "link": "https://github.com/doctrine/cache/archive/v1.6.2.zip",
      "git": "https://github.com/doctrine/cache",
      "name": "cache",
      "version": "1.6.2",
      "vendor": "doctrine",
      "state": "1",
      "system_package": "1"
    }
  ]
}
```
