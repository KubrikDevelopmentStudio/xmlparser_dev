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
 *
 * Возможен парсинг следующих полей:
 *
 * [ERROR_TYPE] [обязательно] - тип ошибки (warning, error, fatal).
 * [HEADER]     [обязательно] - заголовок, будет выделен жирным шрифтом при отображении на странице.
 * [MESSAGE]    [обязательно] - текст с описанием ошибки.
 * [LINE]       [обязательно] - строка, на которой обнаружена ошибка.
 * [FILE]       [не обязательно] - файл, в котором обнаружена ошибка.
 *
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

                    case 'TEST':
                        $data = custom_validation($xml, $xsd);

                        print_r($data);
                        die();

                        break;

                    case 'MY_TEST':
                        $data = custom_validation2($xml, $xsd);

                        print_r($data);
                        die();

                        break;
                }
            }
        }
        break;
}


$data2 = [];
// Функция выполняющая рекурсивный спуск по массиву
function recursion($arr)
{
    global $data2;
    if(is_array($arr))
    {
        for($i=0; $i<count($arr); $i++)
        {
            if(is_array($arr[$i]))
            {
                recursion($arr[$i]);
            }
            else
            {
                $data2[] = $arr[$i];
            }
        }
    }
    else
    {
        $data2[] =  $arr;
    }
}

$recArr = [];
// Функция выполняющая рекурсивный спуск по массиву
function recursionKeys($arr)
{
    global $recArr;
    if(is_array($arr))
    {
        $recArr[] = array_keys($arr);
        for($i=0; $i<=count($arr); $i++)
        {
            if(is_array($arr[$i]))
            {
                recursionKeys($arr[$i]);
            }
        }
    }
}

function custom_validation2($xml, $xsd) {

    global $data2;
    global $recArr;

    $firstXML  = simplexml_load_file($xml);
    $secondXML  = simplexml_load_file($xsd);

    $aaa = ["s"=>"2", "q"=>"f"];

//    $recArr[] = array_keys([
//       '1' => [
//           '1_1' => "val_1_1",
//           '1_2' => [
//               '1_2_1' => "val_1_2_1",
//               '1_2_2' => "val_1_2_2"
//           ]
//       ]
//    ]);



//    recursionKeys([
//        'dsa' =>"dsa2",
//        'sdfdf' => "4342"
//    ]);


    recursionKeys([
       '1' => [
           '1_1' => "val_1_1",
           '1_2' => [
               '1_2_1' => "val_1_2_1",
               '1_2_2' => "val_1_2_2"
           ]
       ]
    ]);


    return [
      'keys' => $recArr,
        'xml' => $data2
    ];




    /*if(!file_exists($xml)) {
        return [
            'hasErrors' => true,
            'errorList' => "File not found."
        ];
    }

    //XML document
    $xmlDoc = [];

    //XML nodes
    $xmlNode = [];

    //CurrentNodeLevel
    $currNodelevel = 0;

    $handle = @fopen($xml, "r");
    if ($handle) {
        $xmlDoc[] = $xmlNode;
        while (($buffer = fgets($handle)) !== false) {
            $match = [];
            if(preg_match_all('/<(.*?)>/', $buffer, $match)) {
                if(!in_array($match[0], $xmlNode)) {
                    $xmlNode[$currNodelevel++] = $match[0];
                }
                return [
                  'xmlDoc' => $xmlNode
                ];
            }

        }

        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }*/
}


function custom_validation($xml, $xsd) {

    //Включаем информацию об ошибках.
    libxml_use_internal_errors(true);

    $doc = new XMLReader();
    $doc->open($xml);

    $nodes = [];
    while($doc->read()) {
        $mathc=[];
        $node = $doc->readOuterXml();
        if(preg_match_all('/<(.*?)>/', $node, $mathc)) {
            if(count($mathc[0]) == 2) {
               $nodes[] = $node;
            }
        } else {
            $node = str_replace(array("\r\n", "\r", "\n"), '',  trim($node));
            if(!preg_match('/<(.*?)>/', $node) && $node != "") {
                $nodes[count($nodes)][] = $node;
            }
        }

    }
    $doc->close();




//    $doc->setParserProperty(XMLReader::NONE, true);
//    $doc->setParserProperty(XMLReader::ELEMENT, true);
//    $doc->setParserProperty(XMLReader::ATTRIBUTE, true);
//    $doc->setParserProperty(XMLReader::TEXT, true);
//    $doc->setParserProperty(XMLReader::CDATA, true);
//    $doc->setParserProperty(XMLReader::ENTITY_REF, true);
//    $doc->setParserProperty(XMLReader::ENTITY, true);
//    $doc->setParserProperty(XMLReader::PI, true);
//    $doc->setParserProperty(XMLReader::COMMENT, true);
//    $doc->setParserProperty(XMLReader::DOC, true);
//    $doc->setParserProperty(XMLReader::DOC_TYPE, true);
//    $doc->setParserProperty(XMLReader::DOC_FRAGMENT, true);
//    $doc->setParserProperty(XMLReader::NOTATION, true);
//    $doc->setParserProperty(XMLReader::WHITESPACE, true);
//    $doc->setParserProperty(XMLReader::SIGNIFICANT_WHITESPACE, true);
//    $doc->setParserProperty(XMLReader::END_ELEMENT, true);
//    $doc->setParserProperty(XMLReader::END_ENTITY, true);
//    $doc->setParserProperty(XMLReader::XML_DECLARATION, true);

    return [
        'valid' =>$doc->isValid(),
        'errors' =>$error_body = libxml_display_errors(),
        'nodes' => $nodes

    ];

    if(!$doc->isValid()) {
        $has_errors = true;
        $error_body = libxml_display_errors();
    } else {
        $has_errors = false;
    }

    $data = [
        'hasErrors' => $has_errors,
        'errorList' => $error_body,
    ];

    return $data;
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