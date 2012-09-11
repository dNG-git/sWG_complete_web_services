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
* osets/default/swg_cp_daemon.php
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

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

/**
* direct_output_oset_cp_daemon_command ()
*
* @return string Valid XHTML code
* @since  v0.1.01
*/
function direct_output_oset_cp_daemon_command ()
{
	global $direct_cachedata,$direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_output_oset_cp_daemon_command ()- (#echo(__LINE__)#)"); }

	$direct_settings['theme_output_page_title'] = (direct_local_get ("core_done").": ".$direct_cachedata['output_command']);
	return "<div class='pagecontent'>{$direct_cachedata['output_return_value']}</div>";
}

/**
* direct_output_oset_cp_daemon_entry ()
*
* @return string Valid XHTML code
* @since  v0.1.01
*/
function direct_output_oset_cp_daemon_entry ()
{
	global $direct_cachedata,$direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_output_oset_cp_daemon_entry ()- (#echo(__LINE__)#)"); }

	$direct_settings['theme_output_page_title'] = $direct_cachedata['output_entry']['name'];
	if (isset ($direct_cachedata['output_entry']['identifier'])) { $direct_settings['theme_output_page_title'] .= " - <span style='font-size:10px'>{$direct_cachedata['output_entry']['identifier']}</span>"; }

$f_return = ("<table class='pageborder1' style='width:100%;table-layout:auto'>
<thead><tr>
<td colspan='2' class='pagetitlecellbg' style='padding:$direct_settings[theme_td_padding];text-align:center'><span class='pagetitlecellcontent'>$f_title</span></td>
</tr></thead><tbody><tr>
<td class='pageextrabg' style='width:25%;padding:$direct_settings[theme_form_td_padding];text-align:right;vertical-align:middle'><span class='pageextracontent' style='font-weight:bold;font-size:10px'>".(direct_local_get ("cp_daemon_task_status")).":</span></td>
<td class='pagebg' style='width:75%;padding:$direct_settings[theme_form_td_padding];text-align:center;vertical-align:middle'><span class='pagecontent' style='font-weight:bold'>{$direct_cachedata['output_entry']['status']}</span></td>
</tr><tr>
<td class='pageextrabg' style='width:25%;padding:$direct_settings[theme_form_td_padding];text-align:right;vertical-align:middle'><span class='pageextracontent' style='font-weight:bold;font-size:10px'>".(direct_local_get ("cp_daemon_task_started")).":</span></td>
<td class='pagebg' style='width:75%;padding:$direct_settings[theme_form_td_padding];text-align:center;vertical-align:middle'><span class='pagecontent' style='font-size:10px'>".($direct_globals['basic_functions']->datetime ("shortdate&time",$direct_cachedata['output_entry']['time_started'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))."</span></td>
</tr><tr>
<td class='pageextrabg' style='width:25%;padding:$direct_settings[theme_form_td_padding];text-align:right;vertical-align:middle'><span class='pageextracontent' style='font-weight:bold;font-size:10px'>".(direct_local_get ("cp_daemon_task_update_next")).":</span></td>
<td class='pagebg' style='width:75%;padding:$direct_settings[theme_form_td_padding];text-align:center;vertical-align:middle'><span class='pagecontent' style='font-size:10px'>".($direct_globals['basic_functions']->datetime ("shortdate&time",$direct_cachedata['output_entry']['time_update'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))."</span></td>
</tr><tr>
<td class='pageextrabg' style='width:25%;padding:$direct_settings[theme_form_td_padding];text-align:right;vertical-align:middle'><span class='pageextracontent' style='font-weight:bold;font-size:10px'>".(direct_local_get ("cp_daemon_task_update_latest")).":</span></td>
<td class='pagebg' style='width:75%;padding:$direct_settings[theme_form_td_padding];text-align:center;vertical-align:middle'><span class='pagecontent' style='font-size:10px'>".($direct_globals['basic_functions']->datetime ("shortdate&time",$direct_cachedata['output_entry']['time_updated'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))."</span></td>
</tr>");

	if (isset ($direct_cachedata['output_entry']['data'])) { $f_return .= ("<tr>\n<td colspan='2' class='pagebg' style='padding:$direct_settings[theme_form_td_padding];text-align:left'><span class='pagecontent'>{$direct_cachedata['output_entry']['data']}</span></td>\n</tr>"); }
	return $f_return."</tbody>\n</table>";
}

/**
* direct_output_oset_cp_daemon_status ()
*
* @return string Valid XHTML code
* @since  v0.1.01
*/
function direct_output_oset_cp_daemon_status ()
{
	global $direct_cachedata,$direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_output_oset_cp_daemon_status ()- (#echo(__LINE__)#)"); }

	$direct_settings['theme_output_page_title'] = direct_local_get ("cp_daemon_status");
	$f_colspan = (empty ($direct_cachedata['output_queue']) ? "" : " colspan='3'");

	$f_return = "<p class='pageborder{$direct_settings['theme_css_corners']} pagebg pagecontent' style='padding:$direct_settings[theme_form_td_padding];text-align:center'><b>".(direct_local_get ("cp_daemon_uptime")).":</b> ".($direct_globals['basic_functions']->datetime ("shortdate&time",$direct_cachedata['output_uptime'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))));

	if (empty ($direct_cachedata['output_queue'])) { $f_return .= "\n<p><b>".(direct_local_get ("cp_daemon_queue_empty"))."</b></p>"; }
	else
	{
		if ($direct_cachedata['output_pages'] > 1) { $f_return .= "<br />\n<span style='font-size:10px'>".($direct_globals['output']->pagesGenerator ($direct_cachedata['output_page_url'],$direct_cachedata['output_pages'],$direct_cachedata['output_page']))."</span></p>"; }
		else { $f_return .= "</p>"; }

$f_return .= ("<table class='pagetable' style='width:100%;table-layout:auto'>
<thead><tr>
<td class='pagetitlecell' style='width:60%;padding:$direct_settings[theme_td_padding];text-align:left;vertical-align:middle'>".(direct_local_get ("cp_daemon_task"))."</td>
<td class='pagetitlecell' style='width:15%;padding:$direct_settings[theme_td_padding];text-align:center;vertical-align:middle'>".(direct_local_get ("cp_daemon_task_status"))."</td>
<td class='pagetitlecell' style='width:25%;padding:$direct_settings[theme_td_padding];text-align:center;vertical-align:middle'>".(direct_local_get ("cp_daemon_task_update_latest"))."</td>
</tr></thead><tbody>");

		foreach ($direct_cachedata['output_queue'] as $f_entry_array)
		{
$f_return .= ("<tr>
<td class='pagebg pagecontent' style='width:60%;padding:$direct_settings[theme_td_padding];text-align:left;vertical-align:middle'><b><a href=\"".(direct_linker ("url0",$f_entry_array['pageurl']))."\" target='_self'>$f_entry_array[name]</a></b>");

			$f_return .= ((isset ($f_entry_array['identifier'])) ? " - <span style='font-size:10px'>$f_entry_array[identifier]<br />\n" : "<br />\n<span style='font-size:10px'>");

$f_return .= ("<b>".(direct_local_get ("cp_daemon_task_started")).":</b> ".($direct_globals['basic_functions']->datetime ("shortdate&time",$f_entry_array['time_started'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))." - <b>".(direct_local_get ("cp_daemon_task_update_next")).":</b> ".($direct_globals['basic_functions']->datetime ("shortdate&time",$f_entry_array['time_update'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))."</span></td>
<td class='pageextrabg pageextracontent' style='width:15%;padding:$direct_settings[theme_td_padding];text-align:center;vertical-align:middle'>$f_entry_array[status]</td>
<td class='pagebg pagecontent' style='width:25%;padding:$direct_settings[theme_td_padding];text-align:center;vertical-align:middle'>".($direct_globals['basic_functions']->datetime ("shortdate&time",$f_entry_array['time_updated'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))."</td>
</tr>");
		}

		$f_return .= "</tbody>\n</table>";
		if ($direct_cachedata['output_pages'] > 1) { $f_return .= "\n<p class='pageborder{$direct_settings['theme_css_corners']} pageextrabg pageextracontent' style='text-align:center;font-size:10px'>".($direct_globals['output']->pagesGenerator ($direct_cachedata['output_page_url'],$direct_cachedata['output_pages'],$direct_cachedata['output_page']))."</p>"; }
	}

	return $f_return;
}

//j// Script specific commands

$direct_settings['theme_css_corners'] = (isset ($direct_settings['theme_css_corners_class']) ? " ".$direct_settings['theme_css_corners_class'] : " ui-corner-all");
if (!isset ($direct_settings['theme_td_padding'])) { $direct_settings['theme_td_padding'] = "5px"; }
if (!isset ($direct_settings['theme_form_td_padding'])) { $direct_settings['theme_form_td_padding'] = "3px"; }

//j// EOF
?>