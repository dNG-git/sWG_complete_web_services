<?php
//j// BOF

/*n// NOTE
----------------------------------------------------------------------------
secured WebGine
net-based application engine
----------------------------------------------------------------------------
(C) direct Netware Group - All rights reserved
http://www.direct-netware.de/redirect.php?swg

This work is distributed under the W3C (R) Software License, but without any
warranty; without even the implied warranty of merchantability or fitness
for a particular purpose.
----------------------------------------------------------------------------
http://www.direct-netware.de/redirect.php?licenses;w3c
----------------------------------------------------------------------------
#echo(sWGwebServicesVersion)#
sWG/#echo(__FILEPATH__)#
----------------------------------------------------------------------------
NOTE_END //n*/
/**
* Provides functions to handle HTTP AUTH BASIC and DIGEST transparently.
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
* @uses       direct_product_iversion
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;w3c
*             W3C (R) Software License
*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Basic configuration

/* -------------------------------------------------------------------------
Direct calls will be honored with an "exit ()"
------------------------------------------------------------------------- */

if (!defined ("direct_product_iversion")) { exit (); }

//j// Functions and classes

//f// direct_web_services_http_auth_check ()
/**
* Check for basic and digest sessions.
*
* @param  direct_datalinker &$f_object DataLinker object
* @uses   direct_basic_functions::dclass_include()
* @uses   direct_basic_functions::dclass_settings_get()
* @uses   direct_debug()
* @uses   direct_discuss_board::dclass_get()
* @uses   direct_discuss_post::dclass_get()
* @uses   direct_discuss_topic::dclass_get()
* @uses   USE_debug_reporting
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_services_http_auth_check ()
{
	global $direct_cachedata,$direct_classes,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_services_http_auth_check ()- (#echo(__LINE__)#)"); }

	$f_return = false;

	if (isset ($_SERVER['PHP_AUTH_DIGEST']))
	{
		
	}

	if ($direct_settings['user']['type'] == "gt")
	{
		if ($direct_classes['basic_functions']->settings_get ($direct_settings['path_data']."/settings/swg_account.php"))
		{
			if (isset ($_SERVER['PHP_AUTH_DIGEST']))
			{
				
			}
			elseif (isset ($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']))
			{
				$f_username = $direct_classes['basic_functions']->inputfilter_basic ($_SERVER['PHP_AUTH_USER']);
				$f_password = $direct_classes['basic_functions']->tmd5 ($_SERVER['PHP_AUTH_PW'],$direct_settings['account_password_bytemix']);

				$f_user_array = $direct_classes['kernel']->v_user_get ("",$f_username,true);

				if (($f_user_array)&&($f_user_array['ddbusers_password'] == $f_password))
				{
					$direct_settings['user'] = array ("id" => $f_user_array['ddbusers_id'],"name" => $f_user_array['ddbusers_name'],"name_html" => (direct_html_encode_special ($f_user_array['ddbusers_name'])),"type" => $f_user_array['ddbusers_type'],"timezone" => $f_user_array['ddbusers_timezone']);
					$f_return = true;
				}
			}
		}
	}
	elseif ((isset ($_SERVER['PHP_AUTH_DIGEST']))||(isset ($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])))
	{
		$f_user_array = $direct_classes['kernel']->v_user_get ($direct_settings['user']['id']);
		$f_return = true;
	}

	$f_return = ((($f_return)&&($f_user_array)&&($f_user_array['ddbusers_type'] != "ex")&&(!$f_user_array['ddbusers_banned'])&&(!$f_user_array['ddbusers_deleted'])&&(!$f_user_array['ddbusers_locked'])) ? true : false);
	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_web_services_http_auth_check ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
}

//f// direct_web_services_http_auth_request_basic ($f_realm = NULL)
/**
* Check for basic and digest sessions.
*
* @param  direct_datalinker &$f_object DataLinker object
* @uses   direct_basic_functions::dclass_include()
* @uses   direct_basic_functions::dclass_settings_get()
* @uses   direct_debug()
* @uses   direct_discuss_board::dclass_get()
* @uses   direct_discuss_post::dclass_get()
* @uses   direct_discuss_topic::dclass_get()
* @uses   USE_debug_reporting
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_services_http_auth_request_basic ($f_realm = NULL)
{
	global $direct_classes,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_services_http_auth_request_basic (+f_realm)- (#echo(__LINE__)#)"); }

	if (!isset ($f_realm)) { $f_realm = $direct_settings['swg_title_txt']." ({$direct_settings['home_url']})"; }
	$f_realm = str_replace ('"','\"',$f_realm);

	header ("HTTP/1.1 401 Unauthorized");
	header ("WWW-Authenticate: Basic realm=\"$f_realm\"");
	$direct_classes['error_functions']->error_page ("login","core_access_denied","sWG/#echo(__FILEPATH__)# -direct_web_services_http_auth_request_basic ()- (#echo(__LINE__)#)");
}

//f// direct_web_services_http_auth_request_digest ($f_realm = NULL)
/**
* Check for basic and digest sessions.
*
* @param  direct_datalinker &$f_object DataLinker object
* @uses   direct_basic_functions::dclass_include()
* @uses   direct_basic_functions::dclass_settings_get()
* @uses   direct_debug()
* @uses   direct_discuss_board::dclass_get()
* @uses   direct_discuss_post::dclass_get()
* @uses   direct_discuss_topic::dclass_get()
* @uses   USE_debug_reporting
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_services_http_auth_request_digest ($f_realm = NULL)
{
	global $direct_classes,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_services_http_auth_request_digest (+f_realm)- (#echo(__LINE__)#)"); }

	if (!isset ($f_realm)) { $f_realm = $direct_settings['swg_title_txt']." ({$direct_settings['home_url']})"; }
	$f_realm = str_replace ('"','\"',$f_realm);

	header ("HTTP/1.1 401 Unauthorized");
	header ("WWW-Authenticate: Basic realm=\"$f_realm\"");
	$direct_classes['error_functions']->error_page ("login","core_access_denied","sWG/#echo(__FILEPATH__)# -direct_web_services_http_auth_request_digest ()- (#echo(__LINE__)#)");
}

//j// Script specific commands

if (!isset ($direct_settings['account_password_bytemix'])) { $direct_settings['account_password_bytemix'] = ($direct_settings['swg_id'] ^ (strrev ($direct_settings['swg_id']))); }

//j// EOF
?>