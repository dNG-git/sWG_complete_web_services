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
if (!isset ($direct_settings['cp_daemon_entries_per_page'])) { $direct_settings['cp_daemon_entries_per_page'] = 30; }
if (!isset ($direct_settings['serviceicon_cp_daemon_entry_restart'])) { $direct_settings['serviceicon_cp_daemon_entry_restart'] = "mini_default_option.png"; }
if (!isset ($direct_settings['serviceicon_cp_daemon_plugins_reload'])) { $direct_settings['serviceicon_cp_daemon_plugins_reload'] = "mini_default_option.png"; }
if (!isset ($direct_settings['serviceicon_default_back'])) { $direct_settings['serviceicon_default_back'] = "mini_default_back.png"; }
if (!isset ($direct_settings['swg_pyhelper'])) { $direct_settings['swg_pyhelper'] = false; }
$direct_settings['additional_copyright'][] = array ("Module web_services #echo(sWGwebServicesVersion)# - (C) ","http://www.direct-netware.de/redirect.php?swg","direct Netware Group"," - All rights reserved");

if ($direct_settings['a'] == "index") { $direct_settings['a'] = "status"; }
//j// BOS
switch ($direct_settings['a'])
{
//j// $direct_settings['a'] == "entry"
case "entry":
{
	if (USE_debug_reporting) { direct_debug (1,"sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }

	$direct_cachedata['output_eid'] = (isset ($direct_settings['dsd']['deid']) ? $direct_settings['dsd']['deid'] : "");

	$direct_cachedata['page_this'] = "m=cp;s=daemon+index;a=entry;dsd=deid+".$direct_cachedata['output_eid'];
	$direct_cachedata['page_backlink'] = "m=cp;s=daemon+index;a=status";
	$direct_cachedata['page_homelink'] = "m=cp;a=services";

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
		$direct_globals['output']->relatedManager ("cp_daemon_index_entry_".$direct_cachedata['output_eid'],"pre_module_service_action");
		$direct_globals['kernel']->serviceHttps ($direct_settings['cp_https_daemon_manage'],$direct_cachedata['page_this']);
		$direct_globals['basic_functions']->requireClass ('dNG\sWG\directFormtags');

		direct_class_init ("formtags");
		$direct_globals['output']->optionsInsert (1,"servicemenu","m=cp;s=daemon+entry;a=command;dsd=deid+{$direct_settings['dsd']['deid']}++daction+restart",(direct_local_get ("cp_daemon_entry_restart")),$direct_settings['serviceicon_cp_daemon_entry_restart'],"url0");
		$direct_globals['output']->optionsInsert (2,"servicemenu","m=cp;s=daemon+index;a=status",(direct_local_get ("core_back")),$direct_settings['serviceicon_default_back'],"url0");

		$g_entry_array = $g_daemon_object->request ("de.direct_netware.psd.plugins.queue.getEntry",(array ($direct_cachedata['output_eid'])));

		if (is_array ($g_entry_array))
		{
			$g_id_safe = strtolower (preg_replace ("#\W#","_",$g_entry_array['id']));
			$direct_cachedata['output_entry'] = array ("id" => "swgpyhandler".$g_id_safe,"oid" => $g_entry_array['id'],"pageurl" => "m=cp;s=daemon+entry;a=view;dsd=deid+".$g_entry_array['id']);

			$g_parsed_name = direct_string_id_translation ("cp_daemon",(md5 ($g_entry_array['name'])));
			$direct_cachedata['output_entry']['name'] = ((is_bool ($g_parsed_name)) ? direct_html_encode_special ($g_entry_array['name']) : $g_parsed_name);
			if (strlen ($g_entry_array['identifier'])) { $direct_cachedata['output_entry']['identifier'] = direct_html_encode_special ($g_entry_array['identifier']); }

			if (strlen ($g_entry_array['data']))
			{
				$g_data_array = direct_evars_get ($g_entry_array['data']);

				if ((is_array ($g_data_array))&&(!empty ($g_data_array))) { $direct_cachedata['output_entry']['data'] = $direct_globals['formtags']->decode ($g_daemon_object->parseEvarsData ($g_data_array)); }
				else { $direct_cachedata['output_entry']['data'] = direct_local_get ("core_daemon_unknown_response","text"); }
			}

			if ($g_entry_array['status'] == "unknown") { $direct_cachedata['output_entry']['status'] = direct_local_get ("core_unknown"); }
			else { $direct_cachedata['output_entry']['status'] = direct_local_get ("cp_daemon_status_".$g_entry_array['status']); }

			$direct_cachedata['output_entry']['time_started'] = ($g_entry_array['time_started'] ? $g_entry_array['time_started'] : 0);
			$direct_cachedata['output_entry']['time_update'] = ($g_entry_array['time_update'] ? $g_entry_array['time_update'] : 0);
			$direct_cachedata['output_entry']['time_updated'] = ($g_entry_array['time_updated'] ? $g_entry_array['time_updated'] : 0);

			$direct_globals['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
			$direct_globals['output']->relatedManager ("cp_daemon_index_entry_".$direct_cachedata['output_eid'],"post_module_service_action");
			$direct_globals['output']->oset ("cp/daemon","entry");
			$direct_globals['output']->outputSend (direct_local_get ("cp_daemon_status"));
		}
		else { $direct_globals['output']->outputSendError ("standard","core_daemon_eid_invalid","","sWG/#echo(__FILEPATH__)# _a=entry_ (#echo(__LINE__)#)"); }
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
//j// $direct_settings['a'] == "status"
case "status":
{
	if (USE_debug_reporting) { direct_debug (1,"sWG/#echo(__FILEPATH__)# _a=status_ (#echo(__LINE__)#)"); }

	$direct_cachedata['output_page'] = (isset ($direct_settings['dsd']['page']) ? $direct_settings['dsd']['page'] : 1);

	$direct_cachedata['page_this'] = "m=cp;s=daemon+index;a=status;dsd=page+".$direct_cachedata['output_page'];
	$direct_cachedata['page_backlink'] = "m=cp;a=services";
	$direct_cachedata['page_homelink'] = "m=cp;a=services";

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
		$direct_globals['output']->relatedManager ("cp_daemon_index_status","pre_module_service_action");
		$direct_globals['kernel']->serviceHttps ($direct_settings['cp_https_daemon_manage'],$direct_cachedata['page_this']);

		$direct_globals['output']->optionsInsert (1,"servicemenu","m=cp;s=daemon+entry;a=command;dsd=drid+de.direct_netware.psd.plugins.reload",(direct_local_get ("cp_daemon_plugins_reload")),$direct_settings['serviceicon_cp_daemon_plugins_reload'],"url0");
		$direct_globals['output']->optionsInsert (2,"servicemenu",$direct_cachedata['page_backlink'],(direct_local_get ("core_back")),$direct_settings['serviceicon_default_back'],"url0");

		$g_uptime = $g_daemon_object->request ("de.direct_netware.psd.status.getUptime");
		$direct_cachedata['output_uptime'] = (($g_uptime != NULL) ? $g_uptime : 0);

		$direct_cachedata['output_queue'] = array ();
		$g_entry_count = $g_daemon_object->request ("de.direct_netware.psd.plugins.queue.getEntryCount");

		if ($g_entry_count)
		{
			$direct_cachedata['output_pages'] = ceil ($g_entry_count / $direct_settings['cp_daemon_entries_per_page']);
			if ($direct_cachedata['output_pages'] < 1) { $direct_cachedata['output_pages'] = 1; }

			if ($direct_cachedata['output_page'] == "last") { $direct_cachedata['output_page'] = $direct_cachedata['output_pages']; }
			elseif ((!$direct_cachedata['output_page'])||($direct_cachedata['output_page'] < 1)) { $direct_cachedata['output_page'] = 1; }

			$g_offset = (($direct_cachedata['output_page'] - 1) * $direct_settings['cp_daemon_entries_per_page']);
			$g_entries_array = $g_daemon_object->request ("de.direct_netware.psd.plugins.queue.getEntries",(array ($g_offset,$direct_settings['cp_daemon_entries_per_page'])));
		}
		else { $g_entries_array = NULL; }

		$direct_cachedata['output_page_url'] = "m=cp;s=daemon+index;a=status;dsd=";

		if (is_array ($g_entries_array))
		{
			foreach ($g_entries_array as $g_entry_array)
			{
				$g_id_safe = strtolower (preg_replace ("#\W#","_",$g_entry_array['id']));
				$g_parsed_array = array ("id" => "swgpyhandler".$g_id_safe,"oid" => $g_entry_array['id'],"pageurl" => "m=cp;s=daemon+index;a=entry;dsd=deid+".$g_entry_array['id'],"status" => direct_local_get ("cp_daemon_status_".$g_entry_array['status']));

				$g_parsed_name = direct_string_id_translation ("cp_daemon",(md5 ($g_entry_array['name'])));
				$g_parsed_array['name'] = ((is_bool ($g_parsed_name)) ? direct_html_encode_special ($g_entry_array['name']) : $g_parsed_name);
				if (strlen ($g_entry_array['identifier'])) { $g_parsed_array['identifier'] = direct_html_encode_special ($g_entry_array['identifier']); }
				$g_parsed_array['time_started'] = ($g_entry_array['time_started'] ? $g_entry_array['time_started'] : 0);
				$g_parsed_array['time_update'] = ($g_entry_array['time_update'] ? $g_entry_array['time_update'] : 0);
				$g_parsed_array['time_updated'] = ($g_entry_array['time_updated'] ? $g_entry_array['time_updated'] : 0);

				$direct_cachedata['output_queue'][] = $g_parsed_array;
			}
		}

		$direct_globals['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
		$direct_globals['output']->relatedManager ("cp_daemon_index_status","post_module_service_action");
		$direct_globals['output']->oset ("cp/daemon","status");
		$direct_globals['output']->outputSend (direct_local_get ("cp_daemon_status"));
	}
	else { $direct_globals['output']->outputSendError ("standard","core_daemon_unavailable","","sWG/#echo(__FILEPATH__)# _a=status_ (#echo(__LINE__)#)"); }
	//j// EOA
	}
	else { $direct_globals['output']->outputSendError ("login","core_access_denied","","sWG/#echo(__FILEPATH__)# _a=status_ (#echo(__LINE__)#)"); }
	}
	else { $direct_globals['output']->outputSendError ("standard","core_service_inactive","","sWG/#echo(__FILEPATH__)# _a=status_ (#echo(__LINE__)#)"); }
	}

	$direct_cachedata['core_service_activated'] = true;
	break 1;
}
//j// EOS
}

//j// EOF
?>