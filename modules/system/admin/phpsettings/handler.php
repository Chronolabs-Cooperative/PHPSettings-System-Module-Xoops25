<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package      XOOPS-Core
 * @subpackage   System-Module
 * @since        1.0.0
 * @author       XOOPS Module Team
 * @author       Dr. Simon Antony Roberts <chronolabscoop@users.sourceforge.net>
 */

require_once __DIR__ . DS . 'phpini.php';

/**
 * System PHP Setting
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemPHPSetting extends XoopsObject
{
    function __construct()
    {
        parent::initVar('name', XOBJ_DTYPE_TXTBOX, NULL, true, 255);
        parent::initVar('category', XOBJ_DTYPE_INT, NULL, true);
        parent::initVar('state', XOBJ_DTYPE_ENUM, NULL, false, false, false, array('defined','undefined'));
        parent::initVar('mode', XOBJ_DTYPE_ENUM, NULL, false, false, false, array('write','written','checked'));
        parent::initVar('comment', XOBJ_DTYPE_OTHER, NULL);
        parent::initVar('default', XOBJ_DTYPE_TXTBOX, NULL, false, 255);
        parent::initVar('development', XOBJ_DTYPE_TXTBOX, NULL, false, 255);
        parent::initVar('production', XOBJ_DTYPE_TXTBOX, NULL, false, 255);
        parent::initVar('value', XOBJ_DTYPE_TXTBOX, NULL, false, 255);
        parent::initVar('last', XOBJ_DTYPE_TXTBOX, NULL, false, 255);
    }
}



/**
 * System PHP Setting
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemPHPSettings extends XoopsObjectHandler
{
    var $_phpini = array();
    
    var $_categories = array();
    
    function __construct($db = '')
    {
        parent::__construct($db);
        $this->className = 'SystemPHPSetting';
        $this->parsePHPINI();
    }
    
    function __destruct()
    {
        if (count($this->_phpini)) {
            if (is_file(XOOPS_VAR_PATH . DS . 'data' . DS . 'php.settings.json')) 
                unlink(XOOPS_VAR_PATH . DS . 'data' . DS . 'php.settings.json');
            $data = array();
            foreach($this->_phpini as $catid => $values)
                foreach($values as $varname => $object) 
                    $data[$object->getVar('name')] = $object->getValues(array_key($object->vars));
            file_put_contents(XOOPS_VAR_PATH . DS . 'data' . DS . 'php.settings.json', json_encode($data));
        }
    }
    
    function getByCategory($catid = 0)
    {
        if (isset($this->_phpini[$catid]))
            return $this->_phpini[$catid];
        return false;
    }
    
    private function parsePHPINI()
    {
        $this->_phpini = array();
        $mod = xoops_getHandler('module')->getByDirname('system');
        $configs = xoops_getHandler('config')->getConfigList($mod->getVar('mid'));
        exec($configs['execphpini'], $output);
        foreach($output as $out) {
            $parts = explode(" ", $out);
            foreach($parts as $part)
                if (is_file($part))
                    $inifile = $part;
        }
        if (is_file($inifile))
        {
            $phpini = new PHPIniHandler($inifile);
            if (count($phpini))
            {
                foreach($phpini as $category => $values) {
                    if (defined("SYSTEM_CAT_$category")) {
                        if (!isset($this->_categories[constant("SYSTEM_CAT_$category")]))
                            $this->_categories[constant("SYSTEM_CAT_$category")] = $category;
                        
                        foreach($values as $name => $data) {
                            $this->_phpini[constant("SYSTEM_CAT_$category")][$name] = new $this->className;
                            $this->_phpini[constant("SYSTEM_CAT_$category")][$name]->setVars($data->getArray());
                            $this->_phpini[constant("SYSTEM_CAT_$category")][$name]->setVar('category', constant("SYSTEM_CAT_$category"));
                        }
                    }
                }
            }
            if (is_file(XOOPS_VAR_PATH . DS . 'data' . DS . 'php.settings.json')) {
                $data = json_decode(file_get_contents(XOOPS_VAR_PATH . DS . 'data' . DS . 'php.settings.json'), true);
                foreach($data as $name => $value)
                   foreach($this->_phpini as $catid => $values)
                        foreach($values as $varname => $object) 
                            if ($varname == $name)
                                if (is_object($this->_phpini[$catid][$varname]))
                                    $this->_phpini[$catid][$varname]->setVars($value);
                            
            }
            switch($configs['writephpini'])
            {
                case 'htaccess':
                    if (is_file(XOOPS_ROOT_PATH . DS . '.htaccess')) {
                        $data = self::getWhitelessFile(XOOPS_ROOT_PATH . DS . '.htaccess');
                        foreach($data as $key => $line)
                        {
                            if (substr($line, 0, strlen('php_value')) == 'php_value')
                            {
                                $name = trim(substr($line, strlen('php_value'), strpos($line, ' ', strlen('php_value ') + 2) - strlen('php_value')));
                                $value = trim(substr($line, strpos($line, ' ', strlen('php_value ') + 2) + 1));
                                if (strlen($name) && strlen($value))
                                {
                                    foreach($this->_phpini as $catid => $values)
                                        foreach($values as $varname => $object) {
                                            if ($varname == $name) {
                                                $this->_phpini[$catid][$varname]->setVar('default', $object->getVar('value'));
                                                $this->_phpini[$catid][$varname]->setVar('value', $value);
                                                $this->_phpini[$catid][$varname]->setVar('state', 'defined');
                                            }
                                        }
                                }
                            }
                        }
                    }
                    break;
                case 'phpini':
                    if (is_file(XOOPS_ROOT_PATH . DS . 'php.ini')) {
                        $data = self::getWhitelessFile(XOOPS_ROOT_PATH . DS . 'php.ini');
                        foreach($data as $key => $line)
                        {
                            $parts = explode(' = ', $line);
                            if (count($parts)==2)
                            {
                                if (strlen($parts[0]) && strlen($parts[1]))
                                {
                                    foreach($this->_phpini as $catid => $values)
                                        foreach($values as $varname => $object) {
                                            if ($varname == $parts[0]) {
                                                $this->_phpini[$catid][$varname]->setVar('default', $object->getVar('value'));
                                                $this->_phpini[$catid][$varname]->setVar('value', $parts[1]);
                                                $this->_phpini[$catid][$varname]->setVar('state', 'defined');
                                            }
                                        }
                                }
                            }
                        }
                    }
                    break;
                case 'preloader':
                    if (is_file(XOOPS_VAR_PATH . DS . 'data' . DS . 'preloader.php.ini.json')) {
                        $data = json_decode(file_get_contents(XOOPS_VAR_PATH . DS . 'data' . DS . 'preloader.php.ini.json'), true);
                        foreach($data as $name => $value)
                        {
                            if (strlen($name) && strlen($value))
                            {
                                foreach($this->_phpini as $catid => $values)
                                    foreach($values as $varname => $object) {
                                        if ($varname == $name) {
                                            $this->_phpini[$catid][$varname]->setVar('default', $object->getVar('value'));
                                            $this->_phpini[$catid][$varname]->setVar('value', $value);
                                            $this->_phpini[$catid][$varname]->setVar('state', 'defined');
                                        }
                                    }
                            }
                        }
                    }
                    break;
            }
        }
    }
    
    private function getWhitelessFile($file = '')
    {
        if (!is_file($file))
            return array();
        $data = file($file);
        foreach($data as $key => $value)
            $data[$key] = str_replace("\r", "", str_replace("\n", "", $value));
        return $data;
    }
}