<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 23.11.16
 * Time: 0:12
 */

    require_once "../classes/Application.php";

    $app = new Application();

    $app->load_schema('xsd', '../data/xsds');
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

    <script type="text/javascript" src="../lib/js/animations/xml_test-anim.js"></script>
    <script type="text/javascript" src="../lib/js/redirects/xml_test-redir.js"></script>
    <script type="text/javascript" src="../lib/js/main.js"></script>
</head>
<body>

<?php if(!empty($app->errorMsg)): ?>
    <div id="alert_message" class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php foreach ($app->errorMsg as $message) {
            echo $message;
        } ?>
    </div>
<?php endif; ?>

<div id="main_block" class="card">
    <div id="main_block_header" class="card-header">
        VERSION v1.0a
        <button id="main_menu_btn" type="button" class="btn btn-outline-warning">Главное меню</button>
    </div>
    <div class="card-block">
        <h4 id="main_block_body_header" class="card-title">XML PARSER DEV</h4>

        <div class="card card-block">
            <h4 class="card-title">Выберите способ загрузки XML</h4>
            <p class="card-text">Выберите XML для загрузки из списка ниже, или укажить путь до неё.</p>

            <button id="load_from_file_btn" class="btn btn-primary">Загрузить из файла...</button>

            <button id="cheat_button" type="button" class="btn btn-outline-info btn-sm">Загрузить эталонку</button>
            <br><br>

            <!--Таблица со списком xml-->
            <div class="card card-block">
                <h4 class="card-title">Существующие XML</h4>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Версия файла</th>
                        <th>Тип</th>
                        <th>Имя</th>
                        <th><i class="fa fa-check-circle fa-2x" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php echo $xml_table; ?>
                    </tbody>
                </table>

                <!--Постраничная навигация внизу таблицы-->
                <nav aria-label="Page navigation"">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
                </ul>
                </nav>
            </div>
        </div>

        <!--Блок с SELECT для выбора типа сообщения-->
        <div id="select_message_block" class="card card-block">
            <h4 class="card-title">Выбор XSD схемы</h4>
            <p class="card-text">Выберите XSD схему, для валидирования выбранной выше XML.</p>
            <form>
                <div class="form-group">
                    <label for="exampleSelect1">Тип</label>
                    <select id="message_type_select" class="form-control" id="exampleSelect1">
                        <option></option>
                        <?php echo $msg_types; ?>
                    </select>
                </div>
            </form>
            <button id="setup_xsd" type="button" class="btn btn-outline-info btn-sm">Настройка XSD</button>
        </div>

        <!--Блок для выбора параметров тестирования-->
        <div id="select_test_params_block" class="card card-block">
            <h4 class="card-title">Параметры тестирования</h4>
            <p class="card-text">Отметьте необходимые параметры для тестирования.</p>
            <form id="test_params_form">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">
                        Первый параметр
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">
                        Второй параметр
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">
                        Третий параметр
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">
                        Четвертый параметр
                    </label>
                </div>
                <hr>
                <button id="launch_test" type="button" class="btn btn-success" disabled>Начать тестирование</button>
            </form>
        </div>

    </div>



    <div class="card-footer text-muted">
        2 days ago
    </div>
</div>
</body>
</html>


