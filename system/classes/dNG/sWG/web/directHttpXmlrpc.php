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
use dNG\sWG\directXml,
    dNG\sWG\web\directHttpRequest;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("CLASS_directHttpXmlrpc"))
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
class directHttpXmlrpc extends directHttpRequest
{
/**
	* @var array $RESULT_400 [400] Bad Request
*/
	public static $RESULT_400 = array ("-32600","[400] Bad Request");
/**
	* @var array $RESULT_401 [401] Unauthorized
*/
	public static $RESULT_401 = array ("-32500","[401] Unauthorized");
/**
	* @var array $RESULT_402 [402] Payment Required
*/
	public static $RESULT_402 = array ("-32500","[402] Payment Required");
/**
	* @var array $RESULT_403 [403] Forbidden
*/
	public static $RESULT_403 = array ("-32500","[403] Forbidden");
/**
	* @var array $RESULT_404 [404] Not Found
*/
	public static $RESULT_404 = array ("-32601","[404] Not Found");
/**
	* @var array $RESULT_405 [405] Method Not Allowed
*/
	public static $RESULT_405 = array ("-32602","[405] Method Not Allowed");
/**
	* @var array $RESULT_406 [406] Not Acceptable
*/
	public static $RESULT_406 = array ("-32600","[406] Not Acceptable");
/**
	* @var array $RESULT_407 [407] Proxy Authentication Required
*/
	public static $RESULT_407 = array ("-32500","[407] Proxy Authentication Required");
/**
	* @var array $RESULT_408 [408] Request Timeout
*/
	public static $RESULT_408 = array ("-32300","[407] Request Timeout");
/**
	* @var array $RESULT_409 [409] Conflict
*/
	public static $RESULT_409 = array ("-32400","[409] Conflict");
/**
	* @var array $RESULT_410 [410] Gone
*/
	public static $RESULT_410 = array ("-32400","[410] Gone");
/**
	* @var array $RESULT_411 [411] Length Required
*/
	public static $RESULT_411 = array ("-32602","[411] Length Required");
/**
	* @var array $RESULT_412 [412] Precondition Failed
*/
	public static $RESULT_412 = array ("-32602","[412] Precondition Failed");
/**
	* @var array $RESULT_413 [413] Request Entity Too Large
*/
	public static $RESULT_413 = array ("-32602","[413] Request Entity Too Large");
/**
	* @var array $RESULT_414 [414] Request-URI Too Long
*/
	public static $RESULT_414 = array ("-32400","[414] Request-URI Too Long");
/**
	* @var array $RESULT_415 [415] Unsupported Media Type
*/
	public static $RESULT_415 = array ("-32400","[415] Unsupported Media Type");
/**
	* @var array $RESULT_416 [416] Requested Range Not Satisfiable
*/
	public static $RESULT_416 = array ("-32602","[416] Requested Range Not Satisfiable");
/**
	* @var array $RESULT_417 [417] Expectation Failed
*/
	public static $RESULT_417 = array ("-32602","[417] Expectation Failed");
/**
	* @var array $RESULT_500 [500] Internal Server Error
*/
	public static $RESULT_500 = array ("-32500","[500] Internal Server Error");
/**
	* @var array $RESULT_501 [501] Not Implemented
*/
	public static $RESULT_501 = array ("-32601","[501] Not Implemented");
/**
	* @var array $RESULT_502 [502] Bad Gateway
*/
	public static $RESULT_502 = array ("-32300","[502] Bad Gateway");
/**
	* @var array $RESULT_503 [503] Service Unavailable
*/
	public static $RESULT_503 = array ("-32400","[503] Service Unavailable");
/**
	* @var array $RESULT_504 [504] Gateway Timeout
*/
	public static $RESULT_504 = array ("-32300","[504] Gateway Timeout");
/**
	* @var array $RESULT_505 [505] HTTP Version Not Supported
*/
	public static $RESULT_505 = array ("-32300","[505] HTTP Version Not Supported");
/**
	* @var array $methods Method cache for a single or multiple method calls.
*/
	protected $methods;
/**
	* @var mixed $result Result cache
*/
	protected $result;
/**
	* @var direct_xml $xml Parser object
*/
	protected $xml;

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directHttpXmlrpc)
	*
	* @since v0.1.00
