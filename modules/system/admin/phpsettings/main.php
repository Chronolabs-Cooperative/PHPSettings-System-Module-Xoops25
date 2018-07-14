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
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

require_once __DIR__ . DS . 'handler.php';

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    exit(_NOPERM);
}

if (isset($_REQUEST)) {
    foreach ($_REQUEST as $k => $v) {
        ${$k} = $v;
    }
}
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'default', 'string');
// Setting type
$phpcat_id = system_CleanVars($_REQUEST, 'phpcat_id', 0, 'int');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_phpsettings.tpl';
// Call Header
xoops_cp_header();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
// Define scripts
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('modules/system/js/admin.js');
$xoTheme->addScript('modules/system/js/phpsettings.js');

$xoBreadCrumb->addLink(_AM_SYSTEM_PREFERENCES_NAV_MAIN, system_adminVersion('phpsettings', 'adminpath'));

//Display part
switch ($op) {

    case 'show':
        if (empty($phpcat_id)) {
            $phpcat_id = 0;
        }
        $phpini_handler = new SystemPHPSettings($GLOBALS['xoopsDB']);
        if (!isset($phpini_handler->_categories[$phpcat_id])) {
            redirect_header('admin.php?fct=phpsettings', 1);
        }
        $xoBreadCrumb->addLink($phpini_handler->_categories[$phpcat_id]);
        $xoBreadCrumb->addHelp(system_adminVersion('phpsettings', 'help'));
        $xoBreadCrumb->render();
        $xoopsTpl->assign('breadcrumb', 1);

        $form           = new XoopsThemeForm($phpini_handler->_categories[$phpcat_id], 'phpini_form', 'admin.php?fct=phpsettings', 'post', true);
        /* @var $config_handler XoopsConfigHandler  */
        $configs    = $phpini_handler->getByCategory($phpcat_id);
        foreach ($configs as $name => $config) {
            $title = ucwords(str_replace(array(".", '-', '_'), ' ', $name));
            $desc  = $config->getVar('comment')."<br /><br /><input type='checkbox' name='$name-defined' id='$name-defined' value='defined' " . ($config->getVar('state')=='defined'?'checked="checked" ':'') . "onclick='enablePHPSetting(\"$name\")' />&nbps;<strong>Enable for Definition!</strong>";
            $ele  = new XoopsFormElementTray($title);
            $eletxt = new XoopsFormText($title, $name, 50, 255, $myts->htmlspecialchars($config->getVar('value')));
            if ($config->getVar('state') == 'undefined')
                $eletxt->setExtra('disabled="disabled"');
            $elebut = array();
            if (strlen($config->getVar('last'))) {
                $elebut['last'] = new XoopsFormButton(_MD_AM_SYSTEM_BUTTON_SETLAST, "$name-last");
                $elebut['last']->setExtra("onclick='setPHPSettingValue(\"$name\", \"" . $myts->htmlspecialchars($config->getVar('last')) . "\")'");
                if ($config->getVar('state') == 'undefined')
                    $elebut['last']->setExtra('disabled="disabled"');
            }
            if (strlen($config->getVar('default'))) {
                $elebut['default'] = new XoopsFormButton(_MD_AM_SYSTEM_BUTTON_SETDEFAULT, "$name-default");
                $elebut['default']->setExtra("onclick='setPHPSettingValue(\"$name\", \"" . $myts->htmlspecialchars($config->getVar('default')) . "\")'");
                if ($config->getVar('state') == 'undefined')
                    $elebut['default']->setExtra('disabled="disabled"');
            }
            if (strlen($config->getVar('development'))) {
                $elebut['development'] = new XoopsFormButton(_MD_AM_SYSTEM_BUTTON_SETDEVELOPMENT, "$name-development");
                $elebut['development']->setExtra("onclick='setPHPSettingValue(\"$name\", \"" . $myts->htmlspecialchars($config->getVar('development')) . "\")'");
                if ($config->getVar('state') == 'undefined')
                    $elebut['development']->setExtra('disabled="disabled"');
            }
            if (strlen($config->getVar('production'))) {
                $elebut['production'] = new XoopsFormButton(_MD_AM_SYSTEM_BUTTON_SETPRODUCTION, "$name-production");
                $elebut['production']->setExtra("onclick='setPHPSettingValue(\"$name\", \"" . $myts->htmlspecialchars($config->getVar('production')) . "\")'");
                if ($config->getVar('state') == 'undefined')
                    $elebut['production']->setExtra('disabled="disabled"');
            }
            $hidden = new XoopsFormHidden('phpini_names[]', $name);
            $ele->setDescription($desc);
            $ele->addElement($eletxt, false);
            if (count($elebut)) {
                $elebutray  = new XoopsFormButtonTray('');
                foreach($elebut as $butt)
                    $elebutray->addElement($butt, false);
                $ele->addElement($elebutray, false);
            }
            $form->addElement($ele);
            $form->addElement($hidden);
            unset($ele, $hidden, $elebut, $eletxt, $elebutray);
        }
        $form->addElement(new XoopsFormHidden('op', 'save'));
        $form->addElement(new XoopsFormButton('', 'button', _GO, 'submit'));
        $form->display();
        break;

    case 'showmod':

        /* @var $config_handler XoopsConfigHandler  */
        $config_handler = xoops_getHandler('config');
        $mod            = isset($_REQUEST['mod']) ? (int)$_REQUEST['mod'] : 0;
        if ($mod <= 0) {
            header('Location: admin.php?fct=phpsettings');
            exit();
        }
        $config = $config_handler->getConfigs(new Criteria('conf_modid', $mod));
        $count  = count($config);
        if ($count < 1) {
            redirect_header('admin.php?fct=phpsettings', 1);
        }
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form           = new XoopsThemeForm(_MD_AM_MODCONFIG, 'pref_form', 'admin.php?fct=phpsettings', 'post', true);
        $module_handler = xoops_getHandler('module');
        $module         = $module_handler->get($mod);

        xoops_loadLanguage('modinfo', $module->getVar('dirname'));

        // if has comments feature, need comment lang file
        if ($module->getVar('hascomments') == 1) {
            xoops_loadLanguage('comment');
        }
        // RMV-NOTIFY
        // if has notification feature, need notification lang file
        if ($module->getVar('hasnotification') == 1) {
            xoops_loadLanguage('notification');
        }

        $modname = $module->getVar('name');
        if (!empty($_REQUEST['redirect'])) {
            $myts = MyTextSanitizer::getInstance();
            $form->addElement(new XoopsFormHidden('redirect', $myts->htmlspecialchars($_REQUEST['redirect'])));
        } elseif ($module->getInfo('adminindex')) {
            $form->addElement(new XoopsFormHidden('redirect', XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getInfo('adminindex')));
        }
        for ($i = 0; $i < $count; ++$i) {
            $title       = constant($config[$i]->getVar('conf_title'));
            $description = defined($config[$i]->getVar('conf_desc')) ? constant($config[$i]->getVar('conf_desc')) : '';
            switch ($config[$i]->getVar('conf_formtype')) {

                case 'textarea':
                    $myts = MyTextSanitizer::getInstance();
                    if ($config[$i]->getVar('conf_valuetype') === 'array') {
                        // this is exceptional.. only when value type is arrayneed a smarter way for this
                        $ele = ($config[$i]->getVar('conf_value') != '') ? new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars(implode('|', $config[$i]->getConfValueForOutput())), 5, 50) : new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), '', 5, 50);
                    } else {
                        $ele = new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()), 5, 50);
                    }
                    break;

                case 'select':
                    $ele     = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                    $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                    $opcount = count($options);
                    for ($j = 0; $j < $opcount; ++$j) {
                        $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                        $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                        $ele->addOption($optval, $optkey);
                    }
                    break;

                case 'select_multi':
                    $ele     = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                    $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                    $opcount = count($options);
                    for ($j = 0; $j < $opcount; ++$j) {
                        $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                        $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                        $ele->addOption($optval, $optkey);
                    }
                    break;

                case 'yesno':
                    $ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
                    break;

                case 'group':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                    break;

                case 'group_multi':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                    break;

                // RMV-NOTIFY: added 'user' and 'user_multi'
                case 'user':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                    break;

                case 'user_multi':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                    break;

                case 'password':
                    $myts = MyTextSanitizer::getInstance();
                    $ele  = new XoopsFormPassword($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;

                case 'color':
                    $myts = MyTextSanitizer::getInstance();
                    $ele  = new XoopsFormColorPicker($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;

                case 'hidden':
                    $myts = MyTextSanitizer::getInstance();
                    $ele  = new XoopsFormHidden($config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;

                case 'line_break':
                    $myts = MyTextSanitizer::getInstance();
                    $form->insertBreak('<div style="text-align:center">' . $title . '</div>', $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;

                case 'textbox':
                default:
                    $myts = MyTextSanitizer::getInstance();
                    $ele  = new XoopsFormText($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;

            }
            if (isset($ele)) {
                $ele->setDescription($description);
                $form->addElement($ele);
            }
            $hidden = new XoopsFormHidden('conf_ids[]', $config[$i]->getVar('conf_id'));
            $form->addElement($hidden);
            unset($ele, $hidden);
        }
        $form->addElement(new XoopsFormHidden('op', 'save'));
        $form->addElement(new XoopsFormButton('', 'button', _GO, 'submit'));
        if ($module->getVar('name') === 'System') {
            // Define Breadcrumb
            $xoBreadCrumb->addLink(_AM_SYSTEM_PREFERENCES_SETTINGS);
            $xoBreadCrumb->render();
            $xoopsTpl->assign('breadcrumb', 1);
        } else {
            if ($module->getInfo('adminindex')) {
                echo '<a href="' . XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getInfo('adminindex') . '">' . $module->getVar('name') . '</a>&nbsp;<span style="font-weight:bold;">&raquo;</span>&nbsp;' . _PREFERENCES . '<br><br>';
            } else {
                echo $module->getVar('name') . '&nbsp;<span style="font-weight:bold;">&raquo;</span>&nbsp;' . _PREFERENCES . '<br><br>';
            }
        }
        $form->display();
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=phpsettings', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $xoopsTpl         = new XoopsTpl();
        $count            = count($conf_ids);
        $tpl_updated      = false;
        $theme_updated    = false;
        $startmod_updated = false;
        $lang_updated     = false;
        if ($count > 0) {
            for ($i = 0; $i < $count; ++$i) {
                $config    = $config_handler->getConfig($conf_ids[$i]);
                $new_value =& ${$config->getVar('conf_name')};
                if (is_array($new_value) || $new_value != $config->getVar('conf_value')) {
                    // if language has been changed
                    if (!$lang_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') === 'language') {
                        $xoopsConfig['language'] = ${$config->getVar('conf_name')};
                        $lang_updated            = true;
                    }

                    // if default theme has been changed
                    if (!$theme_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') === 'theme_set') {
                        /* @var $member_handler XoopsMemberHandler */
                        $member_handler = xoops_getHandler('member');
                        $member_handler->updateUsersByField('theme', ${$config->getVar('conf_name')});
                        $theme_updated = true;
                    }
                    //todo: remove this code since it is not used anymore.
                    // if default template set has been changed
                    if (!$tpl_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') === 'template_set') {
                        // clear cached/compiled files and regenerate them if default theme has been changed
                        if ($xoopsConfig['template_set'] != ${$config->getVar('conf_name')}) {
                            $newtplset = ${$config->getVar('conf_name')};

                            // clear all compiled and cachedfiles
                            $xoopsTpl->clear_compiled_tpl();

                            // generate compiled files for the new theme
                            // block files only for now..
                            $tplfile_handler = xoops_getHandler('tplfile');
                            $dtemplates      = $tplfile_handler->find('default', 'block');
                            $dcount          = count($dtemplates);

                            // need to do this to pass to xoops_template_touch function
                            $GLOBALS['xoopsConfig']['template_set'] = $newtplset;

                            for ($j = 0; $j < $dcount; ++$j) {
                                $found = $tplfile_handler->find($newtplset, 'block', $dtemplates[$j]->getVar('tpl_refid'), null);
                                if (count($found) > 0) {
                                    // template for the new theme found, compile it
                                    xoops_template_touch($found[0]->getVar('tpl_id'));
                                } else {
                                    // not found, so compile 'default' template file
                                    xoops_template_touch($dtemplates[$j]->getVar('tpl_id'));
                                }
                            }

                            // generate image cache files from image binary data, save them under cache/
                            $image_handler = xoops_getHandler('imagesetimg');
                            $imagefiles    = $image_handler->getObjects(new Criteria('tplset_name', $newtplset), true);
                            foreach (array_keys($imagefiles) as $j) {
                                if (!$fp = fopen(XOOPS_CACHE_PATH . '/' . $newtplset . '_' . $imagefiles[$j]->getVar('imgsetimg_file'), 'wb')) {
                                } else {
                                    fwrite($fp, $imagefiles[$j]->getVar('imgsetimg_body'));
                                    fclose($fp);
                                }
                            }
                        }
                        $tpl_updated = true;
                    }

                    // add read permission for the start module to all groups
                    if (!$startmod_updated && $new_value != '--' && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') === 'startpage') {
                        /* @var $member_handler XoopsMemberHandler */
                        $member_handler     = xoops_getHandler('member');
                        $groups             = $member_handler->getGroupList();
                        /* @var $moduleperm_handler XoopsGroupPermHandler  */
                        $moduleperm_handler = xoops_getHandler('groupperm');
                        $module_handler     = xoops_getHandler('module');
                        $module             = $module_handler->getByDirname($new_value);
                        foreach ($groups as $groupid => $groupname) {
                            if (!$moduleperm_handler->checkRight('module_read', $module->getVar('mid'), $groupid)) {
                                $moduleperm_handler->addRight('module_read', $module->getVar('mid'), $groupid);
                            }
                        }
                        $startmod_updated = true;
                    }

                    $config->setConfValueForInput($new_value);
                    $config_handler->insertConfig($config);
                }
                unset($new_value);
            }
        }

        if (!empty($use_mysession) && $xoopsConfig['use_mysession'] == 0 && $session_name != '') {
            setcookie($session_name, session_id(), time() + (60 * (int)$session_expire), '/', XOOPS_COOKIE_DOMAIN, 0);
        }

        // Clean cached files, may take long time
        // User register_shutdown_function to keep running after connection closes so that cleaning cached files can be finished
        // Cache management should be performed on a separate page
        require_once XOOPS_ROOT_PATH . '/modules/system/class/maintenance.php';
        $maintenance = new SystemMaintenance();
        $options     = array(1,2,3); // smarty_cache and Smarty_compile
        register_shutdown_function(array(&$maintenance, 'CleanCache'), $options);

        if ($lang_updated) {
            // Flush cache files for cpanel GUIs
            xoops_load('cpanel', 'system');
            XoopsSystemCpanel::flush();
        }

        if (isset($redirect) && $redirect != '') {
            redirect_header($redirect, 2, _AM_SYSTEM_DBUPDATED);
        } else {
            redirect_header('admin.php?fct=phpsettings', 2, _AM_SYSTEM_DBUPDATED);
        }
        break;

    default:
        // Display setting cats
        $xoBreadCrumb->addTips(_AM_SYSTEM_PREFERENCES_NAV_TIPS);
        $xoBreadCrumb->addHelp(system_adminVersion('phpsettings', 'help'));
        $xoBreadCrumb->render();

        $confcat_handler = xoops_getHandler('configcategory');
        $confcats        = $confcat_handler->getObjects();
        $image           = system_adminVersion('phpsettings', 'configcat');
        $count_prefs     = 1;
        $nbcolonnes_pref = 5;
        foreach (array_keys($confcats) as $i) {
            $phpsettings['id']    = $confcats[$i]->getVar('confcat_id');
            $phpsettings['image'] = system_AdminIcons('xoops/' . $image[$i]);
            $phpsettings['name']  = constant($confcats[$i]->getVar('confcat_name'));
            ++$count_prefs;
            $phpsettings['newline'] = ($count_prefs % $nbcolonnes_pref == 1);// ? true : false;
            $xoopsTpl->assign('newline', $phpsettings['newline']);

            $xoopsTpl->append_by_ref('phpsettings', $phpsettings);
            unset($phpsettings);
        }
        $xoopsTpl->assign('menu', 1);
        $xoopsTpl->assign('breadcrumb', 1);
        break;
}
// Call Footer
xoops_cp_footer();
