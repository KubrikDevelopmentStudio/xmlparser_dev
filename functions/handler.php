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

require_once "../classes/Application.php";

$app = new Application();

$app->load_schema('xsd', '../data/xsds/CC');
$app->load_schema('xml', '../data/custom_xmls');

$xml_table = $app->get_xml_table();
$msg_types = $app->get_message_types();


$all_params = 0;
$current_param = 0;

switch ($_REQUEST['action']) {
    case 'validateXml':

        $xml               = $_REQUEST['xml_doc']           ? $_REQUEST['xml_doc']           : null;
        $xml_comparer_text = $_REQUEST['xml_comparer_text'] ? $_REQUEST['xml_comparer_text'] : null;
        $xsd               = $_REQUEST['xsd_doc']           ? $_REQUEST['xsd_doc']           : null;
        $params            = $_REQUEST['test_params']       ? $_REQUEST['test_params']       : null;
        
        $all_params = count($params);
        
        if(isset($xml) && (isset($xsd) || isset($xml_comparer_text))) {
            $global_data = [];
            foreach ($params as $param) {
                switch ($param) {
                    /**
                     * Стандартный тест валидирования.
                     */
                    case 'DEFAULT':
                        $current_param++;                      

                        $validResult = default_validation($xml, $xsd);

                        $data = [
                            'test_caption' => "Валидация по XSD схеме",
                            'test_description' => "Для проверки используется функция schemaValidate()",
                        ];

                        $data = array_merge($data, $validResult);
                        $global_data[] = array_merge($global_data, $data);

                        if($current_param === $all_params) {
                            print_r(json_encode($global_data));
                        }

                        /*die();*/
                        
                        break;

                    case 'MY_TEST':
                        $current_param++;

                        $validResult = custom_validation2($xml, $xml_comparer_text);

                        $temp_arr = [];
                        $temp_arr =  array_merge($validResult['NOT_FOUND_TAGS'], $validResult['MISEPLACED_TAGS']);
                        
                        sort($temp_arr);

                        $data = [
                            'test_caption' => "мой тестик",
                            'test_description' => "На пол-Фёдора работающий тест.",
                            
                            'all_tags_in_first_xml' => $validResult['TAGS_COUNT_IN_XML'],
                            'all_tags_in_comparer_xml' => $validResult['TAGS_COUNT_IN_COMPARER_XML'],
                            
                            'errorList' => $temp_arr
                        ];

                        $global_data[] = array_merge($global_data, $data);

                        if($current_param === $all_params) {
                            print_r(json_encode($global_data));
                        }

                        /*die();*/

                        break;
                }
            }
        }
        die();

        break;

        case 'getXSDschemas':
            $has_values = (!empty($msg_types) && $msg_types !== null) ? true : false;
            $data = [
                'has_values' => $has_values,
                'xsd_table' => $msg_types
            ];

            print_r(json_encode($data));
            die();
            break;

        case 'getXMLtables':
            $has_values = (!empty($xml_table) && $xml_table !== null) ? true : false;
            $data = [
                'has_values' => $has_values,
                'xml_table' => $xml_table
            ];

            print_r(json_encode($data));
            die();
            break;
}


/**
*    Читаем указанный файл в массив тримируя пробелы на каждой строчке.
*/
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


/**
*    Получение имени тега между скобок <tag>
*/
function getTagName($tag) {
    $_match;
    if(preg_match('/(?=\<)(.*?)(?=\>)/', $tag, $_match)) {
        $tag_name = preg_replace('/</', '', $_match);

        /*Костыль на обрезание закрывающего слеша, проблема в том,
          если тег имеет вид <tag/>, он по сути является (пустым или сгенерился неверно), 
          нужно это отловить, но не здесь! Как-то выяснить это при присваении тегу значения TAG_VALUE,
          чтобы правильно потом обработать тег при парсинге.*/
        $index = stripos($tag_name[0], '/');

        if($idnex !== false) {
            if($index == 0) {
                $tag_name[0] = substr($tag_name[0], $index);
                return $tag_name[0];
            } else {
                $tag_name[0] = substr($tag_name[0], 0, $index);
                return $tag_name[0];
            }
        }

        return is_array($tag_name) ? $tag_name[0] : $tag_name;
    } else {
        return null;
    }
}


