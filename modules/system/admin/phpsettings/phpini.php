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

/**
 * PHP.INI
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class PHPIni
{
    var $_category = NULL;
    
    var $_name = NULL;
    
    var $_comment = NULL;
    
    var $_default = NULL;
    
    var $_development = NULL;
    
    var $_production = NULL;
    
    var $_value = NULL;
    
    var $_state = NULL;
    
    function setVar($name = '', $value = '')
    {
        switch($name)
        {
            case 'category':
                $this->_category = $value;
                break;
            case 'name':
                $this->_name = $value;
                break;
            case 'comment':
                $this->_comment = $value;
                break;
            case 'default':
                $this->_default = $value;
                break;
            case 'development':
                $this->_development = $value;
                break;
            case 'production':
                $this->_production = $value;
                break;
            case 'value':
                $this->_value = $value;
                break;
            case 'state':
                $this->_state = $value;
                break;
        }
        
    }
    
    function getVar($name = '')
    {
        switch($name)
        {
            case 'category':
                return $this->_category;
                break;
            case 'name':
                return $this->_name;
                break;
            case 'comment':
                return $this->_comment;
                break;
            case 'default':
                return $this->_default;
                break;
            case 'development':
                return $this->_development;
                break;
            case 'production':
                return $this->_production;
                break;
            case 'value':
                return $this->_value;
                break;
            case 'state':
                return $this->_state;
                break;
        }
    }
    
    
    function getArray()
    {
        return array(   'category'      =>      $this->_category,
                        'name'          =>      $this->_name,
                        'comment'       =>      $this->_comment,
                        'default'       =>      $this->_default,
                        'development'   =>      $this->_development,
                        'production'    =>      $this->_production,
                        'value'         =>      $this->_value,
                        'state'         =>      $this->_state
                    );
    }
    
}



/**
 * PHP.INI Handler
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class PHPIniHandler
{
    var $_categories = '';
    
    var $_data = array();
    
    function __construct($inifile = '')
    {
        if (is_file($inifile))
            return self::parsePHPINI($inifile);
    }
    
    function parsePHPINI($inifile = '')
    {
        
        if (!is_file($inifile))
            return false;
        
        $this->_data = array();
        $processcat = $findingcat = false;
        $category = $name = $comment = $default = $development = $production = $value = $state = '';
        $data = self::getWhitelessFile($inifile);
        foreach($data as $key => $val)
        {
            if ($processcat == true)
            {
                switch ($category)
                {
                    default:
                        if (substr($val, 0, 2) = '; ') {
                            if (substr($val, 0, strlen('; Default Value:')) = '; Default Value:')
                            {
                                $default = trim(substr($val, strlen('; Default Value:'), strlen($val) - strlen('; Default Value:') + 1));
                            } elseif (substr($val, 0, strlen('; Development Value:')) = '; Development Value:') {
                                $development = trim(substr($val, strlen('; Development Value:'), strlen($val) - strlen('; Development Value:') + 1));
                            } elseif (substr($val, 0, strlen('; Production Value:')) = '; Production Value:') {
                                $production = trim(substr($val, strlen('; Production Value:'), strlen($val) - strlen('; Production Value:') + 1));
                            } else {
                                if (strlen($comment)>0)
                                    $comment .= '<br />';
                                $comment .= trim(substr($val, 2, strlen($val) - 2));
                            }
                        } else {
                            $parts = explode(" = ", $val);
                            if (count($parts)>=2)
                            {
                                $value = $parts[1];
                                if (substr($parts[0],0,1)==';')
                                {
                                    $name = substr($parts[0], 1, strlen($parts[0]) - 1);
                                    $state = 'undefined';
                                } else {
                                    $name = $parts[0];
                                    $state = 'defined';
                                }
                            } elseif (count($parts)=1) {
                                $value = '';
                                if (substr($parts[0],0,1)==';')
                                {
                                    $name = substr($parts[0], 1, strlen($parts[0]) - 1);
                                    $state = 'undefined';
                                } else {
                                    $name = $parts[0];
                                    $state = 'defined';
                                }
                            }
                        }
                        
                        break;
                    case "ABOUTPHPINI":
                    case "ABOUTTHISFILE":
                    case "QUICKREFERENCE":
                        break;
                }
                
                if (strlen($name)>0 && in_array($state, array('undefined', 'defined')))
                {
                    if (!isset($this->_data[$category][$name]))
                        $this->_data[$category][$name] = new PHPIni();
                    $this->_data[$category][$name]->setVar('category', $category);
                    $this->_data[$category][$name]->setVar('name', $name);
                    $this->_data[$category][$name]->setVar('comment', $comment);
                    $this->_data[$category][$name]->setVar('default', $default);
                    $this->_data[$category][$name]->setVar('development', $development);
                    $this->_data[$category][$name]->setVar('production', $production);
                    $this->_data[$category][$name]->setVar('value', $value);
                    $this->_data[$category][$name]->setVar('state', $state);
                    $name = $comment = $default = $development = $production = $value = $state = '';
                }
            }
            
            if ($findingcat == true && substr($val, 0, 2) = '; ')
            {
                $category = strtoupper(str_replace(array(" ", ";", ',', '.'), '', $val));
                if (!isset($this->_categories[$category]))
                    $this->_categories[$category] = str_replace(array("; ", " ;"), '', $val);
            }
            
            if (count(explode(";", $val)) == strlen($val) || count(explode(";", $val)) == strlen($val)+1)
            {
                if ($findingcat == false)
                {
                    $findingcat = true;
                    $processcat = false;
                } else {
                    $findingcat = false;
                    $processcat = true;
                }
            }
        }
        return $this->_data;
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