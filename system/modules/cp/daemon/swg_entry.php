<?php
//j// BOF

/*n// NOTE
----------------------------------------------------------------------------
secured WebGine
net-based application engine
----------------------------------------------------------------------------
(C) direct Netware Group - All rights reserved
http://www.direct-netware.de/redirect.php?swg

This Source Code Form is subject to the terms of the Mozilla Public License,
v. 2.0. If a copy of the MPL was not distributed with this file, You can
obtain one at http://mozilla.org/MPL/2.0/.
----------------------------------------------------------------------------
http://www.direct-netware.de/redirect.php?licenses;mpl2
----------------------------------------------------------------------------
#echo(sWGwebServicesVersion)#
sWG/#echo(__FILEPATH__)#
----------------------------------------------------------------------------
NOTE_END //n*/
/**
* cp/daemon/swg_entry.php
*
* @internal   We are using phpDocumentor to automate the documentation process
*             for creating the Developer's Manual. All sections including
*             these special comments will be removed from the release source
*             code.
*             Use the following line to ensure 76 character sizes:
* ----------------------------------------------------------------------------
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/

/*#use(direct_use) */
use dNG\sWG\web\directPyHelper;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Basic configuration

/* -------------------------------------------------------------------------
Direct calls will be honored with an "exit ()"
------------------------------------------------------------------------- */

if (!defined ("direct_product_iversion")) { exit (); }

//j// Script specific commands

if (!isset ($direct_settings['cp_https_daemon_manage'])) { $direct_settings['cp_https_daemon_manage'] = false; }
if (!isset ($direct_settings['serviceicon_default_back'])) { $direct_settings['serviceicon_default_back'] = "mini_default_back.png"; }
if (!isset ($direct_settings['swg_pyhelper'])) { $direct_settings['swg_pyhelper'] = false; }
$direct_settings['additional_copyright'][] = array ("Module web_services #echo(sWGwebServicesVersion)# - (C) ","http://www.direct-netware.de/redirect.php?swg","direct Netware Group"," - All rights reserved");

//j// BOS
switch ($direct_settings['a'])
{
//j// $direct_settings['a'] == "command"
case "command":
{
	if (USE_debug_reporting) { direct_debug (1,"sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }

	$g_eid = (isset ($direct_settings['dsd']['deid']) ? ($direct_globals['basic_functions']->inputfilterBasic ($direct_settings['dsd']['deid'])) : "");
	$g_action = (isset ($direct_settings['dsd']['daction']) ? ($direct_globals['basic_functions']->inputfilterBasic ($direct_settings['dsd']['daction'])) : "");
	$g_rid = (isset ($direct_settings['dsd']['drid']) ? ($direct_globals['basic_functions']->inputfilterBasic ($direct_settings['dsd']['drid'])) : "");
	$g_source = (isset ($direct_settings['dsd']['source']) ? ($direct_globals['basic_functions']->inputfilterBasic ($direct_settings['dsd']['source'])) : "");
	$g_target = (isset ($direct_settings['dsd']['target']) ? ($direct_globals['basic_functions']->inputfilterBasic ($direct_settings['dsd']['target'])) : "");

	$g_source_url = ($g_source ? base64_decode ($g_source) : "m=cp;s=daemon+index;a=status");

	if ($g_target) { $g_target_url = base64_decode ($g_target); }
	else
	{
		$g_target = $g_source;
		$g_target_url = $g_source_url;
	}

	$direct_cachedata['page_this'] = "m=cp;s=daemon+entry;a=command;dsd=deid+{$g_eid}++daction+{$g_action}++drid+{$g_rid}++source+{$g_source}++target+".$g_target;
	$direct_cachedata['page_backlink'] = str_replace ("[oid]","",$g_source_url);
	$direct_cachedata['page_homelink'] = $direct_cachedata['page_backlink'];

	if ($direct_globals['kernel']->serviceInitDefault ())
	{
	if ($direct_settings['swg_pyhelper'])
	{
	if (($direct_globals['kernel']->vUsertypeGetInt ($direct_settings['user']['type']) > 3)||($direct_globals['kernel']->vGroupUserCheckRight ("cp_daemon_manage")))
	{
	//j// BOA
	$direct_globals['kernel']->serviceHttps ($direct_settings['cp_https_daemon_manage'],$direct_cachedata['page_this']);
	direct_local_integration ("cp_daemon");

	$g_daemon_object = new directPyHelper ();

	if (($g_daemon_object)&&($g_daemon_object->resourceCheck ()))
	{
		$direct_globals['output']->relatedManager ("cp_daemon_entry_command","pre_module_service_action");
		$direct_globals['kernel']->serviceHttps ($direct_settings['cp_https_daemon_manage'],$direct_cachedata['page_this']);
		$direct_globals['basic_functions']->requireClass ('dNG\sWG\directFormtags');

		direct_class_init ("formtags");
		$direct_globals['output']->optionsInsert (2,"servicemenu",$direct_cachedata['page_backlink'],(direct_local_get ("core_back")),$direct_settings['serviceicon_default_back'],"url0");

		$g_return_value = NULL;

		if (($g_eid)&&($g_action == "restart"))
		{
			$g_rid = "de.direct_netware.psd.plugins.queue.updateEntry";
			$g_return_value = $g_daemon_object->request ("de.direct_netware.psd.plugins.queue.updateEntry",(array ($g_eid,(array ("status" => "waiting")))));
		}
		elseif ($g_rid) { $g_return_value = $g_daemon_object->request ($g_rid); }

		if (isset ($g_return_value))
		{
			$direct_cachedata['output_command'] = direct_html_encode_special ($g_rid);
			$direct_cachedata['output_return_value'] = $direct_globals['formtags']->decode ($g_daemon_object->parseEvarsData ($g_return_value));

			$direct_globals['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
			$direct_globals['output']->relatedManager ("cp_daemon_entry_command","post_module_service_action");
			$direct_globals['output']->oset ("cp_daemon","command");
			$direct_globals['output']->outputSend (direct_local_get ("cp_daemon_command_run"));
		}
		else { $direct_globals['output']->outputSendError ("standard","core_daemon_rid_invalid","","sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }
	}
	else { $direct_globals['output']->outputSendError ("standard","core_daemon_unavailable","","sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }
	//j// EOA
	}
	else { $direct_globals['output']->outputSendError ("login","core_access_denied","","sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }
	}
	else { $direct_globals['output']->outputSendError ("standard","core_service_inactive","","sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }
	}

	$direct_cachedata['core_service_activated'] = true;
	break 1;
}
//j// EOS
}

//j// EOF
?>