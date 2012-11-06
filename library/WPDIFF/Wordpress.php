<?php

require_once ('WPDIFF/Interface.php');

/**
 * WPDIFF_Wordpress 
 * 
 * @uses WPDIFF_Interface
 * @abstract
 * @package 
 * @version 0.1
 * @copyright Copyright 2012 by Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com> 
 * @license New BSD License http://en.wikipedia.org/wiki/BSD_licenses
 */
abstract class WPDIFF_Wordpress implements WPDIFF_Interface {

    protected $_files_stack;
    protected $_version;
    protected $_path_to_archive;
    protected $_path_to_folder;

    protected $_disable_themes = false;
    protected $_disable_uploads = false;

    // public disableThemes() {{{ 
    /**
     * disableThemes
     * 
     * @access public
     * @return void
     */
    public function disableThemes() {
        $this->_disable_themes = true;
    }
    // }}}
    
    // public disableUploads() {{{ 
    /**
     * disableUploads
     * 
     * @access public
     * @return void
     */
    public function disableUploads() {
        $this->_disable_uploads = true;
    }
    // }}}

    // public getVersion() {{{ 
    /**
     * getVersion
     * 
     * @access public
     * @return string
     */
    public function getVersion() {
        $path = $this->_path_to_folder.'/wp-includes/version.php';
        include($path);
        
        if (empty($wp_version))
            throw new Exception('$wp_version could not be detected at '.$path);
               
        return $wp_version;
    }
    // }}}

    // public getPath() {{{ 
    /**
     * getPath returns path to folder with Wordpress instance
     * 
     * @access public
     * @return string
     */
    public function getPath() {
        return $this->_path_to_folder;
    }
    // }}}

    // public getFiles() {{{ 
    /**
     * getFiles returns array with all files inside Wordpress folder
     * 
     * @access public
     * @return array
     */
    public function getFiles(){
        if (!empty($this->_files_stack))
            return $this->_files_stack;

        $dir = $this->_path_to_folder;
        $this->_files_stack = $this->_listFiles($dir);
        return $this->_files_stack;
    }
    // }}}

    // private _listFiles(dir) {{{ 
    /**
     * _listFiles parses directory with Wordpress
     * 
     * @param mixed $dir 
     * @access private
     * @return array
     */
    private function _listFiles($dir){
        $list = array();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $subdir = $dir.'/'.$entry;
                    if (is_dir($subdir)) {
                        $sub_list = $this->_listFiles($subdir);
                        $list = array_merge($list,$sub_list);
                    }
                    elseif (
                        $this->_checkIfThemesNotDisabled($subdir) &&
                        $this->_checkIfUploadsNotDisabled($subdir)
                    ) {
                        $file_content = preg_replace('/\r/','',file_get_contents($subdir));
                        $hash = md5($file_content.str_replace($this->_path_to_folder.'/','',$subdir));
                        $list[$hash] = str_replace($this->_path_to_folder.'/','',$subdir);
                    }
                }
            }
            closedir($handle);
        }
        return $list;
    }
    // }}}
    
    // private _checkIfThemesNotDisabled(dir) {{{ 
    /**
     * _checkIfThemesNotDisabled
     * 
     * @param mixed $dir 
     * @access private
     * @return bool
     */
    private function _checkIfThemesNotDisabled($dir) {
        if ($this->_disable_themes) {
            if (preg_match('/wp-content\/themes/',$dir))
                return false;
        }
        return true;
    }
    // }}}

    // private _checkIfUploadsNotDisabled(dir) {{{ 
    /**
     * _checkIfUploadsNotDisabled
     * 
     * @param mixed $dir 
     * @access private
     * @return bool
     */
    private function _checkIfUploadsNotDisabled($dir) {
        if ($this->_disable_uploads) {
            if (preg_match('/wp-content\/uploads/',$dir))
                return false;
        }
        return true;
    }
    // }}}

}
