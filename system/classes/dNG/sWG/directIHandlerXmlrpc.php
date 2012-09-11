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
* OOP (Object Oriented Programming) requires an abstract data
* handling. The sWG is OO (where it makes sense).
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

namespace dNG\sWG;
/* #\n*/
/*#use(direct_use) */
use dNG\sWG\directIHandlerBasics,
    dNG\sWG\web\directHttpXmlrpc;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("directIHandlerXmlrpc"))
{
/**
* This abstraction layer provides functions to handle XML-RPC calls.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/
class directIHandlerXmlrpc extends directIHandlerBasics
{
/**
	* @var array $data Input data cache
*/
	protected $data;
/**
	* @var boolean $data_multicall True if we have a system.multicall request.
*/
	protected $data_multicall;
/**
	* @var integer $data_multicall_current Current system.multicall request
	*      handled.
*/
	protected $data_multicall_current;
/**
	* @var string $prefix XML-RPC method prefix
*/
	protected $prefix;

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directIHandlerXmlrpc)
	*
	* @since v0.1.00
*/
	public function __construct ()
	{
		global $direct_globals,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -iHandler->__construct (directIHandlerXmlrpc)- (#echo(__LINE__)#)"); }

		if (!isset ($direct_globals['@names']['web_http_xmlrpc'])) { $direct_globals['basic_functions']->includeClass ('dNG\sWG\web\directHttpXmlrpc',2); }
		if (!isset ($direct_globals['web_http_xmlrpc'])) { direct_class_init ("web_http_xmlrpc"); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['authCheck'] = $direct_globals['basic_functions']->includeFile ($direct_settings['path_system']."/functions/web_services/swg_http_auth.php",2);
		$this->functions['get'] = true;
		$this->functions['getParams'] = true;
		$this->functions['getRequest'] = true;
		$this->functions['multicallHandle'] = isset ($direct_globals['web_http_xmlrpc']);
		$this->functions['multicallCheck'] = true;
		$this->functions['multicallGet'] = true;

/* -------------------------------------------------------------------------
Set up additional variables :)
------------------------------------------------------------------------- */

		$this->data = array ();
		$this->data_multicall = false;
		$this->data_multicall_current = 0;
		$this->method = "";
 		$this->prefix = (isset ($direct_settings['dsd']['xmodule']) ? $direct_globals['basic_functions']->inputfilterFilePath (str_replace (" ","/",$direct_settings['dsd']['xmodule'])) : "");

		if ((isset ($_SERVER['PHP_AUTH_DIGEST']))&&(preg_match ("#opaque=\"(\w{32})\"#",$_SERVER['PHP_AUTH_DIGEST'],$f_result_array)))
		{
			$this->auth = "digest";
			$this->uuid = $f_result_array[1];
		}
		elseif (isset ($_SERVER['PHP_AUTH_USER']))
		{
			$this->auth = "basic";
			$this->pass = (isset ($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : NULL);
			$this->user = $_SERVER['PHP_AUTH_USER'];
		}

		if (isset ($direct_globals['web_http_xmlrpc']))
		{
			if (($this->prefix)&&($_SERVER['REQUEST_METHOD'] == "GET")) { $f_input_data = "<methodCall><methodName>http_get</methodName><params/></methodCall>"; }
			elseif ($_SERVER['REQUEST_METHOD'] == "HEAD") { $f_input_data = ($this->prefix ? "<methodCall><methodName>http_head</methodName><params/></methodCall>" : "<methodCall><methodName>default.xmlrpc.http_head</methodName><params/></methodCall>"); }
			elseif (isset ($_SERVER['HTTP_CONTENT_ENCODING']))
			{
				switch (strtolower ($_SERVER['HTTP_CONTENT_ENCODING']))
				{
				case "deflate":
				{
					$f_input_data = gzinflate (file_get_contents ("php://input"));
					break 1;
				}
				case "gzip":
				case "x-gzip":
				{
					$f_input_data = implode ("",(gzfile ("php://input")));
					break 1;
				}
				default: { $f_input_data = null; }
				}
			}
			else { $f_input_data = file_get_contents ("php://input"); }

			if (($f_input_data)&&(strlen ($f_input_data)))
			{
				$f_xml = $direct_globals['web_http_xmlrpc']->getXmlParser ();
				$f_continue_check = ((($f_xml)&&($f_xml->xml2array ($f_input_data))) ? true : false);
			}
			else { $f_continue_check = false; }

			if ($f_continue_check)
			{
				$f_data_array = $f_xml->nodeGet ("methodCall methodName");

				if ((is_array ($f_data_array))&&(isset ($f_data_array['value'])))
				{
					$f_xml->nodeCachePointer ("methodCall params");
					$f_params = $f_xml->nodeCount ("methodCall params param");

					$this->method = $f_data_array['value'];

					for ($f_i = 0;$f_i < $f_params;$f_i++)
					{
						$f_data_array = $f_xml->nodeGet ("methodCall params param#".$f_i);
						if (($f_data_array)&&(isset ($f_data_array['value']))) { $f_data_array = $direct_globals['web_http_xmlrpc']->parseXml ($f_data_array['value']); }
						if (isset ($f_data_array)) { $this->data[$f_i] = $f_data_array; }
					}

					if (($this->method)&&($this->method == "system.multicall"))
					{
						$f_request_array = (isset ($this->data[0],$this->data[0][0],$this->data[0][0]['methodName']) ? $this->getRequest ($this->data[0][0]['methodName']) : array ("default","xmlrpc","empty"));
						$this->data_multicall = true;
					}
					else { $f_request_array = $this->getRequest ($this->method); }

					if (is_array ($f_request_array))
					{
						$direct_settings['a'] = $f_request_array[2];
						$direct_settings['m'] = $f_request_array[0];
						$direct_settings['s'] = $f_request_array[1];
					}
				}

				$f_xml->set (array (),true);
			}
		}
	}

/**
	* Returns the authentification state.
	*
	* @return boolean True if authenticated
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function authCheck () { return direct_web_http_auth_check (); }

/**
	* Reads and parses the XML-RPC request.
	*
	* @param  integer $f_number Parameter position or nothing for all
	*         parameters.
	* @return mixed Requested method and params array; false on error
	* @since  v0.1.00
*/
	public function get ($f_number = NULL)
	{
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -iHandler->get (+f_number)- (#echo(__LINE__)#)"); }
		$f_return = NULL;

		if ($this->data_multicall)
		{
			if (isset ($this->data[0][$this->data_multicall_current],$this->data[0][$this->data_multicall_current]['methodName'],$this->data[0][$this->data_multicall_current]['params']))
			{
				$f_data = $this->data[0][$this->data_multicall_current]['params'];
				$f_return = (isset ($f_number,$f_data[$f_number]) ? array ($this->data[0][$this->data_multicall_current]['methodName'],$f_data[$f_number]) : array ($this->data[0][$this->data_multicall_current]['methodName'],$f_data));
			}
		}
		elseif (strlen ($this->method)) { $f_return = ((isset ($f_number,$this->data[$f_number])) ? array ($this->method,$this->data[$f_number]) : array ($this->method,$this->data)); }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -iHandler->get ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Get all parameters or the parameter at the defined position.
	*
	* @param  integer $f_number Parameter position or nothing for all
	*         parameters.
	* @return array Category data
	* @since  v0.1.00
*/
	public function getParams ($f_number = NULL)
	{
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -iHandler->getParams (+f_number)- (#echo(__LINE__)#)"); }
		$f_return = NULL;

		if (!empty ($this->data))
		{
			$f_data = NULL;

			if ($this->data_multicall)
			{
				if (isset ($this->data[0][$this->data_multicall_current],$this->data[0][$this->data_multicall_current]['params'])) { $f_data = $this->data[0][$this->data_multicall_current]['params']; }
			}
			else { $f_data = $this->data; }

			if (isset ($f_data)) { $f_return = ((isset ($f_number,$f_data[$f_number])) ? $f_data[$f_number] : $f_data); }
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -iHandler->getParams ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Handle XML-RPC request.
	*
	* @param  string $f_base_module Module definition to be used as prefix
	* @return array Category data
	* @since  v0.1.00
*/
	protected function getRequest ($f_method)
	{
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -iHandler->getRequest ()- (#echo(__LINE__)#)"); }

		$f_return = array ("index","index","default");

		$f_module = (strlen ($this->prefix) ? $this->prefix."." : "");
		$f_module .= $f_method;
		$f_module = $direct_globals['basic_functions']->inputfilterFilePath (str_replace (".","/",$f_module));
		$f_module_array = explode ("/",$f_module);

		if (!empty ($f_module_array)) { $f_return[2] = array_pop ($f_module_array); }
		if (!empty ($f_module_array)) { $f_return[0] = array_shift ($f_module_array); }
		if (!empty ($f_module_array)) { $f_return[1] = implode ("/",$f_module_array); }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -iHandler->getRequest ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Returns true if a XML-RPC result is set.
	*
	* @return boolean True if set
	* @since  v0.1.00
*/
	public function multicallCheck ($f_last_check = false)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -iHandler->multicallCheck ()- (#echo(__LINE__)#)"); }
		$f_return = false;

		if ($f_last_check)
		{
			if (((!$this->data_multicall))||(count ($this->data[0]) <= (1 + $this->data_multicall_current))) { $f_return = true; }
		}
		else { $f_return = $this->data_multicall; }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -iHandler->multicallCheck ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Handle XML-RPC request.
	*
	* @param  string $f_base_module Module definition to be used as prefix
	* @return array Category data
	* @since  v0.1.00
*/
	public function multicallHandle ()
	{
		global $direct_cachedata,$direct_globals,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -iHandler->multicallHandle ()- (#echo(__LINE__)#)"); }

		$f_error = array ();
		$this->data_multicall_current++;

		if (isset ($this->data[0],$this->data[0][$this->data_multicall_current],$this->data[0][$this->data_multicall_current]['methodName']))
		{
			$f_call_array = $this->data[0][$this->data_multicall_current];
			$f_request_array = $this->getRequest ($f_call_array['methodName']);

			if (($direct_cachedata['core_time'] + $direct_settings['timeout'] + $direct_settings['timeout_core']) < (time ())) { $f_error = directHttpXmlrpc::$RESULT_504; }
			elseif (is_array ($f_request_array))
			{
				$direct_settings['a'] = $f_request_array[2];
				$direct_settings['m'] = $f_request_array[0];
				$direct_settings['s'] = $f_request_array[1];

				$f_module = trim (str_replace ("/"," ",$direct_settings['s']));
				$f_module_data = explode (" ",$f_module);
				$f_module = ((count ($f_module_data)) - 1);
				$f_module_data[$f_module] = ("swg_".$f_module_data[$f_module].".php");
				$f_module = implode ("/",$f_module_data);

				if (file_exists ($direct_settings['path_system']."/modules/$direct_settings[m]/".$f_module)) { $direct_globals['basic_functions']->includeFile ($direct_settings['path_system']."/modules/$direct_settings[m]/".$f_module,4,false); }
				else { $f_error = directHttpXmlrpc::$RESULT_404; }
			}
			else { $f_error = directHttpXmlrpc::$RESULT_400; }
		}
		else { $f_error = directHttpXmlrpc::$RESULT_400; }

		if (!empty ($f_error))
		{
			if (!isset ($direct_globals['output'])) { direct_class_init ("output"); }
			$direct_globals['output']->outputSendError ($f_error);
		}
	}

/**
	* Returns true if a XML-RPC result is set.
	*
	* @return boolean True if set
	* @since  v0.1.00
*/
	public function multicallGet ($f_number = false)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -iHandler->multicallGet ()- (#echo(__LINE__)#)"); }

		if ($f_number) { $f_return = ($this->data_multicall ? $this->data_multicall_current : 0); }
		elseif ($this->data_multicall) { $f_return = ((count ($this->data[0]) >= $this->data_multicall_current) ? $this->get ($this->data_multicall_current) : NULL); }
		else { $f_return = $this->get (); }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -iHandler->multicallGet ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directIHandlerXmlrpc",true);

//j// Script specific commands

global $direct_globals;
$direct_globals['@names']['input'] = 'dNG\sWG\directIHandlerXmlrpc';
}

//j// EOF
?>