<?php
/**
 * This file is part of the API SHOP
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/pllano/api-shop
 * @version 1.0.1
 * @package pllano.api-shop
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Pllano\Caching\Cache;
 
class JsonCache {
 
    private $path;
    private $file;
    private $content;
 
    public function __construct($path)
    {
        $this->path = $path;
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
        if (!file_exists($this->path.'/.htaccess')) {
            $htaccess = '';
            $htaccess .= 'Order deny,allow'. PHP_EOL;
            $htaccess .= 'Deny from all';
            file_put_contents($this->path.'/.htaccess', $htaccess);
        }
    }
 
    public function getItem($key)
    {
        $this->file = $this->path.'/'.$key.'.json';
        if (file_exists($this->file)) {
            $content = json_decode(file_get_contents($this->file), true);
            $this->content = $content;
        } else {
            $this->content = null;
        }
    }
 
    public function get()
    {
        if(isset($this->content)) {
            return $this->content;
        } else {
            return null;
        }
    }
 
    public function set($content)
    {
        $this->content = json_encode($content);
    }
 
    public function save()
    {
        file_put_contents($this->file, $this->content);
    }
 
    public function expiresAfter($cache_lifetime = null)
    {
        return true;
    }
 
}
 