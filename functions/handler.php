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
                        $temp_arr =  array_merge($validResult['NOT_FOUND_TAGS'], $validResult['MISEPLACED_TAGS'], $validResult['BROKEN_TAGS']);
                        
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
*   Функция возвращает инфомрацию о текущем теге.
*/
function get_tag_info($string, $line) {
    $tag_name;
    $tag_value;
    $tag_line = $line + 1;
    $tag_parent = "NOT_READY";
    $brkn_cls_tg = "";

    $_match;
    if(preg_match('/<(.*?)\/>/', $string, $_match)) {
        $tag_name = $_match[1];
        $tag_value = "ERROR_TAG";
        $tag_line = $line + 1;
    } else {
        if(preg_match('/<([^\/].*?)>/', $string, $_match)) {
             $tag_name = $_match[1];
             if(preg_match('/>(.*?)</', $string, $_match)) {
                 if(!empty($_match[1])) {
                     $tag_value = $_match[1];                   
                 } else {
                     $tag_value = "EMPTY_TAG";
                 }
                 if(preg_match('/<\/(.*?)>/', $string, $_match)) {
                     if(!empty($_match[1])) {
                         if(/*$tag_name !== $_match[1]*/stripos($tag_name, $_match[1] !== false)) {
                             $brkn_cls_tg = "TRUE";
                         } else {
                             $brkn_cls_tg = "FALSE";
                         }
                     } else {
                         $brkn_cls_tg = "TRUE";
                     }
                 }
             } else {
                 $tag_value = "OPEN_TAG";
             }
        } else if(preg_match('/<\/(.*?)>/', $string, $_match)) {
            $tag_name = $_match[1];
            $tag_value = "CLOSE_TAG";
            $brkn_cls_tg = "FALSE";
        }
    }

    return [
        'TAG_NAME' => $tag_name,
        'TAG_VALUE' => $tag_value,
        'BROKEN_CLOSE_TAG' => $brkn_cls_tg,
        'TAG_PARENT' => $tag_parent,
        'LINE' => $tag_line,
    ];
}


