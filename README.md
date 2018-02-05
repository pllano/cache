# Кеширование для вашего проекта
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
// Передать конфигурацию 
// Если передать пустой массив [] возмет конфигурацию из файла cache_config.json
$config = [];
$url = 'site/index'; // Установить url или ключ
$cache_lifetime = 30*24*60*60; // Установить время жизни кеша
// Подключить класс
$cache = new Cache($config);
// Установить путь к файлу конфигурации
// $path = __DIR__ . '/../configs/';
// $cache->set_config($path);
// Проверяем статус кеширования и наличие кеша
if ($cache->run($url, $cache_lifetime) === null) {
    $content = []; // Получаем массив данных из базы
    // Если кеширование включено сохраняем кеш
    if ((int)$cache->state() == 1) {
        $cache->set($content);
    }
} else {
    // Если кеширование включено и кеш существует вернет массив данных из кеша
    $content = $cache->get();
}
```
## Установка
### Composer
```json
{
  "require": {
    "pllano/cache": "~1.0.1"
  }
}
```
### [AutoRequire](https://github.com/pllano/auto-require)
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
