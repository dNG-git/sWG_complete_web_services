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

$g_continue_check = ((defined ("CLASS_direct_web_service_xmlrpc")) ? false : true);
if (!defined ("CLASS_direct_data_handler")) { $g_continue_check = false; }

if ($g_continue_check)
{
//c// direct_web_service_xmlrpc
/**
* This abstraction layer provides functions to handle XML-RPC calls.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @uses       CLASS_direct_data_handler
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;w3c
*             W3C (R) Software License
*/
class direct_web_service_xmlrpc extends direct_data_handler
{
/**
	* @var integer $data_multicall True if we have a system.multicall request.
*/
	protected $data_multicall;
/**
	* @var integer $data_multicall_current Current system.multicall request
	*              handled.
*/
	protected $data_multicall_current;
/**
	* @var string $data_result Result string
*/
	protected $data_result;
/**
	* @var string $method Method name called
*/
	protected $method;
/**
	* @var direct_xml $xml_object XML object to parse input.
*/
	protected $xml_object;
/**
	* @var array $RESULT_400 [400] Bad Request
*/
	static $RESULT_400 = array ("-32600","[400] Bad Request");
/**
	* @var array $RESULT_401 [401] Unauthorized
*/
	static $RESULT_401 = array ("-32500","[401] Unauthorized");
/**
	* @var array $RESULT_402 [402] Payment Required
*/
	static $RESULT_402 = array ("-32500","[402] Payment Required");
/**
	* @var array $RESULT_403 [403] Forbidden
*/
	static $RESULT_403 = array ("-32500","[403] Forbidden");
/**
	* @var array $RESULT_404 [404] Not Found
*/
	static $RESULT_404 = array ("-32601","[404] Not Found");
/**
	* @var array $RESULT_405 [405] Method Not Allowed
*/
	static $RESULT_405 = array ("-32602","[405] Method Not Allowed");
/**
	* @var array $RESULT_406 [406] Not Acceptable
*/
	static $RESULT_406 = array ("-32600","[406] Not Acceptable");
/**
	* @var array $RESULT_407 [407] Proxy Authentication Required
*/
	static $RESULT_407 = array ("-32500","[407] Proxy Authentication Required");
/**
	* @var array $RESULT_408 [408] Request Timeout
*/
	static $RESULT_408 = array ("-32300","[407] Request Timeout");
/**
	* @var array $RESULT_409 [409] Conflict
*/
	static $RESULT_409 = array ("-32400","[409] Conflict");
/**
	* @var array $RESULT_410 [410] Gone
*/
	static $RESULT_410 = array ("-32400","[410] Gone");
/**
	* @var array $RESULT_411 [411] Length Required
*/
	static $RESULT_411 = array ("-32602","[411] Length Required");
/**
	* @var array $RESULT_412 [412] Precondition Failed
*/
	static $RESULT_412 = array ("-32602","[412] Precondition Failed");
/**
	* @var array $RESULT_413 [413] Request Entity Too Large
*/
	static $RESULT_413 = array ("-32602","[413] Request Entity Too Large");
/**
	* @var array $RESULT_414 [414] Request-URI Too Long
*/
	static $RESULT_414 = array ("-32400","[414] Request-URI Too Long");
/**
	* @var array $RESULT_415 [415] Unsupported Media Type
*/
	static $RESULT_415 = array ("-32400","[415] Unsupported Media Type");
/**
	* @var array $RESULT_416 [416] Requested Range Not Satisfiable
*/
	static $RESULT_416 = array ("-32602","[416] Requested Range Not Satisfiable");
/**
	* @var array $RESULT_417 [417] Expectation Failed
*/
	static $RESULT_417 = array ("-32602","[417] Expectation Failed");
/**
	* @var array $RESULT_500 [500] Internal Server Error
*/
	static $RESULT_500 = array ("-32500","[500] Internal Server Error");
/**
	* @var array $RESULT_501 [501] Not Implemented
*/
	static $RESULT_501 = array ("-32601","[501] Not Implemented");
/**
	* @var array $RESULT_502 [502] Bad Gateway
*/
	static $RESULT_502 = array ("-32300","[502] Bad Gateway");
/**
	* @var array $RESULT_503 [503] Service Unavailable
*/
	static $RESULT_503 = array ("-32400","[503] Service Unavailable");
/**
	* @var array $RESULT_504 [504] Gateway Timeout
*/
	static $RESULT_504 = array ("-32300","[504] Gateway Timeout");
/**
	* @var array $RESULT_505 [505] HTTP Version Not Supported
*/
	static $RESULT_505 = array ("-32300","[505] HTTP Version Not Supported");

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

	//f// direct_web_service_xmlrpc->__construct ()
/**
	* Constructor (PHP5) __construct (direct_web_service_xmlrpc)
	*
	* @uses  direct_basic_functions::include_file()
	* @uses  direct_class_init()
	* @uses  direct_debug()
	* @uses  USE_debug_reporting
	* @since v0.1.00
*/
	public function __construct ()
	{
		global $direct_classes,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->__construct (direct_web_service_xmlrpc)- (#echo(__LINE__)#)"); }

		if (!defined ("CLASS_direct_xml")) { $direct_classes['basic_functions']->include_file ($direct_settings['path_system']."/classes/swg_xml.php"); }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions 
------------------------------------------------------------------------- */

		$this->functions['get'] = defined ("CLASS_direct_xml");
		$this->functions['get_params'] = true;
		$this->functions['handle'] = true;
		$this->functions['is_result_set'] = true;
		$this->functions['parse'] = true;
		$this->functions['parse_datetime'] = true;
		$this->functions['parse_fault'] = true;
		$this->functions['parse_xmlrpc'] = true;
		$this->functions['response'] = true;
		$this->functions['set_fault'] = true;

/* -------------------------------------------------------------------------
Set up additional variables :)
------------------------------------------------------------------------- */

		$this->data = array ();
		$this->data_multicall = 0;
		$this->data_multicall_current = 0;
		$this->data_result = NULL;
		$this->method = "";
		$this->xml_object = NULL;
	}

	//f// direct_web_service_xmlrpc->get ($f_number = NULL)
/**
	* Reads and parses the XML-RPC request.
	*
	* @param  integer $f_number Parameter position or the position of the
	*         requested method in a "system.multicall" request.
	* @uses   direct_debug()
	* @uses   direct_xml_bridge::xml2array()
	* @uses   USE_debug_reporting
	* @return mixed Requested method and params array; false on error
	* @since  v0.1.00
*/
	public function get ($f_number = NULL)
	{
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->get (+f_number)- (#echo(__LINE__)#)"); }
		$f_return = false;

		if (!$this->data)
		{
			$f_input_data = file_get_contents ("php://input");

			if (($f_input_data)&&(strlen ($f_input_data)))
			{
				$this->xml_object = new direct_xml ();
				$f_continue_check = ((($this->xml_object)&&($this->xml_object->xml2array ($f_input_data))) ? true : false);
			}
			else { $f_continue_check = false; }

			if ($f_continue_check)
			{
				$f_data_array = $this->xml_object->node_get ("methodCall methodName");

				if ((is_array ($f_data_array))&&(isset ($f_data_array['value']))) { $this->method = $f_data_array['value']; }
				else { $f_continue_check = false; }
			}

			if ($f_continue_check)
			{
				$this->xml_object->node_cache_pointer ("methodCall params");
				$f_params = $this->xml_object->node_count ("methodCall params param");

				for ($f_i = 0;$f_i < $f_params;$f_i++)
				{
					$f_data_array = $this->xml_object->node_get ("methodCall params param#".$f_i);
					if (($f_data_array)&&(isset ($f_data_array['value']))) { $f_data_array = $this->parse_xmlrpc ($f_data_array['value']); }
					if ((isset ($f_data_array))&&($f_data_array)) { $this->data[$f_i] = $f_data_array; }
				}
			}
		}

		if (strlen ($this->method)) { $f_return = ((isset ($f_number,$this->data[$f_number])) ? array ($this->method,$this->data[$f_number]) : array ($this->method,$this->data)); }
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->get ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

	//f// direct_web_service_xmlrpc->get_params ($f_number = NULL)
/**
	* Get all parameters or the parameter at the defined position.
	*
	* @param  integer $f_number Position of the requested parameter.
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @return array Category data
	* @since  v0.1.00
*/
	public function get_params ($f_number = NULL)
	{
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->get_params (+f_number)- (#echo(__LINE__)#)"); }
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

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->get_params ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

	//f// direct_web_service_xmlrpc->handle ($f_base_module = NULL)
/**
	* Handle XML-RPC request.
	*
	* @param  integer $f_number Position of the requested method in a
	*         "system.multicall" request.
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @return array Category data
	* @since  v0.1.00
*/
	public function handle ($f_base_module = NULL)
	{
		global $direct_cachedata,$direct_classes,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->handle (+f_base_module)- (#echo(__LINE__)#)"); }

		$f_return = false;

		if (strlen ($this->method))
		{
			$f_calls_array = array ();

			if ($this->method == "system.multicall")
			{
				if ((!isset ($this->data[0]))||(!is_array ($this->data[0]))||(empty ($this->data[0]))) { $this->set_fault (direct_web_service_xmlrpc::$RESULT_400); }
				else
				{
					$this->data_multicall = count ($this->data);
					$this->data_result = array ();
					$f_calls_array = $this->data[0];
				}
			}
			else { $f_calls_array[] = array ("methodName" => $this->method); }

			foreach ($f_calls_array as $f_call_array)
			{
				if (isset ($f_call_array['methodName']))
				{
					if (($direct_cachedata['core_time'] + $direct_settings['timeout'] + $direct_settings['timeout_core']) < (time ())) { $this->set_fault (direct_web_service_xmlrpc::$RESULT_504); }
					else
					{
						$f_module = ((isset ($f_base_module)) ? $f_base_module."." : "");
						$f_module .= $f_call_array['methodName'];
						$f_module = $direct_classes['basic_functions']->inputfilter_filepath (str_replace (".","/",$f_module));
						$f_module_array = explode ("/",$f_module);

						if (count ($f_module_array) > 1)
						{
							$direct_settings['a'] = array_pop ($f_module_array);

							$f_module = array_pop ($f_module_array);
							$f_module_array[] = "swg_$f_module.php";
							$f_module = implode ("/",$f_module_array);

							if (file_exists ($direct_settings['path_system']."/modules/dataport/xmlrpc/".$f_module))
							{
								$direct_classes['basic_functions']->include ($direct_settings['path_system']."/modules/dataport/xmlrpc/".$f_module,4,false);

								if (($this->data_multicall)&&(!isset ($this->data_result[$this->data_multicall_current]))) { $this->set_fault (direct_web_service_xmlrpc::$RESULT_400); }
								elseif (!isset ($this->data_result)) { $this->set_fault (direct_web_service_xmlrpc::$RESULT_400); }
							}
							else { $this->set_fault (direct_web_service_xmlrpc::$RESULT_404); }
						}
						else { $this->set_fault (direct_web_service_xmlrpc::$RESULT_400); }
					}
				}
				else { $this->set_fault (direct_web_service_xmlrpc::$RESULT_400); }

				$this->data_multicall_current++;
			}
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->handle ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

	//f// direct_web_service_xmlrpc->is_result_set ()
/**
	* Returns true if a XML-RPC result is set.
	*
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @return boolean True if set
	* @since  v0.1.00
*/
	public function is_result_set ()
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->is_result_set ()- (#echo(__LINE__)#)"); }

		if ((($this->data_multicall)&&(isset ($this->data_result[$this->data_multicall_current])))||(!empty ($this->data_result))) { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->is_result_set ()- (#echo(__LINE__)#)",:#*/true/*#ifdef(DEBUG):,true):#*/; }
		else { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->is_result_set ()- (#echo(__LINE__)#)",:#*/false/*#ifdef(DEBUG):,true):#*/; }
	}

	//f// direct_web_service_xmlrpc->parse ($f_data)
/**
	* Parse PHP data for XML-RPC output.
	*
	* @param  array $f_data XML array tree
	* @uses   direct_datalinker::define_extra_attributes()
	* @uses   direct_datalinker::define_extra_conditions()
	* @uses   direct_datalinker::define_extra_joins()
	* @uses   direct_datalinker::get_subs()
	* @uses   direct_db::define_row_conditions_encode()
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @return array Array with pointers to the documents
	* @since  v0.1.00
*/
	public function parse ($f_data)
	{
		global $direct_classes;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->parse (+f_data)- (#echo(__LINE__)#)"); }

		$f_return = "<nil />";

		if (isset ($f_data))
		{
			$f_type = gettype ($f_data);

			if (($f_type == "array")&&(isset ($f_data[''])))
			{
				if ($f_data[''] == "dateTime.iso8601")
				{
					$f_data = array_pop ($f_data);
					$f_type = "datetime";
				}
				else
				{
					$f_struct_check = true;
					unset ($f_data['']);
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

				foreach ($f_data as $f_key => $f_value)
				{
					if ($f_struct_check) { $f_return .= "<member>".($direct_classes['xml_bridge']->array2xml_item_encoder (array ("tag" => "name","value" => $f_key)))."<value>".($this->parse ($f_value))."</value></member>"; }
					else { $f_return .= "<data><value>".($this->parse ($f_value))."</value></data>"; }
				}

				$f_return .= ($f_struct_check ? "</struct>" : "</array>");
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
				$f_return = $direct_classes['xml_bridge']->array2xml_item_encoder (array ("tag" => "string","value" => $f_data));
				break 1;
			}
			}
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->parse ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

	//f// direct_web_service_xmlrpc->parse_datetime ($f_timestamp = NULL)
/**
	* Returns an array for "parse ()" for the given timestamp.
	*
	* @param  array $f_timestamp UNIX timestamp or NULL for the current GMT time
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @return array Encoded timestamp
	* @since  v0.1.00
*/
	public function parse_datetime ($f_timestamp = NULL)
	{
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->parse_datetime (+f_timestamp)- (#echo(__LINE__)#)"); }

		$f_return = ((is_int ($f_timestamp)) ? gmdate ("c",$f_timestamp) : gmdate ("c"));
		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->handle ()- (#echo(__LINE__)#)",(:#*/array ("" => "dateTime.iso8601",$f_return)/*#ifdef(DEBUG):),true):#*/;
	}

	//f// direct_web_service_xmlrpc->parse_fault ($f_data)
/**
	* Returns the struct for the given XML-RPC fault.
	*
	* @param  array $f_data Fault data
	* @uses   direct_debug()
	* @uses   direct_xml_bridge::array2xml_item_encoder()
	* @uses   USE_debug_reporting
	* @return string XML fault struct
	* @since  v0.1.00
*/
	public function parse_fault ($f_data)
	{
		global $direct_classes;
		if (USE_debug_reporting) { direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->parse_fault (+f_data)- (#echo(__LINE__)#)"); }

return ("<value><struct>
<member><name>faultCode</name><value>".($direct_classes['xml_bridge']->array2xml_item_encoder (array ("tag" => "int","value" => $f_data[0])))."</value></member>
<member><name>faultString</name><value>".($direct_classes['xml_bridge']->array2xml_item_encoder (array ("tag" => "string","value" => $f_data[1])))."</value></member>
</struct></value>");
	}

	//f// direct_web_service_xmlrpc->parse_xmlrpc ($f_data)
/**
	* Parse a value XML array tree.
	*
	* @param  array $f_data XML array tree
	* @uses   direct_datalinker::define_extra_attributes()
	* @uses   direct_datalinker::define_extra_conditions()
	* @uses   direct_datalinker::define_extra_joins()
	* @uses   direct_datalinker::get_subs()
	* @uses   direct_db::define_row_conditions_encode()
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @return array Array with pointers to the documents
	* @since  v0.1.00
*/
	public function parse_xmlrpc ($f_data)
	{
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->parse_xmlrpc (+f_data)- (#echo(__LINE__)#)"); }
		$f_return = NULL;

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
					if (isset ($f_data['data']['value']))
					{
						$f_data = $f_data['data']['value'];

						if (isset ($f_data['xml.mtree']))
						{
							unset ($f_data['xml.mtree']);
							$f_return = array ();

							foreach ($f_data as $f_entry)
							{
								if (isset ($f_entry)) { $f_return[] = $this->parse_xmlrpc ($f_entry); }
							}
						}
						else
						{
							$f_data = $this->parse_xmlrpc ($f_data);
							if (isset ($f_data)) { $f_return = array ($f_data); }

							break 1;
						}
					}

					break 1;
				}
				case "member":
				{
					if (isset ($f_data['name']['value'],$f_data['value'])) { $f_return = array ("name" => $f_data['name']['value'],"value" => $this->parse_xmlrpc ($f_data['value'])); }
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
								$f_entry_array = $this->parse_xmlrpc ($f_entry_array);
								if (is_array ($f_entry_array)) { $f_return[$f_entry_array['name']] = $f_entry_array['value']; }
							}
						}
						else
						{
							$f_entry_array = $this->parse_xmlrpc ($f_data);
							if (is_array ($f_entry_array)) { $f_return = array ($f_entry_array['name'] => $f_entry_array['value']); }
						}
					}

					break 1;
				}
				case "value":
				{
					$f_data = array_pop ($f_data);
					$f_data = $this->parse_xmlrpc ($f_data);
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
			case "string":
			case "value":
			{
				$f_return = $f_data['value'];
				break 1;
			}
			}
		}

		return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->parse_xmlrpc ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
	}

	//f// direct_web_service_xmlrpc->response ()
/**
	* Returns the XML-RPC response.
	*
	* @uses   direct_class_init()
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @since  v0.1.00
*/
	public function response ()
	{
		global $direct_classes,$direct_settings,$direct_local;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->response ()- (#echo(__LINE__)#)"); }

		if (!isset ($direct_classes['output'])) { direct_class_init ("output"); }
		$direct_classes['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
		header ("Content-type: text/xml; charset=".$direct_local['lang_charset']);

		if ($this->data_multicall)
		{
			$f_result = "<params><param><value><array>";

			foreach ($this->data_result as $f_result_entry)
			{
				$f_result .= "<data>";
				$f_result .= ((is_array ($f_result_entry)) ? $this->parse_fault ($f_result_entry) : $f_result_entry);
				$f_result .= "</data>";
			}

			$f_result .= "</array></value></param></params>";
		}
		elseif (is_array ($this->data_result)) { $f_result = "<fault>".($this->parse_fault ($this->data_result))."</fault>"; }
		else { $f_result = "<params><param>".$this->data_result."</param></params>"; }

		echo "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodResponse>$f_result</methodResponse>";
	}

	//f// direct_web_service_xmlrpc->set ($f_data)
/**
	* Sets (and overwrites existing) XML-RPC result data.
	*
	* @param  array $f_data Response data
	* @uses   direct_debug()
	* @uses   direct_web_service_xmlrpc::parse()
	* @uses   USE_debug_reporting
	* @since  v0.1.00
*/
	public function set ($f_data = NULL)
	{
		global $direct_classes;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->set (+f_data)- (#echo(__LINE__)#)"); }

		if (isset ($f_data))
		{
			if ($this->data_multicall) { $this->data_result[] = "<value>".($this->parse ($f_data))."</value>"; }
			else { $this->data_result = "<value>".($this->parse ($f_data))."</value>"; }
		}
		elseif ($this->data_multicall) { $this->data_result[] = "<value><nil /></value>"; }
		else { $this->data_result = "<value><nil /></value>"; }
	}

	//f// direct_web_service_xmlrpc->set_fault ($f_resultcode,$f_resultstring = NULL)
/**
	* Sets a fault (and overwrites existing) XML-RPC result.
	*
	* @param  mixed $f_resultcode Result integer or array for predefined result
	*         definitions
	* @param  string $f_resultstring Error text
	* @uses   direct_debug()
	* @uses   USE_debug_reporting
	* @since  v0.1.00
*/
	public function set_fault ($f_resultcode,$f_resultstring = NULL)
	{
		global $direct_classes;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -web_service_xmlrpc_handler->set (+f_data)- (#echo(__LINE__)#)"); }

		if ((is_array ($f_resultcode))&&(count ($f_resultcode) == 2)&&(isset ($f_resultcode[0],$f_resultcode[1])))
		{
			if ($this->data_multicall) { $this->data_result[] = $f_resultcode; }
			else { $this->data_result = $f_resultcode; }
		}
		elseif (is_numeric ($f_resultcode))
		{
			if (!isset ($f_resultstring)) { $f_resultstring = $f_resultcode; }

			if ($this->data_multicall) { $this->data_result[] = array ($f_resultcode,$f_resultstring); }
			else { $this->data_result = array ($f_resultcode,$f_resultstring); }
		}
		else { $this->set_fault (direct_web_service_xmlrpc::$RESULT_500); }
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_direct_web_service_xmlrpc",true);

$direct_classes['@names']['web_service_xmlrpc'] = "direct_web_service_xmlrpc";
}

//j// EOF
?>