/**
*    Получени родителя тега.
*
*    Необходимо передать массив с XML и тег, родителя которого необходимо получить.
*/
function supreme_parents_serach($xml, $id) {
    //Полный проверяемый путь.
    $path;

    //Открытые "родители".
    $parents = [];

    for($i = 0; $i < $id; $i++) {
        if($xml[$i]['TAG_VALUE'] === 'OPEN_TAG') {
            $parents[] = $xml[$i]['TAG_NAME'];
        } elseif($xml[$i]['TAG_VALUE'] === 'CLOSE_TAG') {
            array_pop($parents);
        }
    }

    return implode('/', $parents);
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

function get_last_parents($parents_string, $count) {
    $parents_arr = explode('/', $parents_string);
    
    if(count($parents_arr) >= $count) {
        $last_parents = [];
        for($i = count($parents_arr) - $count; $i < count($parents_arr); $i++) {
            $last_parents[] = $parents_arr[$i];
        } 
    } else {
        $last_parents = [];
        for($i = count($parents_arr) - 1; $i >= 0; $i--) {
            $last_parents[] = $parents_arr[$i];
        }
    }

    return implode('/', $last_parents);
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
    } else if(count($strings1) < count($strings2)) {
        $count = count($strings1);
        $priority = 1;
    } else {
        $count = count($strings2);
        $priority = 2;
    }

    //Значения тегов из эталонной xml
    $first_xml_values = [];

    //Значения тегов из xml, которую проверяем
    $second_xml_values = [];
    
    /*Отсутствующие теги*/
    $difference = [];

    /*теги, находящиеся не на своих местах*/
    $misplaced = [];

    /*Сломанные теги (пример <tag/>)*/
    $broken_tags = [];

    /*Получение тегов и значений из всех XML*/
    for($i = 0; $i < $count; $i++) {
        $first_xml_values[]  = get_tag_info($strings1[$i], $i);
        $first_xml_values[$i]['TAG_PARENT'] = supreme_parents_serach($first_xml_values, $i);

        $second_xml_values[] = get_tag_info($strings2[$i], $i);  
        $second_xml_values[$i]['TAG_PARENT'] = supreme_parents_serach($second_xml_values, $i);

        if($second_xml_values[$i]['TAG_VALUE'] === 'ERROR_TAG') {
            $broken_tags[] = [
                'TAG_NAME' => $second_xml_values[$i]['TAG_NAME'],
                'TAG_PARENT' => get_last_parents($second_xml_values[$i]['TAG_PARENT'], 3),
                
                'LINE' => $second_xml_values[$i]['LINE'],
            ];           
        }           
    }

    /*Начало алгоритма сравнения XML*/
    for($i = 0; $i < $count; $i++) {       

        /*Берем инфу по текущему тегу*/ 
        $full_tag  = $first_xml_values[$i];
        //Имя тега
        $tag_name        = $first_xml_values[$i]['TAG_NAME'];
        // Значение тега
        $tag_value       = $first_xml_values[$i]['TAG_VALUE'];
        // Полный путь родителей тега
        $full_tag_parents = $first_xml_values[$i]['TAG_PARENT'];
  
        $last_parents = get_last_parents($full_tag_parents, 3);

        // Номер строки в файле
        $line            = $first_xml_values[$i]['LINE']; 

        $tag_exists_bool = false;
        $tag_value_bool  = false;

        $tag_value_found;
        $tag_parent_found;

        /*Проверка на наличие текущего тега в проверяемой XML.
          Если тег найден, идет проверка на соответствие значения тегов.*/

          for($first_ind = 0; $first_ind < count($second_xml_values); $first_ind++) {
              $second_xml_tag = $second_xml_values[$first_ind];

              if($tag_name === $second_xml_tag['TAG_NAME']) {
                  if($full_tag_parents === $second_xml_tag['TAG_PARENT']) {
                      $tag_exists_bool = true;
                      if($tag_value === $second_xml_tag['TAG_VALUE']) {
                          $tag_value_bool = true;
                          break;
                      } else {
                          $tag_value_found = $second_xml_tag['TAG_VALUE'];
                          $tag_parent_found = $last_parents;
                      }
                  }
              }
          }


        /*for($f_index = 0; $f_index < count($second_xml_values); $f_index++) {
            if($tag_name === $second_xml_values[$f_index]['TAG_NAME']) {
                $tag_exists_bool = true;

                for($s_index = $f_index; $s_index < count($second_xml_values); $s_index++) {
                    if($tag_name === $second_xml_values[$s_index]['TAG_NAME']) {
                        if($full_tag_parents === $second_xml_values[$s_index]['TAG_PARENT']) {
                            if($tag_value === $second_xml_values[$s_index]['TAG_VALUE']) {
                                $tag_value_bool = true;      
                                         
                            } else {
                                $tag_value_found = $second_xml_values[$s_index]['TAG_VALUE'];
                                $tag_parent_found = $full_tag_parents;
                            
                            }
                        } 
                    }

                } 

            } else {
                //print_r($tag_name . " != " . $second_xml_values[$f_index]['TAG_NAME'] . "\n");
            }
        }*/
        

        /*Если небыл найден тег, генерируем сообщение об ошибке.*/
        if(!$tag_exists_bool) {
             $difference[] = [
                "ERROR_TYPE" => "FATAL_ERROR",
                "HEADER" => "[Тег не найден]",
                "MESSAGE" => htmlspecialchars("Тег <" . $tag_name . "> не найден по пути <.../" 
                                                      . $last_parents . "> в проверяемой XML.") ,
                "LINE" => $i + 1
                ];  
        }

        /*Если найденные теги, имели разное значение, генерируем предупреждение.*/
        if(!$tag_value_bool && $tag_exists_bool) {
             $difference[] = [
                "ERROR_TYPE" => "WARNING",
                "HEADER" => "[Неверное значение]",
                "MESSAGE" => htmlspecialchars("Тег <" . $tag_name . "> в ветке <.../" 
                                                      . $last_parents . "> имеет неверное значение '" 
                                                      . $tag_value_found . "', ожидаемое значение '$tag_value'") ,
                "LINE" => $i + 1
                ];  
        }    
       
    }   

    $_broken_tags = [];
    for($i = 0; $i < count($broken_tags); $i++) {
            $_broken_tags[] = [
                "ERROR_TYPE" => "ERROR",
                "HEADER" => "[Неверный тег]",
                "MESSAGE" => htmlspecialchars("Тег <" . $broken_tags[$i]['TAG_NAME'] . "/>, находящийся: <.../" 
                                                      . $broken_tags[$i]['TAG_PARENT'] . "> является ошибочным, т.к. не имеет значения и верного закрывающегося формата."),
                "LINE" => $broken_tags[$i]['LINE'],
            ];
        }

    $all_tags_best = count($strings1);
    $all_tags_comparer = count($strings2);

    return [
       'TAGS_COUNT_IN_XML' => $all_tags_best,
       'TAGS_COUNT_IN_COMPARER_XML' => $all_tags_comparer,

       'NOT_FOUND_TAGS' => $difference,
       'MISEPLACED_TAGS' => $misplaced,

       'BROKEN_TAGS' => $_broken_tags
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