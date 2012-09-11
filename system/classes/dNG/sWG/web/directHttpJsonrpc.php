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
* XML-RPC client implementation.
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
use dNG\directJson,
    dNG\sWG\web\directHttpXmlrpc;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("CLASS_directHttpJsonrpc"))
{
/**
* Provides a interface to run a single or multiple methods on a server via
* XML-RPC.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/
class directHttpJsonrpc extends directHttpXmlrpc
{
/**
	* @var string $json_mode Version mode selected
*/
	protected $json_mode;

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directHttpJsonrpc)
	*
	* @since v0.1.00
*/
	public function __construct ()
	{
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->__construct (directHttpJsonrpc)- (#echo(__LINE__)#)"); }

		if (!defined ("CLASS_directXml")) { $direct_globals['basic_functions']->includeClass ('dNG\sWG\directXml',2); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['defineCall'] = true;
		$this->functions['getBase64'] = false;
		$this->functions['getDatetime'] = false;
		$this->functions['getFault'] = true;
		$this->functions['getJsonParser'] = defined ("CLASS_directJson");
		$this->functions['jsonModeGet'] = true;
		$this->functions['jsonModeSet'] = true;
		$this->functions['parseJson'] = true;
		$this->functions['parseParams'] = true;

/* -------------------------------------------------------------------------
Set up the caching variable
------------------------------------------------------------------------- */

		$this->json_mode = "2.0";
	}

/**
	* Parse PHP data for JSON output.
	*
	* @param  array $f_data JSON data
	* @return array Array with pointers to the documents
	* @since  v0.1.00
*/
	public function get ($f_data)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->get (+f_data)- (#echo(__LINE__)#)"); }

		$f_json = $this->getJsonParser ();
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->get ()- (#echo(__LINE__)#)",(:#*/$f_json->data2json ($f_data)/*#ifdef(DEBUG):),true):#*/;
	}

/**
	* Returns the struct for the given XML-RPC fault.
	*
	* @param  array $f_data Fault data
	* @return string XML fault struct
	* @since  v0.1.00
*/
	public function getFault ($f_data)
	{
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getFault (+f_data)- (#echo(__LINE__)#)"); }
		$f_return = "{";

		$f_json = $this->getJsonParser ();
		$f_mode = (isset ($f_data[3]) ? $f_data[3] : $this->json_mode);

		switch ($f_mode)
		{
		case "1.0":
		{
			$f_return .= "\"result\":null,\"error\":{\"name\":\"JSONRPCError\",\"code\":".($f_json->data2json ($f_data[0])).",\"message\":".($f_json->data2json ($f_data[1]))."}";
			break 1;
		}
		case "1.1":
		{
			$f_return .= "\"version\":\"1.1\",\"error\":{\"name\":\"JSONRPCError\",\"code\":".($f_json->data2json ($f_data[0])).",\"message\":".($f_json->data2json ($f_data[1]))."}";
			break 1;
		}
		default: { $f_return .= "\"jsonrpc\":\"2.0\",\"error\":{\"code\":".($f_json->data2json ($f_data[0])).",\"message\":".($f_json->data2json ($f_data[1]))."}"; }
		}

		if (isset ($f_data[2])) { $f_return .= (is_bool ($f_data[2]) ? "}" : ",\"id\":{$f_data[2]}}"); }
		else { $f_return .= ",\"id\":null}"; }

		return $f_return;
	}

/**
	* Returns the active JSON parser.
	*
	* @return string XML fault struct
	* @since  v0.1.00
*/
	public function getJsonParser ()
	{
		global $direct_cachedata,$direct_settings;
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getJsonParser ()- (#echo(__LINE__)#)"); }

		if (!isset ($this->parser)) { $this->parser = new directJson (false,$direct_cachedata['core_time'],$direct_settings['timeout'],USE_debug_reporting); }
		return $this->parser;
	}

/**
	* Returns the JSON-RPC protocol version.
	*
	* @return string JSON-RPC protocol version
	* @since  v0.1.00
*/
	public function jsonModeGet () { return $this->json_mode; }

/**
	* Returns the JSON-RPC protocol version.
	*
	* @param string $f_mode JSON-RPC protocol version
	* @since v0.1.00
*/
	public function jsonModeSet ($f_mode) { $this->json_mode = $f_mode; }

/**
	* Parse JSON data.
	*
	* @param  array $f_data JSON data
	* @return array Array with pointers to the documents
	* @since  v0.1.00
*/
	public function parseJson ($f_data)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->parseJson (+f_data)- (#echo(__LINE__)#)"); }

		$f_json = $this->getJsonParser ();
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->parseJson ()- (#echo(__LINE__)#)",(:#*/$f_json->json2data ($f_data)/*#ifdef(DEBUG):),true):#*/;
	}

/**
	* Parse and return the parameter list.
	*
	* @param  array $f_params Array with parameters
	* @return boolean True on success
	* @since  v0.1.00
*/
	public function parseParams ($f_params)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->parseParams (+f_params)- (#echo(__LINE__)#)"); }
		$f_return = (($this->json_mode == "2.0") ? "" : "[]");

		if (!empty ($f_params)) { $f_return = $this->get ($f_params); }
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->parseParams ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directHttpJsonrpc",true);

//j// Script specific commands

global $direct_globals;
$direct_globals['@names']['web_http_jsonrpc'] = 'dNG\sWG\web\directHttpJsonrpc';
}

//j// EOF
?>