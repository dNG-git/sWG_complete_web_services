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
$Id$
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
if (defined ("CLASS_direct_web_http_xmlrpc")) { $g_continue_check = false; }
if (($g_continue_check)&&(!defined ("CLASS_direct_web_service_xmlrpc"))) { $g_continue_check = $direct_classes['basic_functions']->include_file ($direct_settings['path_system']."/classes/dhandler/swg_web_service_xmlrpc.php",1); }
if (($g_continue_check)&&(!defined ("CLASS_direct_web_http_request"))) { $g_continue_check = $direct_classes['basic_functions']->include_file ($direct_settings['path_system']."/classes/web_services/swg_http_request.php",1); }
if (($g_continue_check)&&(!defined ("CLASS_direct_xml"))) { $g_continue_check = $direct_classes['basic_functions']->include_file ($direct_settings['path_system']."/classes/swg_xml.php",1); }

if ($g_continue_check)
{
//c// direct_web_http_xmlrpc
/**
* Provides a interface to run a single or multiple methods on a server via
* XML-RPC.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;w3c
*             W3C (R) Software License
*/
class direct_web_http_xmlrpc extends direct_web_http_request
{
/**
	* @var direct_web_service_xmlrpc $class_xmlrpc XML-RPC parser
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $class_xmlrpc;
/**
	* @var array $methods Method cache for a single or multiple method calls.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $methods;
/**
	* @var mixed $result Result cache
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $result;

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

	//f// direct_web_http_xmlrpc->__construct ()
/**
	* Constructor (PHP5) __construct (direct_web_http_xmlrpc)
	*
	* @uses  USE_debug_reporting
	* @since v0.1.00
*/
	public function __construct ()
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_http_xmlrpc->__construct (direct_web_http_xmlrpc)- (#echo(__LINE__)#)"); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['define_call'] = true;

/* -------------------------------------------------------------------------
Set up the caching variable
------------------------------------------------------------------------- */

		$this->class_xmlrpc = NULL;
		$this->methods = array ();
	}

	//f// direct_web_http_xmlrpc->define_call ($f_method,$f_params)
/**
	* Adds a method call to the cache for later execution.
	*
	* @param  string $f_method The XML-RPC method to be called.
	* @param  mixed $f_params Parameter definition.
	* @uses   USE_debug_reporting
	* @since  v0.1.00
*/
	public function define_call ($f_method,$f_params)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_http_xmlrpc->define_call ($f_method,+f_params)- (#echo(__LINE__)#)"); }

		if (!$this->class_xmlrpc) { $this->class_xmlrpc = new direct_web_service_xmlrpc (); }
		if ($this->class_xmlrpc) { $this->methods[] = array ($f_method,$f_params); }
	}

	//f// direct_web_http_xmlrpc->execute ($f_method = NULL,$f_params = NULL)
/**
	* This function returns the whole XML array tree.
	*
	* @param  mixed $f_method The XML-RPC method to be called. "NULL" to execute
	*         all methods defined with "define_call ()".
	* @param  mixed $f_params Parameter definition.
	* @uses   USE_debug_reporting
	* @return boolean True on success
	* @since  v0.1.00
*/
	public function execute ($f_method = NULL,$f_params = NULL)
	{
		global $direct_local;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_http_xmlrpc->execute (+f_method,+f_params)- (#echo(__LINE__)#)"); }

		$f_return = false;
		$f_data = false;

		if (!$this->class_xmlrpc) { $this->class_xmlrpc = new direct_web_service_xmlrpc (); }

		if (($this->class_xmlrpc)&&($this->functions['define_content_type'])&&(isset ($this->server,$this->port,$this->path)))
		{
			if (isset ($f_method))
			{
				$f_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodCall><methodName>$f_method</methodName><params>";

				if (is_array ($f_params)) { $f_data .= $this->parse_params ($f_params); }
				else { $f_data .= "<param><value>".($this->class_xmlrpc->parse ($f_params))."</value></param>"; }

				$f_data .= "</params></methodCall>";
			}
			elseif (count ($this->methods) > 1)
			{
				$f_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodCall><methodName>system.multicall</methodName><params>";
				foreach ($this->methods as $f_method_array) { $f_data .= "<param><value>".($this->class_xmlrpc->parse (array ("methodName" => $f_method_array[0],"params" => $f_method_array[1])))."</value></param>"; }
				$f_data .= "</params></methodCall>";

				$this->methods = array ();
			}
			elseif (!empty ($this->methods))
			{
				$f_method_array = array_pop ($this->methods);
				$f_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodCall><methodName>$f_method_array[0]</methodName><params>";

				if (is_array ($f_method_array[1])) { $f_data .= $this->parse_params ($f_method_array[1]); }
				else { $f_data .= "<param><value>".($this->class_xmlrpc->parse ($f_method_array[1]))."</value></param>"; }

				$f_data .= "</params></methodCall>";
			}
		}

		if ($f_data)
		{
			$this->content_type = "text/xml";
			$f_data = $this->http_post ($this->server,$this->port,$this->path,$f_data,false);

			$this->content_type = NULL;
			$this->result = NULL;

			if (is_string ($f_data))
			{
				$f_xml = new direct_xml ();

				if (($f_xml)&&($f_xml->xml2array ($f_data)))
				{
					$f_data = $f_xml->node_get ("methodResponse params param");

					if ($f_data)
					{
						$this->result = $this->class_xmlrpc->parse_xmlrpc ($f_data);
						$f_return = $this->result;
					}
				}
			}
		}

		return $f_return;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_direct_web_http_xmlrpc",true);
}

//j// EOF
?>