/**
*    Получение значение тега между скобок <tag>tag_value</tag>
*/
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
        /*return "OPEN_TAG";*/
        if(preg_match('/\//', $tag)) {
            return "CLOSE_TAG";
        } else {
            return "OPEN_TAG";
        }
    }
}


/**
*    Получени родителя тега.
*
*    Необходимо передать массив с XML и тег, родителя которого необходимо получить.
*/
function getTagParents($xml_values, $line, $all_parents = false) {
    if($all_parents) {
        $tag_parents = "";
        for($i = 0; $i < $line - 1; $i++) {
            if($xml_values[$i]['TAG_VALUE'] === 'OPEN_TAG') {
                $tag_parents .= $xml_values[$i]['TAG_NAME'] . "/";
            }
        }
        return $tag_parents;
    } else {
        for($i = $line - 1; $i >= 0; $i--) {
            if($xml_values[$i]['TAG_VALUE'] === 'OPEN_TAG') {
                return $xml_values[$i]['TAG_NAME'];
            }
        }
    }
}


/**
*    Сохранение переданной в виде текста XML в temp файл с уникальным именем.
*/
function save_to_file($text) {
    $file_name = uniqid();
    $full_file_name = "../data/temp/" . $file_name . ".xml";

    $result = file_put_contents("../data/temp/" . $file_name . ".xml", $text);
  
    return $result !== false ? $full_file_name : false; 
}


