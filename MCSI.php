<?php
/**
 * MCSI extension
 *
 * @file
 *
 * We regroup all extensions documentation in the group named "Extensions":
 * @ingroup Extensions
 *
 * The author would let everyone know who wrote the code, if there is more
 * than one author, add multiple author annotations:
 * @author Afa Cheng
 *
 * @version 1.0
 *
 * The license governing the extension code:
 * @license MIT License
 */

$wgExtensionCredits['other'][] = array(
    'path' => __FILE__,
    'name' => 'Moegirl Client Service Infrastructure',
    'author' => array(
        'Sakamoto Poteko'
    ),
    'version'  => '0.9.91',
    'url' => 'about:blank',
    'license-name' => 'MIT',
    'descriptionmsg' => 'mcsi-desc',
);

$wgMCSIServerUrl    = 'https://moegirlproxy.azure-mobile.net';
$wgMCSIServerAppKey = 'CHANGEME';
//$wgMCSIServerUrl    = 'http://192.168.1.104:45826';
//$wgMCSIServerAppKey = 'OOBoTWllzAABBBWWWdddvvTsssKsdaww';

/* Setup */

// Initialize an easy to use shortcut:
$dir = dirname( __FILE__ );
$dirbasename = basename( $dir );

$wgMessagesDirs['MCSI'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['MCSIAlias'] = $dir . '/MCSI.i18n.alias.php';
$wgExtensionMessagesFiles['MCSIMagic'] = $dir . '/MCSI.i18n.magic.php';

// Register files
// MediaWiki need to know which PHP files contains your class. It has a
// registering mecanism to append to the internal autoloader. Simply use
// $wgAutoLoadClasses as below:
$wgAutoloadClasses['MCSIBackendReq'] = $dir . '/MCSI.BackendReq.php';
$wgAutoloadClasses['MCSIHooks'] = $dir . '/MCSI.hooks.php';

// Register hooks
// See also http://www.mediawiki.org/wiki/Manual:Hooks
$wgHooks['PageContentSave'][] = 'MCSIHooks::onPageContentSaveComplete';
$wgHooks['ArticleDeleteComplete'][] = 'MCSIHooks::onArticleDeleteComplete';
$wgHooks['TitleMoveComplete'][] = 'MCSIHooks::onTitleMoveComplete';


