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
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
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

/**
* Check for basic and digest sessions.
*
* @param  direct_datalinker &$f_object DataLinker object
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_http_auth_check ()
{
	global $direct_cachedata,$direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_check ()- (#echo(__LINE__)#)"); }

	$f_return = false;

	if ($direct_settings['user']['type'] == "gt")
	{
		if ($direct_globals['basic_functions']->settingsGet ($direct_settings['path_data']."/settings/swg_account.php"))
		{
			$f_username = $direct_globals['basic_functions']->inputfilterBasic ($direct_globals['input']->userGet ());

			if (($direct_globals['input']->authGet () == "digest")&&($direct_globals['input']->uuidGet () != NULL)&&(preg_match ("#response=\"(.+?)\"#",$_SERVER['PHP_AUTH_DIGEST'],$f_result_array)))
			{
				$direct_globals['kernel']->vUuidInit ($direct_settings['uuid']);

				$f_digest_response = $f_result_array[1];
				$f_uuid_data = $direct_globals['kernel']->vUuidGet ("s");
				$f_uuid_array = ($f_uuid_data ? direct_evars_get ($f_uuid_data) : array ());

				$f_continue_check = ((($f_uuid_array)&&(isset ($f_uuid_array['web_services_http_auth_nonce']))) ? true : false);
				$f_digest_cnonce = ((preg_match ("#cnonce=\"(.+?)\"#",$_SERVER['PHP_AUTH_DIGEST'],$f_result_array)) ? $f_result_array[1] : "");
				$f_digest_cnc = ((preg_match ("#nc=(\w+)#",$_SERVER['PHP_AUTH_DIGEST'],$f_result_array)) ? $f_result_array[1] : "");
				$f_digest_uri = ((preg_match ("#uri=\"(.+?)\"#",$_SERVER['PHP_AUTH_DIGEST'],$f_result_array)) ? $f_result_array[1] : "");
				$f_request_method = ((isset ($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : "unsupported");

				if ($f_continue_check)
				{
					$f_passphrase = "";

					if ((isset ($f_uuid_array['userid'],$f_uuid_array['web_services_http_auth_passphrase']))&&($f_uuid_array['userid'])&&($f_uuid_array['web_services_http_auth_passphrase'])) { $f_passphrase = $f_uuid_array['web_services_http_auth_passphrase']; }
					elseif (preg_match ("#username=\"(.+?)\"#",$_SERVER['PHP_AUTH_DIGEST'],$f_result_array))
					{
						$f_username = $direct_globals['basic_functions']->inputfilterBasic ($f_result_array[1]);
						$f_user_array = $direct_globals['kernel']->vUserGet ("",$f_username,true);

						if (($f_user_array)&&(isset ($f_user_array['ddbusers_auth_digest_passphrase'])))
						{
							$f_uuid_array['userid'] = $f_user_array['ddbusers_id'];
							$f_passphrase = $f_user_array['ddbusers_auth_digest_passphrase'];
						}
					}

					if (!isset ($f_uuid_array['web_services_http_auth_cnonces'])) { $f_uuid_array['web_services_http_auth_cnonces'] = array (); }
					elseif (is_string ($f_uuid_array['web_services_http_auth_cnonces'])) { $f_uuid_array['web_services_http_auth_cnonces'] = array ($f_uuid_array['web_services_http_auth_cnonces']); }

					$f_continue_check = ($f_passphrase ? direct_web_http_auth_digest_check ($f_passphrase,$f_uuid_array['web_services_http_auth_cnonces'],$f_uuid_array['web_services_http_auth_nonce'],$f_digest_cnc,$f_digest_cnonce,$f_request_method,$f_digest_uri,$f_digest_response) : false);
				}

				if (($f_continue_check)&&($direct_globals['kernel']->vUserCheck ($f_uuid_array['userid'])))
				{
					$f_uuid_array['web_services_http_auth_passphrase'] = $f_passphrase;
					$f_uuid_data = direct_evars_write ($f_uuid_array);

					$direct_globals['kernel']->vUuidWrite ($f_uuid_data);
					$direct_globals['kernel']->vUuidCookieSave ();

					$f_user_array = $direct_globals['kernel']->vUserGet ($f_uuid_array['userid']);

					if (($f_user_array)&&(!$f_user_array['ddbusers_banned'])&&(!$f_user_array['ddbusers_deleted'])&&(!$f_user_array['ddbusers_locked'])&&($f_user_array['ddbusers_type'] != "ex")&&($f_user_array['ddbusers_password'] == $f_password))
					{
						if ((!isset ($GLOBALS['i_lang']))&&(file_exists ($direct_settings['path_lang']."/swg_core.{$f_user_array['ddbusers_lang']}.php")))
						{
							$direct_settings['lang'] = $f_user_array['ddbusers_lang'];
/* -------------------------------------------------------------------------
Reloading language file (if required)
------------------------------------------------------------------------- */

							direct_local_integration ("core","en",true);
						}

						$direct_settings['theme'] = $f_user_array['ddbusers_theme'];
						$direct_cachedata['kernel_lastvisit'] = $f_user_array['ddbusers_lastvisit_time'];

						$direct_settings['user'] = array ("id" => $f_uuid_array['userid'],"type" => $f_user_array['ddbusers_type'],"timezone" => $f_user_array['ddbusers_timezone']);
						$direct_globals['input']->userSet ($f_uuid_array['username']);

						if (isset ($f_uuid_array['groups'])) { $direct_settings['user']['groups'] = $f_uuid_array['groups']; }
						if (isset ($f_uuid_array['rights'])) { $direct_settings['user']['rights'] = $f_uuid_array['rights']; }

						$f_return = $direct_globals['kernel']->vUserWriteKernel ($direct_settings['user']['id']);
					}
				}
			}
			elseif (strlen ($f_username))
			{
				$f_user_array = $direct_globals['kernel']->vUserGet ("",$f_username,true);

				if (($f_user_array)&&(!$f_user_array['ddbusers_banned'])&&(!$f_user_array['ddbusers_deleted'])&&(!$f_user_array['ddbusers_locked'])&&($f_user_array['ddbusers_type'] != "ex")&&($direct_globals['kernel']->vUserCheckPassword ($f_user_array['ddbusers_id'],($direct_globals['input']->passGet ()))))
				{
					$direct_settings['user'] = array ("id" => $f_user_array['ddbusers_id'],"type" => $f_user_array['ddbusers_type'],"timezone" => $f_user_array['ddbusers_timezone']);
					$direct_globals['input']->userSet ($f_user_array['ddbusers_name']);

					$f_return = true;
				}
			}
		}
	}
	elseif (strlen ($direct_globals['input']->userGet ()))
	{
		$f_user_array = ($direct_settings['user']['id'] ? $direct_globals['kernel']->vUserGet ($direct_settings['user']['id']) : NULL);
		$f_return = ($f_user_array ? true : false);
	}

	$f_return = ((($f_return)&&($f_user_array)&&($f_user_array['ddbusers_type'] != "ex")&&(!$f_user_array['ddbusers_banned'])&&(!$f_user_array['ddbusers_deleted'])&&(!$f_user_array['ddbusers_locked'])) ? true : false);
	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_check ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
}

