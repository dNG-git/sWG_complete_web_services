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
* Output handlers parse and convert data in a protocol specific manner.
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
use dNG\sWG\directOHandlerBasics,
    dNG\sWG\web\directHttpXmlrpc;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("CLASS_directOHandlerXmlrpc"))
{
/**
* "directOHandlerXmlrpc" is responsible for formatting content and displaying
* it as a XML-RPC reply.
*
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage web_services
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;mpl2
*             Mozilla Public License, v. 2.0
*/
class directOHandlerXmlrpc extends directOHandlerBasics
{
/**
	* @var string $data_result Result string
*/
	protected $data_result;
/**
	* @var boolean $setup_error True to ignore the request and output an internal
	*      error message.
*/
	protected $setup_error;

/* -------------------------------------------------------------------------
Extend the class
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directOHandlerXmlrpc)
	*
	* @since v0.1.01
*/
	public function __construct ()
	{
		global $direct_globals,$direct_settings;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -oHandler->__construct (directOHandlerXmlrpc)- (#echo(__LINE__)#)"); }

		if (!isset ($direct_globals['@names']['web_http_xmlrpc'])) { $direct_globals['basic_functions']->includeClass ('dNG\sWG\web\directHttpXmlrpc',2); }
		if (!isset ($direct_globals['web_http_xmlrpc'])) { direct_class_init ("web_http_xmlrpc"); }

		if ((isset ($direct_globals['web_http_xmlrpc']))&&($direct_globals['basic_functions']->includeClass ('dNG\sWG\directIHandlerXmlrpc',2)))
		{
			direct_class_init ("input",true);
			$direct_settings['ihandler'] = "xmlrpc";
			$this->setup_error = false;
		}
		else { $this->setup_error = true; }

/* -------------------------------------------------------------------------
My parent should be on my side to get the work done
------------------------------------------------------------------------- */

		parent::__construct ();

/* -------------------------------------------------------------------------
Informing the system about available functions
------------------------------------------------------------------------- */

		$this->functions['isResultSet'] = true;
		$this->functions['set'] = true;

/* -------------------------------------------------------------------------
Set up some variables
------------------------------------------------------------------------- */

		$this->data_result = NULL;
		if ($this->setup_error) { $this->outputSendError (directHttpXmlrpc::$RESULT_500); }
	}

/**
	* Returns true if a XML-RPC result is set.
	*
	* @return boolean True if set
	* @since  v0.1.00
*/
	public function isResultSet ()
	{
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -oHandler->isResultSet ()- (#echo(__LINE__)#)"); }

		if (((!$this->setup_error)&&($direct_globals['input']->multicallCheck ())&&(isset ($this->data_result,$this->data_result[$direct_globals['input']->multicallGet (true)])))||(!empty ($this->data_result))) { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -oHandler->isResultSet ()- (#echo(__LINE__)#)",:#*/true/*#ifdef(DEBUG):,true):#*/; }
		else { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -oHandler->isResultSet ()- (#echo(__LINE__)#)",:#*/false/*#ifdef(DEBUG):,true):#*/; }
	}

/**
	* We need some header outputs for redirecting, that's why there exists this
	* function
	*
	* @param  string $f_url The target URL
	* @param  boolean $f_use_current_url True for allowing the redirect to be
	*         cached
	* @since  v0.1.02
*/
	public function redirect ($f_url,$f_use_current_url = true)
	{
		global $direct_cachedata;
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -oHandler->redirect ($f_url,+f_use_current_url)- (#echo(__LINE__)#)"); }

		$direct_cachedata['output_pagetarget'] = direct_html_encode_special ($f_url);
		$direct_cachedata['output_redirect'] = (function_exists ("direct_linker") ? direct_linker ("optical",$direct_cachedata['output_pagetarget']) : $direct_cachedata['output_pagetarget']);

		if ($f_use_current_url)
		{
			$this->outputHeader ("HTTP/1.1","HTTP/1.1 303 See Other",true);
			$this->header (false);
		}
		else
		{
			$this->outputHeader ("HTTP/1.1","HTTP/1.1 301 Moved Permanently",true);
			$this->header (true);
		}

		$this->outputHeader ("Location",$f_url);
		$this->set ($f_url);
		$this->outputResponse ();

		$direct_cachedata['core_service_activated'] = true;
	}

/**
	* Sets (and overwrites existing) XML-RPC result data.
	*
	* @param  array $f_data Response data
	* @since  v0.1.00
*/
	public function set ($f_data = NULL)
	{
		global $direct_globals;
		if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -oHandler->set (+f_data)- (#echo(__LINE__)#)"); }

		$f_data = (isset ($f_data) ? $direct_globals['web_http_xmlrpc']->get ($f_data) : "<nil />");

		if ((!$this->setup_error)&&($direct_globals['input']->multicallCheck ()))
		{
			if (!isset ($this->data_result)) { $this->data_result = array (); }
			$this->data_result[$direct_globals['input']->multicallGet (true)] = "<value>$f_data</value>";
		}
		else { $this->data_result = "<value>$f_data</value>"; }
	}

/**
	* This function will actually send the prepared content and debug information
	* to user.
	*
	* @param string $f_title Valid XHTML page title
	* @since v0.1.01
*/
	public function outputResponse ($f_title = "",$f_headers = NULL)
	{
		global $direct_globals,$direct_local,$direct_settings;
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -oHandler->outputResponse (+f_title,+f_headers)- (#echo(__LINE__)#)"); }

		if (($this->setup_error)||($direct_globals['input']->multicallCheck (true)))
		{
			$this->outputHeader ("Content-Type","text/xml; charset=".$direct_local['lang_charset']);

			if ((!$this->setup_error)&&($direct_globals['input']->multicallCheck ()))
			{
				$f_result = "<params><param><value><array><data>";
				if (!isset ($this->data_result)) { $this->data_result = array (); }

				foreach ($this->data_result as $f_result_entry)
				{
					$f_result .= "<value><array><data>";
					$f_result .= ((is_array ($f_result_entry)) ? $direct_globals['web_http_xmlrpc']->getFault ($f_result_entry) : $f_result_entry);
					$f_result .= "</data></array></value>";
				}

				$f_result .= "</data></array></value></param></params>";
			}
			elseif (is_array ($this->data_result)) { $f_result = "<fault>".($direct_globals['web_http_xmlrpc']->getFault ($this->data_result))."</fault>"; }
			elseif (isset ($this->data_result)) { $f_result = "<params><param>".$this->data_result."</param></params>"; }
			else { $f_result = "<params><param><value>".($direct_globals['web_http_xmlrpc']->get ($this->output_content))."</value></param></params>"; }

			$this->output_data = "<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodResponse>$f_result</methodResponse>";
			parent::outputResponse (NULL,$f_headers);
		}
		else { $direct_globals['input']->multicallHandle (); }
	}

/**
	* There are 4 different types of errors. The behavior of
	* "outputSendError ()" ranges from a simple error message (continuing
	* with script) up to critical or fatal error messages (with the current
	* theme) and interrupting the process.
	*
	* @param string $f_type Defines the error type that needs to be managed.
    *        The following types are defined: "critical", "fatal", "login" or
    *        "standard". The default error type is "fatal".
	* @param string $f_error A key for localisation strings or an error message
	* @param string $f_extra_data More detailed information to track down the
	*        problem
	* @param string $f_error_position Position where the error occurred
	* @since v0.1.08
*/
	public function outputSendError ($f_error_type,$f_error = NULL,$f_extra_data = "",$f_error_position = "")
	{
		global $direct_cachedata,$direct_globals,$direct_settings;
		if (USE_debug_reporting) { direct_debug (3,"sWG/#echo(__FILEPATH__)# -oHandler->outputSendError (+f_error_type,$f_error,+f_extra_data,$f_error_position)- (#echo(__LINE__)#)"); }

		$f_continue_check = true;

		if ((isset ($direct_globals['kernel']))&&(direct_class_function_check ($direct_globals['kernel'],"serviceInit")))
		{
			$f_service_error = $direct_globals['kernel']->serviceInit ();

			if (!empty ($f_service_error))
			{
				$f_error = $f_service_error[0];
				$f_error_type = 500;
			}
		}
		else { direct_class_init ("basic_functions"); }

		if (isset ($direct_cachedata['output_error_extradata'])) { $f_continue_check = false; }
		elseif (isset ($f_error)) { $f_error = (((!preg_match ("#\W+#i",$f_error))&&(function_exists ("direct_local_get"))) ? direct_local_get ("errors_".$f_error) : $f_error); }

		if ($f_continue_check)
		{
			$f_error_array = NULL;

			if ((is_array ($f_error_type))&&(count ($f_error_type) == 2)&&(isset ($f_error_type[0],$f_error_type[1]))) { $f_error_array = $f_error_type; }
			elseif (is_numeric ($f_error_type))
			{
				if (!isset ($f_error)) { $f_error = "$f_error_type"; }
				$f_error_array = array ($f_error_type,$f_error);
			}
			elseif (isset ($f_error))
			{
				if ($f_error_type == "login") { $this->outputSendError (403,$f_error); }
				else { $this->outputSendError (500,$f_error); }
			}
			elseif ($f_error_type == "login") { $this->outputSendError (directHttpXmlrpc::$RESULT_403); }
			else { $this->outputSendError (directHttpXmlrpc::$RESULT_500); }

			if (isset ($f_error_array))
			{
				if ((!$this->setup_error)&&($direct_globals['input']->multicallCheck ()))
				{
					if (!isset ($this->data_result)) { $this->data_result = array (); }
					$this->data_result[$direct_globals['input']->multicallGet (true)] = $f_error_array;
				}
				else { $this->data_result = $f_error_array; }

				$direct_cachedata['core_service_activated'] = true;
				$this->outputResponse ();
			}
		}
		else { parent::outputSendError ("fatal",$f_error,$f_extra_data."<br /><br />Request terminated",$f_error_position); }
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directOHandlerXmlrpc",true);

//j// Script specific commands

global $direct_globals;
$direct_globals['@names']['output'] = 'dNG\sWG\directOHandlerXmlrpc';
}

//j// EOF
?>