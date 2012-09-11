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
* pyHelper is a sWG helper application written in Python.
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
/*#ifdef(PHP5n) */

namespace dNG\sWG\web;
/* #\n*/
/*#use(direct_use) */
use dNG\sWG\directTcpFunctions;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("directPyHelper"))
{
/**
* directPyHelper communicates with a Python daemon.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/
class directPyHelper extends directTcpFunctions
{
/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (direct_pyHelper)
	*
	* @since v0.1.00
*/
	public function __construct ()
	{
		global $direct_globals,$direct_settings;
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -pyHelper->__construct (directPyHelper)- (#echo(__LINE__)#)"); }

		if (!isset ($direct_globals['@names']['formtags'])) { $direct_globals['basic_functions']->includeClass ('dNG\sWG\directFormtags',2); }
		if (!isset ($direct_globals['formtags'])) { direct_class_init ("formtags"); }
		if (!isset ($direct_globals['@names']['web_http_jsonrpc'])) { $direct_globals['basic_functions']->includeClass ('dNG\sWG\web\directHttpJsonrpc',2); }
		if (!isset ($direct_globals['web_http_jsonrpc'])) { direct_class_init ("web_http_jsonrpc"); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['getMessage'] = true;
		$this->functions['parseEvarsData'] = isset ($direct_globals['formtags']);
		$this->functions['request'] = true;
		$this->functions['writeMessage'] = true;

/* -------------------------------------------------------------------------
Create the session 
------------------------------------------------------------------------- */

		if ((isset ($direct_globals['web_http_jsonrpc']))&&($this->connect ($direct_settings['swg_pyhelper_address'],0)))
		{
			@stream_set_blocking ($this->data,1);
			@stream_set_timeout ($this->data,$direct_settings['swg_tcp_timeout']);
		}
	}

/**
	* Closes an active session.
	*
	* @return boolean True on success
	* @since  v0.1.00
*/
	public function disconnect ()
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -tcp_functions_class->disconnect ()- (#echo(__LINE__)#)"); }

		$this->writeMessage ("");
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -tcp_functions_class->disconnect ()- (#echo(__LINE__)#)",(:#*/parent::disconnect ()/*#ifdef(DEBUG):),true):#*/;
	}

/**
	* Receives a message from the helper application.
	*
	* @return string Message on success; Empty string otherwise.
	* @since  v0.1.00
*/
	public function getMessage ()
	{
		global $direct_cachedata,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -pyHelper->getMessage ()- (#echo(__LINE__)#)"); }

		$f_return = "";

		if (is_resource ($this->data))
		{
			$f_msg = fread ($this->data,256);
			$f_newline_position = strpos ($f_msg,"\n");

			if ($f_newline_position > 0)
			{
				$f_msg_size = substr ($f_msg,0,$f_newline_position);

				if ($f_msg_size > 256)
				{
					$f_return = substr ($f_msg,($f_newline_position + 1));
					$f_msg_size -= (255 - $f_newline_position);
					if (function_exists ("stream_select")) { $f_stream_check = array ($this->data); }
					$f_stream_ignored = NULL;
					$f_timeout_time = (time () + $direct_settings['swg_tcp_timeout']);

					do
					{
						if (isset ($f_stream_check)) { stream_select ($f_stream_check,$f_stream_ignored,$f_stream_ignored,$direct_settings['swg_tcp_timeout']); }
						$f_part_size = (($f_msg_size > 4096) ? 4096 : $f_msg_size);
						$f_return .= fread ($this->data,$f_part_size);
						$f_msg_size -= $f_part_size;
					}
					while (($f_msg_size > 0)&&(!feof ($this->data))&&($f_timeout_time > (time ())));
				}
				elseif (($f_newline_position + 1 + $f_msg_size) == (strlen ($f_msg))) { $f_return = substr ($f_msg,($f_newline_position + 1)); }
			}
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -pyHelper->getMessage ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Requests the helper application to do something.
	*
	* @return (mixed) Result data; NULL on error
	* @since  v0.1.00
*/
	public function parseEvarsData ($f_data)
	{
		global $direct_cachedata,$direct_globals,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -pyHelper->parseEvarsData (+f_data)- (#echo(__LINE__)#)"); }

		$f_return = "";

		if (is_array ($f_data))
		{
			ksort ($f_data);

			foreach ($f_data as $f_key => $f_value)
			{
				if ($f_return) { $f_return .= "[newline]"; }

				if (is_array ($f_value)) { $f_return .= "[font:bold]$f_key:[/font][newline][contentform:textindent:10]".($this->parseEvarsData ($f_value))."[/contentform]"; }
				else { $f_return .= "[font:bold]$f_key:[/font][sourcecode]".(str_replace ("\n","[newline]",$f_value))."[/sourcecode]"; }
			}
		}
		else { $f_return = "[sourcecode]".(str_replace ("\n","[newline]",$f_data))."[/sourcecode]"; }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -pyHelper->parseEvarsData ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Requests the helper application to do something.
	*
	* @return (mixed) Result data; NULL on error
	* @since  v0.1.00
*/
	public function request ($f_request,$f_data,$f_response_expected = True)
	{
		global $direct_cachedata,$direct_globals,$direct_local,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -pyHelper->request ()- (#echo(__LINE__)#)"); }

		$f_return = NULL;

		if ($f_request == "de.direct_netware.psd.status.exit") { $f_response_expected = false; }

		$f_data = $direct_globals['web_http_jsonrpc']->parseParams ($f_data);
		$f_data = "{\"jsonrpc\":\"2.0\",\"method\":\"$f_request\"".($f_data ? ",\"params\":".$f_data : "").",\"id\":1}";

		if (($this->writeMessage ($f_data))&&($f_response_expected))
		{
			$f_return = $this->getMessage ();
			if (strlen ($f_return)) { $f_return = $direct_globals['web_http_jsonrpc']->parseJson ($f_return); }

			if ((is_array ($f_return))&&(isset ($f_return['result']))) { $f_return = $f_return['result']; }
			else { $f_return = NULL; }
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -pyHelper->request ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Sends a message to the helper application.
	*
	* @param  string $f_msg Message to be written
	* @return boolean True on success
	* @since  v0.1.00
*/
	public function writeMessage ($f_msg)
	{
		global $direct_cachedata,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -pyHelper->writeMessage ()- (#echo(__LINE__)#)"); }

		if (is_resource ($this->data))
		{
			$f_bytes_unwritten = strlen ($f_msg);
			$f_msg = $f_bytes_unwritten."\n".$f_msg;
			$f_bytes_unwritten = strlen ($f_msg);

			$f_return = true;
			$f_timeout_time = (time () + $direct_settings['swg_tcp_timeout']);

			while (($f_bytes_unwritten > 0)&&($f_return)&&($f_timeout_time > (time ())))
			{
				$f_sent = fwrite ($this->data,$f_msg,$f_bytes_unwritten);

				if ($f_sent === false) { $f_return = false; }
				else { $f_bytes_unwritten -= $f_sent; }
			}

			if ($f_bytes_unwritten > 0) { $f_return = false; }
		}
		else { $f_return = false; }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -pyHelper->writeMessage ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directPyHelper",true);

//j// Script specific commands

global $direct_settings;
if (!isset ($direct_settings['swg_pyhelper_address'])) { $direct_settings['swg_pyhelper_address'] = "unix:///tmp/de.direct-netware.psd.socket"; }
if (!isset ($direct_settings['swg_tcp_timeout'])) { $direct_settings['swg_tcp_timeout'] = $direct_settings['timeout_core']; }
}

//j// EOF
?>