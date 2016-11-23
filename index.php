<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 22.11.16
 * Time: 20:46
 */
?>

<html>
<head>
    <link href="lib/bootstrap4/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="lib/css/loader.css" rel="stylesheet" type="text/css"/>


    <script type="text/javascript" src="lib/jquery/jquery-3.1.1.js"></script>
    <script type="text/javascript" src="lib/bootstrap4/js/bootstrap.js"></script>

    <script type="text/javascript" src="lib/js/animations/loader-anim.js"></script>
    <script type="text/javascript" src="lib/js/redirects/loader-redir.js"></script>
</head>
<body>

<div id="loading_block" class="card">
    <div class="card-header">
        XML PARSER
    </div>
    <div class="card-block">
        <h4 id="load_header_text" class="card-title">Загрузка...</h4>
        <p id="load_content_text" class="card-text">Внимание! Это тестовая версия скрипта, в нем есть и предусмотрены ошибки.</p>
        <progress class="progress progress-info" value="1" max="100"></progress>
    </div>
</div>

<!--Окно со всплывающийся подсказкой-->
<div class="modal fade" id="module_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Добро пожаловать в XML PARSER!</h4>
            </div>
            <div class="modal-body">
                Наш супер парсер приветствует Вас! На данном этапе, Вам будет необходимо выбрать
                модуль, который необходимо загрузить. <br><hr>

                Модуль тестирования XML: <br>
                <ul>
                    <li>Позволяет проводить тестирование XML.</li>
                    <li>Загрузка в ручную из файла.</li>
                    <li>Автоматическая загрузка.</li>
                    <li>Выбор одного или нескольких вариантов тестирования.</li>
                    <li>Отображение результатов обработки.</li>
                </ul>

                <hr>

                Модуль создания XML: <br>
                <ul>
                    <li>Позволяет создавать XML.</li>
                    <li>Позволяет редактировать XML.</li>
                    <li>Удобный визуальный редактор.</li>
                    <li>Использование шаблонов.</li>
                    <li>Сохранение в файл.</li>
                </ul>

            </div>
            <div class="modal-footer">
                <button id="load_test_module_button" type="button" class="btn btn-outline-info">Тестирование XML</button>
                <button id="load_create_module_button" type="button" class="btn btn-outline-success">Создание XML</button>
            </div>
        </div>
    </div>
</div>
<!--Конец окна с подсказкой-->

</body>
</html>
