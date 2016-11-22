<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.11.16
 * Time: 19:57
 */

require_once "func.php";
require_once "../configuration/database.conf.php";

global $db_link;
$db_link = new mysqli();

switch ($_REQUEST['action']) {
    case 'checkUserExists':
        if(has($_REQUEST['input_login'])) {
            $db_link->connect($db_config['host_name'], $db_config['user_name'], $db_config['user_password'], $db_config['database']['name']);

            if($db_link) {
                $sql = "SELECT COUNT(*) as result FROM `USERS` WHERE USER_LOGIN = '" . $_REQUEST['input_login'] . "'";
                $res = $db_link->query($sql);

                $result = $res->fetch_assoc();
                

                $data = [
                  'input_login' => $_REQUEST['input_login'],
                  'result' => $result
                ];

                print_r(json_encode($data));
                $db_link->close();
            } else {
                die('Очко с бд: ' . $connect->error);
            }
        }
        break;
}