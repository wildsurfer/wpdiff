<?php
/**
 * WPDIFF_Interface 
 * 
 * @package 
 * @version 0.1
 * @copyright Copyright 2012 by Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com> 
 * @license New BSD License
 */
interface WPDIFF_Interface {
    public function __construct($path);
    public function getVersion();
    public function getFiles();
}
