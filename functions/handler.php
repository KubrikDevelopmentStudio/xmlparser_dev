<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 23.11.16
 * Time: 21:00
 */


/**
 * Вид ответа сообщения от сервера в формате JSON, после валидации XML.
 *
 * Именно такой формат парсится в JS для вывода на страницу с результатами.
 *
 *Array
(
    [hasErrors] => 1
    [errorList] => Array
        (
            [0] => Array
                (
                    [ERROR_TYPE] => FATAL_ERROR
                    [HEADER] => Fatal error 4:
                    [MESSAGE] => Start tag expected, '<' not found
                    [LINE] =>  на строке 1
                    )

            [1] => Array
                (
                    [ERROR_TYPE] => ERROR
                    [HEADER] => Error 1872:
                    [MESSAGE] => The document has no document element.
                    [LINE] =>  на строке -1
                )
        )
)
 */

switch ($_REQUEST['action']) {

    case 'validateXml':

        $xml    = $_REQUEST['xml_doc'] ? $_REQUEST['xml_doc'] : null;
        $xsd    = $_REQUEST['xsd_doc'] ? $_REQUEST['xsd_doc'] : null;
        $params = $_REQUEST['test_params'] ? $_REQUEST['test_params'] : null;

        if(isset($xml) && isset($xsd)) {

            foreach ($params as $param) {
                switch ($param) {
                    /**
                     * Стандартный тест валидирования.
                     */
                    case 'DEFAULT':

                        $data = default_validation($xml, $xsd);

                        print_r(json_encode($data));
                        die();

                        break;
                }
            }
        }
        break;
}
/**
 * Стандартный механизм валидирования.
 *
 * @param $xml
 * @param $xsd
 * @return array
 */
function default_validation($xml, $xsd) {

    //Включаем информацию об ошибках.
    libxml_use_internal_errors(true);

    $xmlDoc = new DOMDocument();
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

    return $data;
}


/**
 * Парсинг информации об ошибке.
 *
 * @param $error Конкретная ошибка.
 * @return string Возвращает информацию об ошибке.
 */
function libxml_display_error($error) {
    $return = [];
    switch ($error->level) {

        case LIBXML_ERR_WARNING:
            $return['ERROR_TYPE'] = "WARNING";
            $return['HEADER'] = "[WARNING] code: $error->code: ";
            break;

        case LIBXML_ERR_ERROR:
            $return['ERROR_TYPE'] = "ERROR";
            $return['HEADER'] = "[ERROR] code: $error->code: ";
            break;

        case LIBXML_ERR_FATAL:
            $return['ERROR_TYPE'] = "FATAL_ERROR";
            $return['HEADER'] = "[FATAL ERROR] code: $error->code: ";
            break;

    }

    $return['MESSAGE'] = trim($error->message);

    if ($error->file) {
        $return['FILE']= " в файле $error->file";
    }

    $return['LINE']= " на строке $error->line";

    return $return;
}


/**
 * Парсинг всех ошибок произошедших при валидации.
 *
 * @return array Возвращается массив с описанием всех ошибок.
 */
function libxml_display_errors() {
    $errors = libxml_get_errors();

    $errArr = [];
    foreach ($errors as $error) {
        $errArr[] = libxml_display_error($error);
    }

    libxml_clear_errors();

    return $errArr;
}