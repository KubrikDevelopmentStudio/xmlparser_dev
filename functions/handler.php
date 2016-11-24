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

            // Enable user error handling
            libxml_use_internal_errors(true);

            $xmlDoc= new DOMDocument();
            $xmlDoc->loadXML($xml);

            $has_errors = false;
            if (!$xmlDoc->schemaValidate($xsd)) {
                $has_errors = true;
                $error_body = libxml_display_errors();
            }

            $data = [
                'hasErrors' => $has_errors,
                'errorList' => $error_body
            ];

            print_r($data);
            die();
        }
        break;
}

function libxml_display_error($error)
{
    $return = "";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in $error->file";
    }
    $return .= " on line $error->line";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();

    $errArr = [];
    foreach ($errors as $error) {
        $errArr[] = libxml_display_error($error);
    }

    libxml_clear_errors();

    return $errArr;
}