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
* dataport/swgap/swg_xmlrpc.php
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

//j// Basic configuration

/* -------------------------------------------------------------------------
Direct calls will be honored with an "exit ()"
------------------------------------------------------------------------- */

if (!defined ("direct_product_iversion")) { exit (); }

//j// Script specific commands

if (USE_debug_reporting) { direct_debug (1,"sWG/#echo(__FILEPATH__)# _main_ (#echo(__LINE__)#)"); }

$direct_classes['basic_functions']->require_file ($direct_settings['path_system']."/classes/dhandler/swg_web_service_xmlrpc.php");
$g_request_array = ((direct_class_init ("web_service_xmlrpc")) ? $direct_classes['web_service_xmlrpc']->get () : NULL);

if (($direct_classes['kernel']->service_init_rboolean ())&&(is_array ($g_request_array)))
{
	$g_base_module = (isset ($direct_settings['dsd']['module']) ? $direct_settings['dsd']['module'] : NULL);
	$direct_classes['web_service_xmlrpc']->handle ($g_base_module);

	if (!$direct_classes['web_service_xmlrpc']->is_result_set ()) { $direct_classes['web_service_xmlrpc']->set_fault (direct_web_service_xmlrpc::$RESULT_500); }
	$direct_classes['web_service_xmlrpc']->response ();
}
else
{
	if (direct_class_init ("output")) { $direct_classes['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']); }
	header ("Content-type: text/xml; charset=".$direct_local['lang_charset']);

echo ("<?xml version='1.0' encoding='$direct_local[lang_charset]' ?><methodResponse><fault><value><struct>
<member><name>faultCode</name><value><int>-32600</int></value></member>
<member><name>faultString</name><value><string>[400] Bad Request</string></value></member>
</struct></value></fault></methodResponse>");
}

$direct_cachedata['core_service_activated'] = true;

//j// EOF
?>