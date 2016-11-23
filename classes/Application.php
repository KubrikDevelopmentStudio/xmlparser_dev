<?php

/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 23.11.16
 * Time: 12:39
 */
class Application
{
    /**
     * @var array Массив с XSD схемами.
     */
    private $_schemasArr = [];


    /**
     * @var array Массив с XML файлами.
     */
    private $_xmlsArr = [];


    /**
     * @var array Информация о всех схемах.
     */
    private $_schemasType = [
        'xml' => [
            'type' => 'xml',
            'dir' => '../data/custom_xmls'
        ],

        'xsd' => [
            'type' => 'xsd',
            'dir' => '../data/xsds'
        ]
    ];


    public $errorMsg = [];
    /**
     * Загрузка схем, указанного формата.
     *
     * Список форматов и пример, находятся в массиве $schemasType.
     *
     * @param $schema_type Тип схемы.
     * @param $directory Путь к папке со схемами.
     * @return bool Результат выполнения.
     */
    public function load_schema($schema_type, $directory) {
        if (is_dir($directory)) {
            if ($dh = opendir($directory)) {
                while (($file = readdir($dh)) !== false) {

                    $name       = substr($file, 0, stripos($file, '.'));
                    $extension  = substr($file, stripos($file, '.') + 1);
                    $lastUpdate = date("F d Y H:i:s", fileatime($directory . "/" . $file));
                    $fullPath   = $directory . "/" . $file;

                    if(empty($name)) {
                        continue;
                    }

                    if($extension != 'xsd' && $extension != 'xml') {
                        $msg = "<strong>Внимание!</strong> Загружаемый <a href='". $fullPath . "' class='alert-link'>" . $file . "</a> имеет неверный формат: " . $extension . "! Проверьте файл! <br>";
                        $this->errorMsg[] = $msg;
                        continue;
                    }

                    switch ($schema_type) {
                        case 'xsd':
                            $this->_schemasArr[count($this->_schemasArr)] = [
                                'full_name' => $file,
                                'name' => $name,
                                'extension' => $extension,
                                'last_update' => $lastUpdate,
                                'full_path' => $fullPath
                            ];
                            break;

                        case 'xml':
                            $this->_xmlsArr[count($this->_xmlsArr)] = [
                                'full_name' => $file,
                                'name' => $name,
                                'extension' => $extension,
                                'last_update' => $lastUpdate,
                                'full_path' => $fullPath
                            ];
                            break;

                        default:
                            break;
                    }
                }
                closedir($dh);
            }
        } else return false;

        return true;
    }


    /**
     * Генерация таблицы с существующими XML.
     *
     * @return string
     */
    public function get_xml_table() {
        $html = "";
        $count = 1;
        for ($i = 0; $i < count($this->_xmlsArr); $i++) {
            $html .= "
            <tr data-path='" . $this->_xmlsArr[$i]['full_path'] . "'>.
                <th scope='row'>$count</th>
                <td>" . $this->_xmlsArr[$i]['last_update'] . "</td>               
                <td>" . $this->_xmlsArr[$i]['extension'] . "</td>               
                <td>" . $this->_xmlsArr[$i]['name'] . "</td>               
                <td></td>               
            </tr>
        ";
            $count++;
        }
        return $html;
    }


    /**
     * Генерация списка загруженных XSD.
     *
     * @return string
     */
    public function get_message_types() {
        $html = "";
        for ($i = 0; $i < count($this->_schemasArr); $i++) {
            $html .= "
                <option data-path='" . $this->_schemasArr[$i]['full_path'] . "'>" . $this->_schemasArr[$i]['name'] . "</option>
            ";
        }
        return $html;
    }
}