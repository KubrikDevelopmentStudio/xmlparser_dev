<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 23.11.16
 * Time: 21:00
 */

switch ($_REQUEST['action']) {

    case 'validateXml':

        $xml = $_REQUEST['xml_doc'] ? $_REQUEST['xml_doc'] : null;
        $xsd = $_REQUEST['xsd_doc'] ? $_REQUEST['xsd_doc'] : null;

        if(isset($xml) && isset($xsd)) {

            $_xml = new DOMDocument();
            $_xml->load($xml);

            $data = [
              'result' => $_xml->schemaValidate($xsd),
              'errors_list' => libxml_get_errors()
            ];

            print_r($data);
            die();
        }
        break;
}