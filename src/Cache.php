<?php
/**
 * This file is part of the API SHOP
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/pllano/cache
 * @version 1.0.1
 * @package pllano.cache
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Pllano\Caching;
 
use Pllano\Caching\Utilities\Server;
 
class Cache
{
    private $config;
    private $state;
    private $dynamic;
    private $driver;
    private $www;
    private $cpu = '80';
    private $memory = '80';
    private $print = 0;
    private $cache_lifetime = 60;
    private $meminfo;
    private $nproc;
    private $clear_cache = null;
    protected $url;
    protected $pool;
	private $path_config;
 
    public function __construct($config = [])
    {
        if(isset($config)) {
			$use_config = $config;
		} else {
		    $use_config = $this->get_config();
		}
 
		$this->clear_cache = (int)$use_config['cache']['clear'];
        $this->state = (int)$use_config['cache']['state'];
        $this->dynamic = (int)$use_config['cache']['dynamic'];
        $this->driver = $use_config['cache']['driver'];
        $this->cache_lifetime = (int)$use_config['cache']['cache_lifetime'];
        $this->www = $use_config['dir']['www'];
        $this->cpu = $use_config['cache']['cpu'];
        $this->print = (int)$use_config['cache']['print'];
        $this->memory = $use_config['cache']['memory'];
 
        if (!file_exists($this->www.'/cache')) {
            mkdir($this->www.'/cache', 0777, true);
        }
        if (!file_exists($this->www.'/cache/.htaccess')) {
            $htaccess = '';
            $htaccess .= 'Order deny,allow'. PHP_EOL;
            $htaccess .= 'Deny from all';
            file_put_contents($this->www.'/cache/.htaccess', $htaccess);
        }
 
        // Запускаем тесты нагрузки на сервер
        $this->_server();
        // Устанавливаем конфигурацию для драйвера
        $this->config = $use_config['cache'][$this->driver];
        // Запускаем драйвер
        $this->driver();
    }
 
    public function get_config($path = null)
    {
		if(isset($path)) {
            $this->path_config = $path;
        } else {
            $this->path_config = __DIR__;
        }
		return json_decode($this->path_config.'/config.json', true);
	}
 
    public function run($url, $cache_lifetime = null)
    {
        $this->url = $url;
        if(isset($cache_lifetime)) {
            $this->cache_lifetime = (int)$cache_lifetime;
        }
        if ($this->state == 1) {
            if ($this->clear_cache == 1) {
                $this->clear();
                return null;
            }
            $key = $this->key($this->url);
            $driver = strtolower($this->driver);
            if ($driver == 'json') {
                $this->pool->getItem($key);
                $content = $this->pool->get();
            } else {
                $item = $this->pool->getItem($key);
                $content = json_decode(base64_decode($item->get()), true);
            }
            if(isset($content)) {
                return true;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
 
    public function get()
    {
        $key = $this->key($this->url);
        $driver = strtolower($this->driver);
        if ($driver == 'json') {
            $this->pool->getItem($key);
            $content = $this->pool->get();
        } else {
            $item = $this->pool->getItem($key);
            $content = json_decode(base64_decode($item->get()), true);
        }
        if(isset($content)) {
            if ($this->print == 1) {
                print("<br>url: <strong>{$this->url}</strong> - content из кеша");
                print("<br>key: {$key}");
                print("<br>Время жизни кеша, сек.: {$this->cache_lifetime}<br>");
            }
            return $content;
        } else {
            return null;
        }
    }
 
    public function set($content)
    {
        $key = $this->key($this->url);
        $driver = strtolower($this->driver);
        if ($driver == 'json') {
            $this->pool->getItem($key);
            $this->pool->set($content);
            $this->pool->save();
        } else {
            $item = $this->pool->getItem($key);
            $item->set(base64_encode(json_encode($content)));
            $item->expiresAfter($this->cache_lifetime);
            $this->pool->save($item);
        }
    }
 
    public function state()
    {
        return $this->state;
    }
 
    public function clear()
    {
        $this->pool->clear();
    }
 
    public function key($url)
    {
        return hash('md5', $url);
    }
 
    public function driver()
    {
        $driver = strtolower($this->driver);
 
        if ($driver == 'memcached') {
            $client = new \Memcached();
            $client->addServer($this->config['host'], $this->config['port']);
            $this->pool = new $this->config['pool']($client);
        } elseif ($driver == 'memcache') {
            $client = new \Memcache();
            $client->addServer($this->config['host'], $this->config['port']);
            $this->pool = new $this->config['pool']($client);
        } elseif ($driver == 'filesystem') {
            $filesystemAdapter = new $this->config['filesystem_adapter']($this->www.'/');
            $filesystem = new $this->config['filesystem']($filesystemAdapter);
            $this->pool = new $this->config['pool']($filesystem);
            $this->pool->setFolder($this->config['path']);
        } elseif ($driver == 'json') {
            $this->pool = new $this->config['pool']($this->www.'/'.$this->config['path']);
        } elseif ($driver == 'elasticsearch' || $driver == 'apcu' || $driver == 'apc' || $driver == 'array' || $driver == 'void') {
            $this->pool = new $this->config['pool']();
        } elseif ($driver == 'predis' || $driver == 'redis') {
            $client = new \Predis\Client('tcp:/'.$this->config['host'].':'.$this->config['port']);
            $this->pool = new $this->config['pool']($client);
        } elseif ($driver == 'mongodb') {
            $manager = new $this->config['manager']('mongodb://'.getenv('MONGODB_HOST'));
            $collection = $this->config['pool']::createCollection($manager, $this->config['host'].':'.$this->config['port'], $this->config['name']);
            $this->pool = new $this->config['pool']($collection);
        } elseif ($driver == 'illuminate') {
            // Create an instance of an Illuminate's Store
            $store = new $this->config['store']();
            // Wrap the Illuminate's store with the PSR-6 adapter
            $this->pool = new $this->config['pool']($store);
        } elseif ($driver == 'doctrine') {
            $memcached = new \Memcached();
            $memcached->addServer($this->config['host'], $this->config['port']);
            // Create a instance of Doctrine's MemcachedCache
            $doctrineCache = new $this->config['memcached']();
            $doctrineCache->setMemcached($memcached);
            // Wrap Doctrine's cache with the PSR-6 adapter
            $this->pool = new $this->config['pool']($doctrineCache);
        }
 
        return $driver;
    }
 
    public function _server()
    {
        $server = new Server();
        $this->meminfo = $server->meminfo();
        $this->nproc = $server->nproc();
 
        $memory = round($this->meminfo['MemFree'] / ($this->meminfo['MemTotal'] / 100),2);
        $cpu = $this->nproc/100*$this->cpu;
        $cpu_r = $this->nproc/100*80;
 
        if ($this->dynamic == 1) {
            $sys_get = sys_getloadavg();
            if ($this->print == 1) {
                print("<br>Занято оперативной памяти: {$memory} %");
                print("<br>Допустимый максимум оперативной памяти: {$this->memory}%");
                print("<br>Допустимый максимум CPU: {$cpu} ядер из {$this->nproc}");
                print("<br>Занято ядрер: {$sys_get['1']} из {$this->nproc}");
            }
            if ($sys_get['1'] >= $cpu || $sys_get['0'] >= $cpu_r || $memory >= $this->memory) {
                $this->state = 1;
            } else {
                $this->state = 0;
            }
            if ((int)$memory >= 90) {
                $this->state = 1;
                $this->driver = 'filesystem';
            }
        }
        if ($this->print == 1) {
            print("<br>driver: <strong>{$this->driver}</strong> - state: {$this->state}");
        }
    }
 
}
 