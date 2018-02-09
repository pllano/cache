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
 
namespace Pllano\Caching\Cache;
 
class Elasticsearch {
 
    private $config;
    private $cache_lifetime;
    private $host;
    private $port;
    private $type;
    private $index;
    private $auth;
    private $user;
    private $password;
    private $content;
    private $key;
 
    public function __construct($config)
    {
        $this->cache_lifetime = $config['cache']['cache_lifetime'];
        $this->config = $config['cache']['elasticsearch'];
        if(isset($config['host'])) { $this->host = $config['host']; }
        if(isset($config['port'])) { $this->port = $config['port']; }
        if(isset($config['type'])) { $this->type = $config['type']; }
        if(isset($config['index'])) { $this->index = $config['index']; }
        if(isset($config['auth'])) { $this->auth = $config['auth']; }
        if(isset($config['user'])) { $this->user = $config['user']; }
        if(isset($config['password'])) { $this->password = $config['password']; }
    }
 
    public function getItem($key)
    {
        return null;
    }
 
    public function save()
    {
        return null;
    }
 
    public function set($content)
    {
        return null;
    }
 
    public function expiresAfter()
    {
        return null;
    }
 
}
 