/**
*    Моя кастомная валидация
*/
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

    $count = count($strings1);
    //Значения тегов из эталонной xml
    $first_xml_values = [];

    //Значения тегов из xml, которую проверяем
    $second_xml_values = [];
    
    /*Получение тегов и значений из всех XML*/
    for($i = 0; $i < $count; $i++) {

        //Получаем тег и его значение из эталонной xml.     
        if(($tag = getTagName($strings1[$i])) !== null) {
            if(($value = getTagValue($strings1[$i])) !== null) {
                $first_xml_values[] = [
                    "TAG_NAME" => $tag,     /*Имя тега*/
                    "TAG_VALUE" => $value,  /*Значение тега*/
                    "TAG_PARENT" => getTagParents($first_xml_values, $i + 1),
                    "LINE" => $i + 1        /*На какой строке находится*/
                ];
            }
        }

        //Получаем тег и его значение из проверяемой xml.
        if(($tag = getTagName($strings2[$i])) !== null) {
            if(($value = getTagValue($strings2[$i])) !== null) {
                $second_xml_values[] = [
                    "TAG_NAME" => $tag,     /*Имя тега*/
                    "TAG_VALUE" => $value,  /*Значение тега*/
                    "TAG_PARENT" => getTagParents($second_xml_values, $i + 1),
                    "LINE" => $i + 1        /*На какой строке находится*/
                ];
            }
        }       
    }

    /*Отсутствующие теги*/
    $difference = [];

    /*теги, находящиеся не на своих местах*/
    $misplaced = [];

    /*Текущий тег*/
    $curr_tag;

    /*Начало алгоритма сравнения XML*/
    for($i = 0; $i < $count; $i++) {       

        /*Берем инфу по текущему тегу*/ 
        $full_tag  = $first_xml_values[$i];

        $tag_name   = $first_xml_values[$i]['TAG_NAME'];
        $tag_value  = $first_xml_values[$i]['TAG_VALUE']; 
        $tag_parent = $first_xml_values[$i]['TAG_PARENT'];
        $line       = $first_xml_values[$i]['LINE']; 

        $tag_exists_bool = false;
        $tag_value_bool  = false;

        $tag_value_found = "";
        $tag_parent_found = "";

        /*Проверка на наличие текущего тега в проверяемой XML.
          Если тег найден, идет проверка на соответствие значения тегов.*/
        for($f_index = 0; $f_index < count($second_xml_values); $f_index++) {
            if($tag_name === $second_xml_values[$f_index]['TAG_NAME']) {
                //print_r($tag_name . " == " . $second_xml_values[$f_index]['TAG_NAME'] . "\n");
                $tag_exists_bool = true;

                for($s_index = 0; $s_index < count($second_xml_values); $s_index++) {
                    if($tag_name === $second_xml_values[$s_index]['TAG_NAME']) {
                        if($tag_parent === $second_xml_values[$s_index]['TAG_PARENT']) {
                            if($tag_value === $second_xml_values[$s_index]['TAG_VALUE']) {
                                $tag_value_bool = true;
                            } else {
                                $tag_value_found = $second_xml_values[$s_index]['TAG_VALUE'];
                                $tag_parent_found = $tag_parent;
                            }
                        } 
                    }
                    break;
                } 
                break;
            } else {
                //print_r($tag_name . " != " . $second_xml_values[$f_index]['TAG_NAME'] . "\n");
            }
        }
        

        /*Если небыл найден тег, генерируем сообщение об ошибке.*/
        if(!$tag_exists_bool) {
             $difference[] = [
                "ERROR_TYPE" => "ERROR",
                "HEADER" => "[Тег не найден]",
                "MESSAGE" => htmlspecialchars("Тег <" . $tag_name . "> не найден в проверяемой XML.") ,
                "LINE" => $i + 1
                ];  
        }

        /*Если найденные теги, имели разное значение, генерируем предупреждение.*/
        if(!$tag_value_bool && $tag_exists_bool) {
             $difference[] = [
                "ERROR_TYPE" => "WARNING",
                "HEADER" => "[Неверное значение]",
                "MESSAGE" => htmlspecialchars("Тег <" . $tag_name . "> в ветке <" . $tag_parent . "> имеет неверное значение '" . $tag_value_found . "', ожидаемое значение '" . $tag_value . "'") ,
                "LINE" => $i + 1
                ];  
        }


        /*for($j = 0; $j < count($second_xml_values); $j++) {
            if($tag_name === $second_xml_values[$j]['TAG_NAME']) {
                if($line !== $second_xml_values[$j]['LINE']) {
                    $found_line = $second_xml_values[$j]['LINE'];
                    $misplaced[] = [
                        "ERROR_TYPE" => "WARNING",
                        "HEADER" => "[Not correct line]",
                        "MESSAGE" => htmlspecialchars("Тег <" . $curr_tag['TAG_NAME'] . "> находится на неверной месте!  Ожидался на строке " 
                                        . $curr_tag['LINE'] . ", но обнаружен на " 
                                        . $second_xml_values[$j]['LINE']),
                        //"LINE" => $i + 1
                        ];
                        break;
                }
            }  
        }*/
            
        

       
    }   

    $all_tags_best = count($strings1);
    $all_tags_comparer = count($strings2);

    return [
       'TAGS_COUNT_IN_XML' => $all_tags_best,
       'TAGS_COUNT_IN_COMPARER_XML' => $all_tags_comparer,

       'NOT_FOUND_TAGS' => $difference,
       'MISEPLACED_TAGS' => $misplaced
    ];
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

    if (!$xmlDoc->schemaValidate($xsd)) {
        $error_body = libxml_display_errors();
    }

    $data = [
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
            $return['HEADER'] = "[Предупреждение] код: $error->code: ";
            break;

        case LIBXML_ERR_ERROR:
            $return['ERROR_TYPE'] = "ERROR";
            $return['HEADER'] = "[Ошибка] код: $error->code: ";
            break;

        case LIBXML_ERR_FATAL:
            $return['ERROR_TYPE'] = "FATAL_ERROR";
            $return['HEADER'] = "[Фатальная ошибка] код: $error->code: ";
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