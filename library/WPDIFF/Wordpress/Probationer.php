<?php

require_once ('WPDIFF/Wordpress.php');

/**
 * WPDIFF_Wordpress_Probationer 
 * 
 * @uses WPDIFF
 * @uses _Wordpress
 * @package 
 * @version 0.1
 * @copyright Copyright 2012 by Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com> 
 * @license New BSD License http://en.wikipedia.org/wiki/BSD_licenses
 */
class WPDIFF_Wordpress_Probationer extends WPDIFF_Wordpress {

    // public __construct(path) {{{ 
    /**
     * __construct
     * 
     * @param mixed $path 
     * @access public
     * @return void
     */
    public function __construct($path){
        $this->_path_to_folder = $path;
        if (!is_dir($path))
            throw new Exception('$path is not a directory');
        if (!is_dir($path.'/wp-includes'))
            throw new Exception('wp-includes folder not found');
        if (!is_dir($path.'/wp-content'))
            throw new Exception('wp-content folder not found');
    }
    // }}}

}
