## Chronolabs Cooperative presents
# PHP Settings System Module  
## Version: 1.0.2 (stable)
## by. Dr. Simon Antony Roberts (Sydney) <fontmasters@snails.email>

# Foreword

This set of PHP Files is for allowing the system module in xoops 2.5 to set and configure PHP Settings from the PHP.INI file. 

## Installation

To Install copy the files into the XOOPS Root Path and then make the adjustments as per the Installation instruction below! You will need to then modify the following system module files! You will then once that is done need to log into the XOOPS Site administration control panel and update the system module...

### /module/system/xoops_version.php

Commit the following changes to the xoops_version.php file in the system module

#### Line 21

Increase the version by 1 decimal increment ie.. From:

    $modversion['version']     = 2.14;

Change to:

    $modversion['version']     = 2.15;

#### Line 110

Create a line after line 109 and insert the following:

    $modversion['templates'][] = array('file' => 'system_phpsettings.tpl', 'description' => '', 'type' => 'admin');

#### Line 471

Create a line after line 470 and insert the following:

    ++$i;
    $modversion['config'][$i]['name']        = 'execphpini';
    $modversion['config'][$i]['title']       = '_MI_SYSTEM_PREFERENCE_EXECPHPINI';
    $modversion['config'][$i]['description'] = '_MI_SYSTEM_PREFERENCE_EXECPHPINI_DESC';
    $modversion['config'][$i]['formtype']    = 'text';
    $modversion['config'][$i]['valuetype']   = 'text';
    $modversion['config'][$i]['default']     = 'php -i | grep \'Configuration File\''; // On Windows Use Find: php -i|find/i"configuration file"
    ++$i;
    $modversion['config'][$i]['name']        = 'writephpini';
    $modversion['config'][$i]['title']       = '_MI_SYSTEM_PREFERENCE_WRITEPHPINI';
    $modversion['config'][$i]['description'] = '_MI_SYSTEM_PREFERENCE_WRITEPHPINI_DESC';
    $modversion['config'][$i]['formtype']    = 'select';
    $modversion['config'][$i]['valuetype']   = 'text';
    $modversion['config'][$i]['default']     = 'phpini';
    $modversion['config'][$i]['options']     = array(_MI_SYSTEM_PREFERENCE_WRITEPHPINI_HTACCESS=>'htaccess', _MI_SYSTEM_PREFERENCE_WRITEPHPINI_ROOTPHPINI=>'phpini', _MI_SYSTEM_PREFERENCE_WRITEPHPINI_PRELOADER=>'preloader');

### /module/system/constants.php

Commit the following changes to the constants.php file in the system module

#### Line 36

Create a blank line after line 35 and insert the following:

    define('XOOPS_SYSTEM_PHPSETTINGS', 18);

#### Line 45

Create a blank line after line 44 and insert the following:

    // Configuration PHP Settings Category
    define('SYSTEM_CAT_PHPINIOPTIONS', 0);
    define('SYSTEM_CAT_LANGUAGE', 1);
    define('SYSTEM_CAT_MISCELLANEOUS', 2);
    define('SYSTEM_CAT_RESOURCELIMITS', 3);
    define('SYSTEM_CAT_ERRORHANDLINGANDLOGGING', 4);
    define('SYSTEM_CAT_DATAHANDLING', 5);
    define('SYSTEM_CAT_PATHSANDDIRECTORIES', 6);
    define('SYSTEM_CAT_FILEUPLOADS', 7);
    define('SYSTEM_CAT_FOPENWRAPPERS', 8);
    define('SYSTEM_CAT_DYNAMICEXTENSIONS', 9);
    define('SYSTEM_CAT_MODULESETTINGS', 10);

### /module/system/language/english/modinfo.php

Commit the following changes to the modinfo.php file in the system module

#### Line 40

Create a blank line after line 39 and add the following code:

    define('_MI_SYSTEM_ADMENU17', 'PHP Settings');

#### Line 59

Create a blank line after line 38 and insert the following code:

    define('_MI_SYSTEM_PREFERENCE_ACTIVE_PHPSETTINGS', '');

#### Line 84

Create a blank line after line 83 and add the following code insert

    define('_MI_SYSTEM_PREFERENCE_EXECPHPINI', 'Command to Execute to Locate PHP.INI on the system');
    define('_MI_SYSTEM_PREFERENCE_EXECPHPINI_DESC', 'This is the shell or bash or batch command to execute to locate PHP.INI on the system!');
    define('_MI_SYSTEM_PREFERENCE_WRITEPHPINI', 'Saving PHP.INI Settings');
    define('_MI_SYSTEM_PREFERENCE_WRITEPHPINI_DESC','This is how the PHP.INI settins is saved and written in the system!<br /><br /><em><strong>.htaccess will require</strong> you to enable the module on apache called rewrite to do this run:</em><br /><br />$ a2enmod rewrite');
    define('_MI_SYSTEM_PREFERENCE_WRITEPHPINI_HTACCESS', 'Use .htaccess for the values to be set!');
    define('_MI_SYSTEM_PREFERENCE_WRITEPHPINI_PHPINI', 'Use php.ini in XOOPS Site Root to set values!');
    define('_MI_SYSTEM_PREFERENCE_WRITEPHPINI_PRELOADER', 'Use a XOOPS Preloader to set the values!');
