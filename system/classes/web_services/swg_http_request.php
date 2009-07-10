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
* This is a basic class for providing a interface for HTTP web services.
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

//j// Functions and classes

/* -------------------------------------------------------------------------
Testing for required classes
------------------------------------------------------------------------- */

$g_continue_check = true;
if (defined ("CLASS_direct_web_http_request")) { $g_continue_check = false; }
if (($g_continue_check)&&(!defined ("CLASS_direct_web_functions"))) { $g_continue_check = $direct_classes['basic_functions']->include_file ($direct_settings['path_system']."/classes/swg_web_functions.php",1); }

if ($g_continue_check)
{
//c// direct_web_service
/**
* This support is a basic one. You can use fopen as well as GET and POST
* commands (depending on the "socket" constant.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;w3c
*             W3C (R) Software License
*/
class direct_web_http_request extends direct_web_functions
{
/**
	* @var string $path The absolute address to the remote resource
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $path;
/**
	* @var integer $port The target port
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $port;
/**
	* @var string $server Server name or IP address of target
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $server;

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

	//f// direct_web_http_request->__construct ()
/**
	* Constructor (PHP5) __construct (direct_web_http_request)
	*
	* @uses  USE_debug_reporting
	* @since v0.1.00
*/
	public function __construct ()
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -http_request->__construct (direct_web_http_request)- (#echo(__LINE__)#)"); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['define_recipient'] = true;
		$this->functions['execute'] = true;

/* -------------------------------------------------------------------------
Set up the caching variables
------------------------------------------------------------------------- */

		$this->path = NULL;
		$this->port = NULL;
		$this->server = NULL;
	}

	//f// direct_web_http_request->define_recipient ($f_server,$f_port = 80,$f_path = "")
/**
	* This method defines the recipient server for a message.
	*
	* @param  string $f_server Server name or IP address of target
	* @param  integer $f_port The target port
	* @param  string $f_path The absolute address to the remote resource
	* @uses   USE_debug_reporting
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	public function define_recipient ($f_server,$f_port = 80,$f_path = "")
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -http_request->define_recipient ($f_server,$f_port,$f_path)- (#echo(__LINE__)#)"); }

		if (is_string ($f_server))
		{
			$this->path = $f_path;
			$this->port = $f_port;
			$this->server = $f_server;

			return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -http_request->define_recipient ()- (#echo(__LINE__)#)",:#*/true/*#ifdef(DEBUG):,true):#*/;
		}
		else { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -http_request->define_recipient ()- (#echo(__LINE__)#)",:#*/false/*#ifdef(DEBUG):,true):#*/; }
	}

	//f// direct_web_http_request->execute ($f_request_type,$f_params)
/**
	* This method defines the recipient for a message.
	*
	* @param  string $f_request_type The request type ("get" or "post").
	* @param  mixed $f_params String or array with query parameters.
	* @uses   USE_debug_reporting
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	public function execute ($f_request_type,$f_params)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -http_request->execute ($f_request_type,+f_params)- (#echo(__LINE__)#)"); }
		$f_return = false;

		if (isset ($this->server,$this->port,$this->path))
		{
			if ($f_request_type == "get") { $f_return = $this->http_get ($this->server,$this->port,$this->path,$f_params); }
			elseif ($f_request_type == "post") { $f_return = $this->http_post ($this->server,$this->port,$this->path,$f_params); }

			$f_result_code = $this->get_result_code ();
			if ((is_string ($f_return))&&((200 != $g_request_result_code)&&(203 != $g_request_result_code))) { $f_return = false; }
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -http_request->execute ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}
}

/* -------------------------------------------------------------------------
Set the constant for this class
------------------------------------------------------------------------- */

define ("CLASS_direct_web_http_request",true);
}

//j// EOF
?>