/**
* Check for a defined password.
*
* @param  direct_datalinker &$f_object DataLinker object
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_http_auth_pw_basic_check ($f_auth_password,$f_auth_username = NULL)
{
	global $direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_pw_basic_check ()- (#echo(__LINE__)#)"); }

	$f_return = false;

	if ($direct_globals['input']->authGet () == "basic")
	{
		$f_username = $direct_globals['basic_functions']->inputfilterBasic ($direct_globals['input']->userGet ());
		$f_password = $direct_globals['basic_functions']->inputfilterBasic ($direct_globals['input']->passGet ());

		if (!isset ($f_auth_username)) { $f_auth_username = $direct_settings['swg_id']; }
		if (($f_auth_username == $f_username)&&($f_auth_password == $f_password)) { $f_return = true; }
	}

	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_pw_basic_check ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
}

/**
* Check for a valid digest response.
*
* @param  direct_datalinker &$f_object DataLinker object
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_http_auth_digest_check ($f_passphrase,&$f_cnonces_array,$f_nonce,$f_cnc,$f_cnonce,$f_request_method,$f_uri,$f_response)
{
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_digest_check ()- (#echo(__LINE__)#)"); }
	$f_return = ((md5 ($f_passphrase.":".$f_nonce.":".$f_cnc.":".$f_cnonce.":auth:".(md5 ($f_request_method.":".$f_uri))) == $f_response) ? true : false);

	if ($f_return)
	{
		$f_return = ((in_array ($f_cnonce,$f_cnonces_array)) ? false: true);

		if ($f_return)
		{
			if (count ($f_cnonces_array) > 100) { array_shift ($f_cnonces_array); }
			$f_cnonces_array[] = $f_cnonce;
		}
	}

	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_digest_check ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
}

/**
* Check for basic and digest sessions.
*
* @param  direct_datalinker &$f_object DataLinker object
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_http_auth_request_basic ($f_realm = NULL)
{
	global $direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_request_basic (+f_realm)- (#echo(__LINE__)#)"); }

	if (!isset ($f_realm)) { $f_realm = $direct_settings['swg_title_txt']." ({$direct_settings['home_url']})"; }
	$f_realm = str_replace ('"','\"',$f_realm);

	$direct_globals['output']->outputHeader ("HTTP/1.1","HTTP/1.1 401 Unauthorized",true);
	$direct_globals['output']->outputHeader ("WWW-Authenticate","Basic realm=\"$f_realm\"");
	$direct_globals['output']->outputSendError ("login","core_access_denied","","sWG/#echo(__FILEPATH__)# -direct_web_http_auth_request_basic ()- (#echo(__LINE__)#)");
}

/**
* Check for basic and digest sessions.
*
* @param  direct_datalinker &$f_object DataLinker object
* @return mixed Object on success; false on error
* @since  v0.1.00
*/
function direct_web_http_auth_request_digest ($f_realm = NULL,$f_basic_fallback = false)
{
	global $direct_globals,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_web_http_auth_request_digest (+f_realm,+f_basic_fallback)- (#echo(__LINE__)#)"); }

	if (!isset ($f_realm)) { $f_realm = $direct_settings['swg_title_txt']." ({$direct_settings['home_url']})"; }
	$f_realm = str_replace ('"','\"',$f_realm);
	$f_nonce = md5 (uniqid ($direct_settings['uuid']));

	$f_uuid_string = "<evars><userid /><web_services_http_auth_nonce value=\"$f_nonce\" /></evars>";
	$direct_globals['kernel']->vUuidWrite ($f_uuid_string);
	$direct_globals['kernel']->vUuidCookieSave ();

	$direct_globals['output']->outputHeader ("HTTP/1.1","HTTP/1.1 401 Unauthorized",true);
	$direct_globals['output']->outputHeader ("WWW-Authenticate","Digest realm=\"$f_realm\", domain=\"{$direct_settings['home_url']}\", nonce=\"$f_nonce\", opaque=\"{$direct_settings['uuid']}\", stale=false, algorithm=MD5, qop=auth");
	if ($f_basic_fallback) { $direct_globals['output']->outputHeader ("WWW-Authenticate","Basic realm=\"$f_realm (Basic)\"",false,true); }

	$direct_globals['output']->outputSendError ("login","core_access_denied","","sWG/#echo(__FILEPATH__)# -direct_web_http_auth_request_digest ()- (#echo(__LINE__)#)");
}

//j// Script specific commands

if (!isset ($direct_settings['account_password_bytemix'])) { $direct_settings['account_password_bytemix'] = ($direct_settings['swg_id'] ^ (strrev ($direct_settings['swg_id']))); }

//j// EOF
?>