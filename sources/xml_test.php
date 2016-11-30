<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 23.11.16
 * Time: 0:12
 */

require_once "../classes/Application.php";

$app = new Application();

$app->load_schema('xsd', '../data/xsds/CC');
$app->load_schema('xml', '../data/custom_xmls');


$xml_table = $app->get_xml_table();
$msg_types = $app->get_message_types();

?>

<html>
<head>
    <link href="../lib/bootstrap4/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="../lib/css/xml_test.css" rel="stylesheet" type="text/css"/>

    <script type="text/javascript" src="../lib/jquery/jquery-3.1.1.js"></script>
    <script type="text/javascript" src="../lib/bootstrap4/js/bootstrap.js"></script>
    
    <script type="text/javascript" src="../lib/js/gen-param.lib.js"></script>
    <script type="text/javascript" src="../lib/js/animations/xml_test-anim.js"></script>
    <script type="text/javascript" src="../lib/js/redirects/xml_test-redir.js"></script>   
    <script type="text/javascript" src="../lib/js/main.js"></script>
    <script type="text/javascript" src="../lib/js/hints.js"></script>
</head>
<body>
<div id="alert_message" class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <text id="header_text" style="font-weight: bold"></text>
    <text id="body_text"></text>
    <?php if (!empty($app->errorMsg)) {
        foreach ($app->errorMsg as $message) {
            echo $message;
        }
    } ?>
</div>


<div id="main_block" class="card">
    <div id="main_block_header" class="card-header">
        VERSION v1.0a
        <button id="main_menu_btn" type="button" class="btn btn-outline-warning">Главное меню</button>
    </div>
    <div class="card-block">
        <h4 id="main_block_body_header" class="card-title">XML PARSER DEV</h4>

        <!--Поле для вставки XML-->
        <div class="row">
            <div id="first_block" class="col-sm-6">
                <div class="card card-block">
                    <h4 class="card-title">STEP 1: Настройка XML <i id="xml_setup_help" class="fa fa-info-circle" aria-hidden="true"></i></h4>
                    <p class="card-text">Отформатируйте XML перед загрузкой!</p>
                    <div class="form-group has-warning" id="input_xml_block">
                        <label class="form-control-label" for="inputWarning1">Вставьте отформатированную XML для проверки в блок ниже</label>
                        <textarea id="xml_input_area" class="form-control form-control-warning" rows="15" cols="45" name="text" placeholder="XML вставлять сюда"></textarea>
                        <div class="form-control-feedback">Теги должны располагаться строго по одному на строку!</div>
                        <small class="form-text text-muted">Важная подсказка!</small>
                    </div>            
                    <!--<button id="cheat_button" type="button" class="btn btn-outline-info btn-sm">Загрузить эталонку</button>-->          
                </div>
            </div>

            <!--Таблица со списком xml-->
            <div class="col-sm-6">
                <div class="card card-block" id="xml_table_list_block">
                    <h4 class="card-title">STEP 2: Выберите эталонную XML <i id="xml_table_help" class="fa fa-info-circle" aria-hidden="true"></i></h4>
                    <table class="table table-bordered table-hover" id="xml_table_list">
                        <thead>
                        <tr class="no-clickable">
                            <th>#</th>
                            <th><i class="fa fa-calendar" aria-hidden="true"></i> Версия файла</th>
                            <th><i class="fa fa-code" aria-hidden="true"></i> Тип</th>
                            <th><i class="fa fa-file-text-o" aria-hidden="true"></i> Имя</th>
                            <th><i class="fa fa-check-circle" aria-hidden="true"></i> Выбрать</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $xml_table; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--Блок с SELECT для выбора типа сообщения-->
        <div class="row">
            <div class="col-sm-6">
                <div id="select_message_block" class="card card-block">
                    <h4 class="card-title">STEP 3: Выбор XSD схемы <i id="select_xsd_help" class="fa fa-info-circle" aria-hidden="true"></i></h4>
                    <p class="card-text">Выберите XSD схему, если хотите использовать валидацияю по XSD</p>
                    <form>
                        <div class="form-group">
                            <label for="exampleSelect1">Тип</label>
                            <select id="message_type_select" class="form-control" id="exampleSelect1">
                                <option>Не использовать XSD</option>
                                <?php echo $msg_types; ?>
                            </select>
                        </div>
                    </form>
                    <!--<button id="setup_xsd" type="button" class="btn btn-outline-info btn-sm">Настройка XSD</button>-->
                </div>
            </div>

            <!--Блок для выбора параметров тестирования-->
            <div class="col-sm-6">
                <div id="select_test_params_block" class="card card-block">
                    <h4 class="card-title">STEP 4: Параметры тестирования <i id="test_param_help" class="fa fa-info-circle" aria-hidden="true"></i></h4>
                    <p class="card-text">Отметьте необходимые параметры для тестирования.</p>
                    <form id="test_params_form">                     
                        <!--Генерация происходит в файле: gen-param.lib.js-->    
                    </form>
                </div>
            </div>
        </div>

        <div id="prepare_for_start" class="card card-block text-xs-center">
            <h4 class="card-title">Все готово для начала тестирования!</h4>
            <p class="card-text">Сядьте поудобнее, заварите кофе, поставьте рядом печеньки и приготовьтесь... Что произойдет дальше, никому неизвестно...</p>
            <hr>
            <button id="launch_test" type="button" class="btn btn-success" disabled>Начать тестирование</button>
        </div>
        
        <!--КОНЕЦ: Блок для выбора параметров тестирования-->

        <!--Блок Вывода информации о результатах валидации-->
        <div id="validation_results_block" class="card card-block">

            <h4 id="TEST_HEADER"      class="card-title">Результаты тестирования</h4>
            <p  id="TEST_DESCRIPTION" class="card-text">Тут можно выводить описание тестирования.</p>

        </div>
        <!--КОНЕЦ: Блок Вывода информации о результатах валидации-->
    </div>


    <div class="card-footer text-muted">
        2 days ago
    </div>
</div>

<!--Окно со всплывающийся настройками XSD-->
<div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 id="info_header_text" class="modal-title" id="myModalLabel">Не настроеное сообщение!</h4>
            </div>
            <div id="info_body_text" class="modal-body">
                Исправте этот текст функцией show_model_info в main.js!
            </div>
            <div id="info_footer" class="modal-footer">
               
            </div>
        </div>
    </div>
</div>
<!--КОНЕЦ: Окно со всплывающийся настройками XSD-->
</body>
</html>


