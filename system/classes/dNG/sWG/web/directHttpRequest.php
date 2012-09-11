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
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/
/*#ifdef(PHP5n) */

namespace dNG\sWG\web;
/* #\n*/
/*#use(direct_use) */
use dNG\sWG\directWebFunctions;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("CLASS_directHttpRequest"))
{
/**
* This support is a basic one. You can use fopen as well as GET and POST
* commands (depending on the "socket" constant.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/
class directHttpRequest extends directWebFunctions
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

/**
	* Constructor (PHP5) __construct (directHttpRequest)
	*
	* @since v0.1.00
*/
	public function __construct ()
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->__construct (directHttpRequest)- (#echo(__LINE__)#)"); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['defineRecipient'] = true;
		$this->functions['execute'] = true;

/* -------------------------------------------------------------------------
Set up the caching variables
------------------------------------------------------------------------- */

		$this->path = NULL;
		$this->port = NULL;
		$this->server = NULL;
	}

/**
	* This method defines the recipient server for a message.
	*
	* @param  string $f_server Server name or IP address of target
	* @param  integer $f_port The target port
	* @param  string $f_path The absolute address to the remote resource
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	public function defineRecipient ($f_server,$f_port = 80,$f_path = "")
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->defineRecipient ($f_server,$f_port,$f_path)- (#echo(__LINE__)#)"); }

		if (is_string ($f_server))
		{
			$this->path = $f_path;
			$this->port = $f_port;
			$this->server = $f_server;

			return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->defineRecipient ()- (#echo(__LINE__)#)",:#*/true/*#ifdef(DEBUG):,true):#*/;
		}
		else { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->defineRecipient ()- (#echo(__LINE__)#)",:#*/false/*#ifdef(DEBUG):,true):#*/; }
	}

/**
	* This method defines the recipient for a message.
	*
	* @param  string $f_request_type The request type ("get" or "post").
	* @param  mixed $f_params String or array with query parameters.
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	public function execute ($f_request_type,$f_params)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->execute ($f_request_type,+f_params)- (#echo(__LINE__)#)"); }
		$f_return = false;

		if (isset ($this->server,$this->port,$this->path))
		{
			if ($f_request_type == "get") { $f_return = $this->httpGet ($this->server,$this->port,$this->path,$f_params); }
			elseif ($f_request_type == "post") { $f_return = $this->httpPost ($this->server,$this->port,$this->path,$f_params); }

			$f_result_code = $this->getResultCode ();
			if ((is_string ($f_return))&&((200 != $g_request_result_code)&&(203 != $g_request_result_code))) { $f_return = false; }
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->execute ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}
}

/* -------------------------------------------------------------------------
Set the constant for this class
------------------------------------------------------------------------- */

define ("CLASS_directHttpRequest",true);
}

//j// EOF
?>