*/
	public function __construct ()
	{
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->__construct (directHttpXmlrpc)- (#echo(__LINE__)#)"); }

		if (!defined ("CLASS_directXml")) { $direct_globals['basic_functions']->includeClass ('dNG\sWG\directXml',2); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['defineCall'] = true;
		$this->functions['getBase64'] = true;
		$this->functions['getDatetime'] = true;
		$this->functions['getFault'] = true;
		$this->functions['getXmlParser'] = defined ("CLASS_directXml");
		$this->functions['parseParams'] = true;
		$this->functions['parseXml'] = true;

/* -------------------------------------------------------------------------
Set up the caching variable
------------------------------------------------------------------------- */

		$this->methods = array ();
		$this->parser = NULL;
		$this->result = false;
	}

/**
	* Adds a method call to the cache for later execution.
	*
	* @param  string $f_method The XML-RPC method to be called.
	* @param  mixed $f_params Parameter definition.
	* @since  v0.1.00
*/
	public function defineCall ($f_method,$f_params)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->defineCall ($f_method,+f_params)- (#echo(__LINE__)#)"); }
		$this->methods[] = array ($f_method,$f_params);
	}

/**
	* This function returns the whole XML array tree.
	*
	* @param  mixed $f_method The XML-RPC method to be called. "NULL" to execute
	*         all methods defined with "defineCall ()".
	* @param  mixed $f_params Parameter definition.
	* @return boolean True on success
	* @since  v0.1.00
*/
	public function execute ($f_method = NULL,$f_params = NULL)
	{
		global $direct_local;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->execute (+f_method,+f_params)- (#echo(__LINE__)#)"); }

		$f_return = false;
		$f_data = false;

		if (($this->functions['define_content_type'])&&(isset ($this->server,$this->port,$this->path)))
		{
			if (isset ($f_method))
			{
				$f_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodCall><methodName>$f_method</methodName><params>";
				$f_data .= ((is_array ($f_params)) ? $this->parseParams ($f_params) : "<param><value>".($this->get ($f_params))."</value></param>");
				$f_data .= "</params></methodCall>";
			}
			elseif (count ($this->methods) > 1)
			{
				$f_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodCall><methodName>system.multicall</methodName><params>";
				foreach ($this->methods as $f_method_array) { $f_data .= "<param><value>".($this->get (array ("methodName" => $f_method_array[0],"params" => $f_method_array[1])))."</value></param>"; }
				$f_data .= "</params></methodCall>";

				$this->methods = array ();
			}
			elseif (!empty ($this->methods))
			{
				$f_method_array = array_pop ($this->methods);
				$f_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodCall><methodName>$f_method_array[0]</methodName><params>";
				$f_data .= ((is_array ($f_method_array[1])) ? $this->parseParams ($f_method_array[1]) : "<param><value>".($this->get ($f_method_array[1]))."</value></param>");
				$f_data .= "</params></methodCall>";
			}
		}

		if ($f_data)
		{
			$this->content_type = "text/xml";
			$f_data = $this->httpPost ($this->server,$this->port,$this->path,$f_data,false);

			$this->content_type = NULL;
			$this->result = NULL;

			if (is_string ($f_data))
			{
				$f_xml = $this->getXmlParser ();

				if (($f_xml)&&($f_xml->xml2array ($f_data)))
				{
					$f_data = $f_xml->nodeGet ("methodResponse params param");

					if ($f_data)
					{
						$this->result = $this->parseXml ($f_data);
						$f_return = $this->result;
					}

					$f_xml->set (array (),true);
				}
			}
		}

		return $f_return;
	}

/**
	* Parse PHP data for XML-RPC output.
	*
	* @param  array $f_data XML array tree
	* @return array Array with pointers to the documents
	* @since  v0.1.00
*/
	public function get ($f_data)
	{
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->get (+f_data)- (#echo(__LINE__)#)"); }

		$f_return = "<nil />";

		if (isset ($f_data))
		{
			$f_type = gettype ($f_data);

			if (($f_type == "array")&&(isset ($f_data[''])))
			{
				switch ($f_data[''])
				{
				case "dateTime.iso8601":
				{
					$f_data = array_pop ($f_data);
					$f_type = "datetime";
					break 1;
				}
				case "base64":
				{
					$f_data = array_pop ($f_data);
					$f_type = "base64";
					break 1;
				}
				default:
				{
					$f_struct_check = true;
					unset ($f_data['']);
				}
				}
			}

			switch ($f_type)
			{
			case "array":
			{
				reset ($f_data);
				$f_key = key ($f_data);
				if (!isset ($f_struct_check)) { $f_struct_check = is_string ($f_key); }

				$f_return = ($f_struct_check ? "<struct>" : "<array>");

				if (count ($f_data))
				{
					if (!$f_struct_check) { $f_return .= "<data>"; }

					foreach ($f_data as $f_key => $f_value)
					{
						if ($f_struct_check) { $f_return .= "<member>".($direct_globals['xml_bridge']->array2xmlItemEncoder (array ("tag" => "name","value" => $f_key)))."<value>".($this->get ($f_value))."</value></member>"; }
						else { $f_return .= "<value>".($this->get ($f_value))."</value>"; }
					}

					if (!$f_struct_check) { $f_return .= "</data>"; }
				}

				$f_return .= ($f_struct_check ? "</struct>" : "</array>");
				break 1;
			}
			case "base64":
			{
				$f_return = $direct_globals['xml_bridge']->array2xmlItemEncoder (array ("tag" => "base64","value" => $f_data));
				break 1;
			}
			case "boolean":
			{
				$f_return = ($f_data ? "<boolean>1</boolean>" : "<boolean>0</boolean>");
				break 1;
			}
			case "datetime":
			{
				$f_return = "<dateTime.iso8601>$f_data</dateTime.iso8601>";
				break 1;
			}
			case "double":
			case "float":
			{
				$f_return = "<double>$f_data</double>";
				break 1;
			}
			case "integer":
			{
				$f_return = "<int>$f_data</int>";
				break 1;
			}
			case "string":
			{
				$f_return = $direct_globals['xml_bridge']->array2xmlItemEncoder (array ("tag" => "string","value" => $f_data));
				break 1;
			}
			}
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->get ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Returns an array for "parse ()" for the given BASE64 encoded string.
	*
	* @param  array $f_data BASE64 encoded string
	* @return array Encoded base64 XML-RPC type
	* @since  v0.1.00
*/
	public function getBase64 ($f_data = NULL)
	{
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getBase64 (+f_data)- (#echo(__LINE__)#)"); }
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getBase64 ()- (#echo(__LINE__)#)",(:#*/array ("" => "base64",(base64_encode ($f_data)))/*#ifdef(DEBUG):),true):#*/;
	}

/**
	* Returns an array for "parse ()" for the given timestamp.
	*
	* @param  array $f_timestamp UNIX timestamp or NULL for the current GMT time
	* @return array Encoded timestamp
	* @since  v0.1.00
*/
	public function getDatetime ($f_timestamp = NULL)
	{
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getDatetime (+f_timestamp)- (#echo(__LINE__)#)"); }

		$f_return = (($f_timestamp == NULL) ? gmdate ("Ymd\TH:i:s") : gmdate ("Ymd\TH:i:s",$f_timestamp));
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getDatetime ()- (#echo(__LINE__)#)",(:#*/array ("" => "dateTime.iso8601",$f_return)/*#ifdef(DEBUG):),true):#*/;
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
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getFault (+f_data)- (#echo(__LINE__)#)"); }

return ("<value><struct>
<member><name>faultCode</name><value>".($direct_globals['xml_bridge']->array2xmlItemEncoder (array ("tag" => "int","value" => $f_data[0])))."</value></member>
<member><name>faultString</name><value>".($direct_globals['xml_bridge']->array2xmlItemEncoder (array ("tag" => "string","value" => $f_data[1])))."</value></member>
</struct></value>");
	}

/**
	* Returns the active XML parser.
	*
	* @return string XML fault struct
	* @since  v0.1.00
*/
	public function getXmlParser ()
	{
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->getXmlParser ()- (#echo(__LINE__)#)"); }

		if (!isset ($this->parser)) { $this->parser = new directXml (); }
		return $this->parser;
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

		$f_return = "";
		foreach ($f_params as $f_param) { $f_return .= "<param><value>".($this->get ($f_param))."</value></param>"; }
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->parseParams ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

/**
	* Parse a value XML array tree.
	*
	* @param  array $f_data XML array tree
	* @return array Array with pointers to the documents
	* @since  v0.1.00
*/
	public function parseXml ($f_data)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -webServices->parseXml (+f_data)- (#echo(__LINE__)#)"); }
		$f_return = NULL;

		if (is_array ($f_data))
		{
			if (isset ($f_data['xml.item']))
			{
				$f_xml_array = $f_data['xml.item'];
				unset ($f_data['xml.item']);

				if (isset ($f_xml_array['tag']))
				{
					switch ($f_xml_array['tag'])
					{
					case "array":
					{
						if (isset ($f_data['data']['value']['xml.mtree']))
						{
							$f_data = $f_data['data']['value'];
							unset ($f_data['xml.mtree']);
							$f_return = array ();

							foreach ($f_data as $f_entry)
							{
								if (isset ($f_entry)) { $f_return[] = $this->parseXml ($f_entry); }
							}
						}
						elseif (isset ($f_data['data']['value']))
						{
							$f_data = $this->parseXml ($f_data['data']['value']);
							$f_return = (((isset ($f_data))&&($f_data)) ? array ($f_data) : array ());
						}

						break 1;
					}
					case "member":
					{
						if (isset ($f_data['name']['value'],$f_data['value'])) { $f_return = array ("name" => $f_data['name']['value'],"value" => $this->parseXml ($f_data['value'])); }
						break 1;
					}
					case "struct":
					{
						if (isset ($f_data['member']))
						{
							$f_data = $f_data['member'];

							if (isset ($f_data['xml.mtree']))
							{
								unset ($f_data['xml.mtree']);
								$f_return = array ();

								foreach ($f_data as $f_entry_array)
								{
									$f_entry_array = $this->parseXml ($f_entry_array);
									if (is_array ($f_entry_array)) { $f_return[$f_entry_array['name']] = $f_entry_array['value']; }
								}
							}
							else
							{
								$f_entry_array = $this->parseXml ($f_data);
								if (is_array ($f_entry_array)) { $f_return = array ($f_entry_array['name'] => $f_entry_array['value']); }
							}
						}

						break 1;
					}
					case "value":
					{
						$f_data = array_pop ($f_data);
						$f_data = $this->parseXml ($f_data);
						if (isset ($f_data)) { $f_return = $f_data; }

						break 1;
					}
					}
				}
			}
			elseif (isset ($f_data['tag'],$f_data['value']))
			{
				switch ($f_data['tag'])
				{
				case "base64":
				{
					$f_return = base64_decode ($f_data['value']);
					if (is_bool ($f_return)) { $f_return = NULL; }

					break 1;
				}
				case "boolean":
				{
					$f_return = ($f_data['value'] ? true : false);
					break 1;
				}
				case "dateTime.iso8601":
				{
					$f_return = $f_data['value'];
					break 1;
				}
				case "double":
				{
					$f_return = (float)$f_data['value'];
					break 1;
				}
				case "i4":
				case "int":
				{
					$f_return = (int)$f_data['value'];
					break 1;
				}
				default: { $f_return = $f_data['value']; }
				}
			}
		}
		else { $f_return = $f_data; }

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -webServices->parseXml ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directHttpXmlrpc",true);

//j// Script specific commands

global $direct_globals;
$direct_globals['@names']['web_http_xmlrpc'] = 'dNG\sWG\web\directHttpXmlrpc';
}

//j// EOF
?>