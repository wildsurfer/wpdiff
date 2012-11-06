<?php

require_once ('php-diff/lib/Diff.php');
require_once ('php-diff/lib/Diff/Renderer/Html/SideBySide.php');

/**
 * WPDIFF_Differer 
 * 
 * @package 
 * @version 0.1
 * @copyright Copyright 2012 by Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com> 
 * @license New BSD License http://en.wikipedia.org/wiki/BSD_licenses
 */
class WPDIFF_Differer {

    private $_left_object;
    private $_right_object;
    private $_files_only_in_left;
    private $_files_only_in_right;

    // public __construct(WPDIFF_Wordpressleft,WPDIFF_Wordpressright) {{{ 
    /**
     * __construct
     * 
     * @param WPDIFF_Wordpress $left 
     * @param WPDIFF_Wordpress $right 
     * @access public
     * @return void
     */
    public function __construct(WPDIFF_Wordpress $left, WPDIFF_Wordpress $right) {
        $this->_left_object = $left;
        $this->_right_object = $right;
    }
    // }}}

    // public versionDiff() {{{ 
    /**
     * versionDiff compares versions of left and right Wordpress classes
     * 
     * @access public
     * @return array or bool
     */
    public function versionDiff(){
        $left_version = $this->_left_object->getVersion();
        $right_version = $this->_right_object->getVersion();
        if ($left_version == $right_version)
            return false;
        else 
            return array(
                'left' => $left_version, 'right' => $right_version
            );
    }
    // }}}

    // public filesDiff() {{{ 
    /**
     * filesDiff return array of files that are differ. Files that are not exist in both Wordpress 
     * instances are ignored
     * 
     * @access public
     * @return array or bool
     */
    public function filesDiff(){
        $left_array = $this->_left_object->getFiles();
        $right_array = $this->_right_object->getFiles();
        $array = $this->_arrayDiff($left_array,$right_array);

        $result = false;

        foreach ($array as $file) {
            $left_file_path = $this->_left_object->getPath().'/'.$file;
            $right_file_path = $this->_right_object->getPath().'/'.$file;
            if (is_file($left_file_path) && is_file($right_file_path)) {
                $left_file_content = preg_replace("/\r/",'',file_get_contents($left_file_path));
                $right_file_content = preg_replace("/\r/",'',file_get_contents($right_file_path));
                $left_file_content = explode(PHP_EOL,$left_file_content);
                $right_file_content = explode(PHP_EOL,$right_file_content);
                $options = array();
                $diff = new Diff($left_file_content, $right_file_content, $options);
                $renderer = new Diff_Renderer_Html_SideBySide;
                $result[$file] = $diff->Render($renderer);
            }
        }

        return $result;
    }
    // }}}

    // public filesOnlyInLeft() {{{ 
    /**
     * filesOnlyInLeft returns files that exist only in left instance of Wordpress
     * 
     * @access public
     * @return array
     */
    public function filesOnlyInLeft(){
        $left_array = $this->_left_object->getFiles();
        $right_array = $this->_right_object->getFiles();
        return $this->_arrayDiffOnly($left_array,$right_array);
    }
    // }}}

    // public filesOnlyInRight() {{{ 
    /**
     * filesOnlyInRight returns files that exist only in right instance of Wordpress
     * 
     * @access public
     * @return void
     */
    public function filesOnlyInRight(){
        $left_array = $this->_left_object->getFiles();
        $right_array = $this->_right_object->getFiles();
        return $this->_arrayDiffOnly($right_array,$left_array);
    }
    // }}}

    // private _arrayDiff(left_array,right_array) {{{ 
    /**
     * _arrayDiff returns array with files that are differ
     * 
     * @param mixed $left_array 
     * @param mixed $right_array 
     * @access private
     * @return array
     */
    private function _arrayDiff($left_array,$right_array) {
        $array = array();
        foreach ($left_array as $hash_l => $path_l) {
            $hash_r = array_search($path_l, $right_array);
            if ($hash_l != $hash_r) $array[$hash_l] = $path_l;
        }
        return $array;
    }
    // }}}

    // private _arrayDiffOnly(left_array,right_array) {{{ 
    /**
     * _arrayDiffOnly returns array with files that not exist
     * 
     * @param mixed $left_array 
     * @param mixed $right_array 
     * @access private
     * @return array
     */
    private function _arrayDiffOnly($left_array,$right_array) {
        $array = array();
        foreach ($left_array as $hash_l => $path_l) {
            $hash_r = array_search($path_l, $right_array);

            if ($path_l == 'index.php') {
                $hash_r = array_search($path_l, $right_array);
            }

            if (!$hash_r) $array[$hash_l] = $path_l;
        }
        return $array;
    }
    // }}}

}
