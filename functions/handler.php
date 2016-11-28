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
 *(
 *   [hasErrors] => 1
 *   [errorList] => Array
 *       (
 *           [0] => Array
 *               (
 *                   [ERROR_TYPE] => FATAL_ERROR
 *                   [HEADER] => Fatal error 4:
 *                   [MESSAGE] => Start tag expected, '<' not found
 *                   [LINE] =>  на строке 1
 *                   )
 *
 *           [1] => Array
 *              (
 *                  [ERROR_TYPE] => ERROR
 *                  [HEADER] => Error 1872:
 *                  [MESSAGE] => The document has no document element.
 *                  [LINE] =>  на строке -1
 *              )
 *      )
 *) 
 */

switch ($_REQUEST['action']) {

    case 'validateXml':

        $xml               = $_REQUEST['xml_doc'] ? $_REQUEST['xml_doc'] : null;
        $xml_comparer_text = $_REQUEST['xml_comparer_text'] ? $_REQUEST['xml_comparer_text'] : null;
        $xsd               = $_REQUEST['xsd_doc'] ? $_REQUEST['xsd_doc'] : null;
        $params            = $_REQUEST['test_params'] ? $_REQUEST['test_params'] : null;

        if(isset($xml) && (isset($xsd) || isset($xml_comparer_text))) {

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
                        $validResult = custom_validation2($xml, $xml_comparer_text);

                        $data = [
                            'hasErrors' => "1",
                            'errorList' => array_merge($validResult['NOT_FOUND_TAGS'],
                                                       $validResult['MISEPLACED_TAGS'])
                        ];

                        // $data = $validResult;

                        print_r(json_encode($data));
                        die();

                        break;
                }
            }
        }
        break;
}



// Функция выполняющая рекурсивный спуск по массиву
function xml2array($arr)
{
    if(is_array($arr))
    {
        for($i = 0; $i < count($arr); $i++) {
            if(is_array($arr[$i])) {
                recursion($arr[$i]);
            } else {
                return $arr[$i];
            }
        }
    } else {
        return $arr;
    }
}

$count = 0;
function readXmlDoc($doc_path) {
    $read_arr = [];
    $handle = @fopen($doc_path, "r");
    if ($handle) {
        while (($buffer = fgets($handle)) !== false) {
            if(!empty(trim($buffer)) || trim($buffer) != null) {
                $read_arr[] = trim($buffer);
            }
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
    return $read_arr;
}

function getTagName($tag) {
    $_match;
    if(preg_match('/(?=\<)(.*?)(?=\>)/', $tag, $_match)) {
        $tag_name = preg_replace('/</', '', $_match);
        return is_array($tag_name) ? $tag_name[0] : $tag_name;
    } else {
        return null;
    }
}

function getTagValue($tag) {
    $_match = [];
    if(preg_match_all('/(?=\>)(.*?)(?=\<)/', $tag, $_match)) {
        $value = preg_replace('/>/', '', $_match[0]);
        if($value !== "" && $value[0] !== null) {
            return $value[0];
        } else {
            return "EMPTY_TAG";
        }
    } else {
        return "OPEN_TAG";
    }
}

function save_to_file($text) {
    $file_name = uniqid();
    $full_file_name = "../data/temp/" . $file_name . ".xml";

    $resut = file_put_contents("../data/temp/" . $file_name . ".xml", $text);
  
    return $result !== false ? $full_file_name : false; 
}

function custom_validation2($xml, $xml_comparer_text) {

    $temp_file_name = save_to_file($xml_comparer_text);

    $strings1 = "";
    $strings2 = "";

    if($temp_file_name !== false) {
         $strings1 = readXmlDoc($xml);
         $strings2 = readXmlDoc($temp_file_name);      
    } else {
       $data = [
            'hasErrors' => "1",
            'errorList' => "Could't create temp file! Check access rights!",
        ];

        return $data;
    }

    $count = 0;
    $priority = 0;
    if(count($strings1) == count($strings2)) {
        $count = count($strings1);
    } else if(count($strings1) > count($strings2)) {
        $count = count($strings2);
        $priority = 1;
    } else {
        $count = count($strings1);
        $priority = 2;
    }

    // Изменить вообще проверку: функцией in_array() искать все строки и в конце 
    // выводить инфу о том, какие не обнаружены и где именно! Будет топово)

    //Значения тегов из эталонной xml
    $first_xml_values = [];

    //Значения тегов из xml, которую проверяем
    $second_xml_values = [];
    
    for($i = 0; $i < $count; $i++) {

        //Получаем тег и его значение из эталонной xml.     
        if(($tag = getTagName($strings1[$i])) !== null) {
            if(($value = getTagValue($strings1[$i])) !== null) {
                $first_xml_values[] = [
                    "TAG_NAME" => $tag,
                    "TAG_VALUE" => $value,
                    "LINE" => $i + 1
                ];
            }
        }

        //Получаем тег и его значение из проверяемой xml.
        if(($tag = getTagName($strings2[$i])) !== null) {
            if(($value = getTagValue($strings2[$i])) !== null) {
                $second_xml_values[] = [
                    "TAG_NAME" => $tag,
                    "TAG_VALUE" => $value,
                    "LINE" => $i + 1
                ];
            }
        }       
    }


    //Разница в документах.
    $difference = [];

    //Не на своем месте.
    $misplaced = [];

    $curr_tag;
    $search_tag = [];
    for($i = 0; $i < $count; $i++) {       
        //Если нашли разницу в тегах, получааем инфу по тегу и вставляем
        //в сообщение об ошибке. 
        $tag_name = $first_xml_values[$i]['TAG_NAME'];
        $tag_value = $first_xml_values[$i]['TAG_VALUE']; 
        $line = $first_xml_values[$i]['LINE']; 

        //Если тег вообще не найден
        // @todo:  Это условие выполнится так же, если не будет совпадение 
        // в значении! Если значения тегов отличаются, это условие тоже выполняется,
        //что неверно! Исправить!.
        if(!in_array($strings1[$i], $strings2)) {     
            $difference[] = [
                "ERROR_TYPE" => "error",
                "HEADER" => "Tag not found",
                "MESSAGE" => "The tag <" . $tag_name . "> with value: '" . $tag_value . "' not found in comparer XML" ,
                "LINE" => $i + 1
                ];  
               continue;               
        }


        if(($strings1[$i] !== $strings2[$i])) { 
             
            $curr_tag = $first_xml_values[$i];

            $found_line;

            for($j = 0; $j < count($second_xml_values); $j++) {
                if($curr_tag["TAG_NAME"] === $second_xml_values[$j]["TAG_NAME"]) {
                    $found_line = $second_xml_values[$j]['LINE'];
                }
            }

            $misplaced[] = [
                "ERROR_TYPE" => "warning",
                "HEADER" => "Not correct line",
                "MESSAGE" => "The tag <" . $tag_name . "> is not on the correct line! Exepted on line " 
                             . $line . ", but found on " 
                             . $found_line,
                "LINE" => $i + 1
                ];

                break;
        }
    }   

    $all_tags_best = count($strings1);
    $all_tags_comparer = count($strings2);

    return [
    //    'da' => $first_xml_values,
       'TAGS_COUNT_IN_XML' => $all_tags_best,
       'TAGS_COUNT_IN_COMPARER_XML' => $all_tags_comparer,

       'NOT_FOUND_TAGS' => $difference,
       'MISEPLACED_TAGS' => $misplaced
    ];
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


    return [
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