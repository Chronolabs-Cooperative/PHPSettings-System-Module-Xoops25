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
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 * @author       Dr. Simon Antony Roberts <chronolabscoop@users.sourceforge.net>
 */

$modversion['name']        = _AM_SYSTEM_PHPSETTINGS;
$modversion['version']     = '1.0';
$modversion['description'] = _AM_SYSTEM_PHPSETTINGS_DESC;
$modversion['author']      = '';
$modversion['credits']     = 'The XOOPS Project; Simon Roberts (AKA, wishcraft)';
$modversion['help']        = 'page=preferences';
$modversion['license']     = 'GPL see LICENSE';
$modversion['official']    = 1;
$modversion['image']       = 'phpsettings.png';

$modversion['hasAdmin']  = 1;
$modversion['adminpath'] = 'admin.php?fct=phpsettings';
$modversion['category']  = XOOPS_SYSTEM_PHPSETTINGS;

$modversion['configcat'][SYSTEM_CAT_PHPINIOPTIONS]   = 'system_phpinioptions.png';
$modversion['configcat'][SYSTEM_CAT_LANGUAGE]   = 'system_language.png';
$modversion['configcat'][SYSTEM_CAT_MISCELLANEOUS]   = 'system_miscellaneous.png';
$modversion['configcat'][SYSTEM_CAT_RESOURCELIMITS]   = 'system_resourcelimits.png';
$modversion['configcat'][SYSTEM_CAT_ERRORHANDLINGANDLOGGING] = 'system_errorhandlingandlogging.png';
$modversion['configcat'][SYSTEM_CAT_DATAHANDLING]   = 'system_datahandling.png';
$modversion['configcat'][SYSTEM_CAT_PATHSANDDIRECTORIES]   = 'system_pathsanddirectories.png';
$modversion['configcat'][SYSTEM_CAT_FILEUPLOADS]   = 'system_fileuploads.png';
$modversion['configcat'][SYSTEM_CAT_FOPENWRAPPERS] = 'system_fopenwrappers.png';
$modversion['configcat'][SYSTEM_CAT_DYNAMICEXTENSIONS]   = 'system_dynamicextensions.png';
$modversion['configcat'][SYSTEM_CAT_MODULESETTINGS]   = 'system_modulesettings.png';
