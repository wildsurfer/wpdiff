<?php

require_once ('WPDIFF/Wordpress.php');

/**
 * WPDIFF_Wordpress_Original 
 * 
 * @uses WPDIFF
 * @uses _Wordpress
 * @package 
 * @version 0.1
 * @copyright Copyright 2012 by Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com> 
 * @license New BSD License http://en.wikipedia.org/wiki/BSD_licenses
 */
class WPDIFF_Wordpress_Original extends WPDIFF_Wordpress {

    // public __construct(version='latest') {{{ 
    /**
     * __construct
     * 
     * @param string $version 
     * @access public
     * @return void
     */
    public function __construct($version='latest'){
        $url = "http://wordpress.org/wordpress-{$version}.zip";
        $tmp_dir = sys_get_temp_dir();
        $pathinfo = pathinfo($url);
        
        $this->_version = $version;
        $this->_path_to_folder = "{$tmp_dir}/wordpress-{$version}/wordpress";
        $this->_path_to_archive = "{$tmp_dir}/{$pathinfo['basename']}";
        
        if (!$this->_testUrl($url))
            throw new Exception('Invalid URL or Wordpress version');
        if (!$this->_fetch($url))
            throw new Exception('Fetching failed');
        if (!$this->_unzip())
            throw new Exception('Unzip failed');
    }
    // }}}

    // private _testUrl(url) {{{ 
    /**
     * _testUrl checks if given URL is ok
     * 
     * @param mixed $url 
     * @access private
     * @return bool
     */
    private function _testUrl($url) {
        $headers = get_headers($url, 1);
        if ($headers[0] == 'HTTP/1.1 200 OK')
            return true;
        else 
            return false;
    }
    // }}}

    // private _fetch(url) {{{ 
    /**
     * _fetch downloads zip archive from wordpress.org
     * 
     * @param mixed $url 
     * @access private
     * @return bool
     */
    private function _fetch($url) {
        $pathinfo = pathinfo($url);
        $tmp_dir = sys_get_temp_dir();
        $path = $this->_path_to_archive = "{$tmp_dir}/{$pathinfo['basename']}";

        if (file_exists($path)) 
            return true;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data) {
            file_put_contents($path, $data);
            //echo 'Curl error: ' . curl_error($ch);
            return true;
        }
        else 
            return false;
    }
    // }}}

    // private _unzip() {{{ 
    /**
     * _unzip extracts ZIP archive
     * 
     * @access private
     * @return bool
     */
    private function _unzip(){
        $path = $this->_path_to_archive;
        $pathinfo = pathinfo($path);

        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            $dir = $pathinfo['dirname'].'/wordpress-'.$this->_version;
            $zip->extractTo($dir);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
    // }}}
}
