<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 23.11.16
 * Time: 0:12
 */
?>

<html>
<head>
    <link href="lib/bootstrap4/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>

    <script type="text/javascript" src="lib/jquery/jquery-3.1.1.js"></script>
    <script type="text/javascript" src="lib/bootstrap4/js/bootstrap.js"></script>

    <script type="text/javascript" src="lib/js/main.js"></script>
</head>
<body>
<div class="card" style="width: 80%;height: auto;margin: 50px auto;">
    <div class="card-header" style="text-align: center">
        VERSION v1.0a
    </div>
    <div class="card-block">
        <h4 class="card-title" style="text-align: center">XML PARSER DEV</h4>

        <div class="card card-block" style="width: 100%">
            <h4 class="card-title">Выберите способ загрузки XML</h4>
            <p class="card-text">Выберите XML для загрузки из списка ниже, или укажить путь до неё.</p>

            <button type="button" class="btn btn-primary">Загрузить из файла...</button>
            <button type="button" class="btn btn-outline-info" style="float: right">Загрузить эталонку</button>
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
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                    </tr>
                    </tbody>
                </table>

                <!--Постраничная навигация внизу таблицы-->
                <nav aria-label="Page navigation"">
                <ul class="pagination" style="margin: auto;>
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
        <div class="card card-block" style="width: 100%">
            <h4 class="card-title">Выберите тип сообщения</h4>
            <p class="card-text">Выберите тип сообщения, соответствующий выбранной выше XML.</p>
            <form>
                <div class="form-group" style="width: 35%">
                    <label for="exampleSelect1">Тип</label>
                    <select class="form-control" id="exampleSelect1">
                        <option></option>
                        <option>ENT_ANKETA_REQUEST_DATA_RS</option>
                        <option>ENT_ANKETA_REQUEST_DATA_RQ</option>
                        <option>AGREEMENT_RS</option>
                        <option>AGREEMENT_RQ</option>
                    </select>
                </div>
            </form>
        </div>

        <!--Блок для выбора параметров тестирования-->
        <div class="card card-block" style="width: 100%">
            <h4 class="card-title">Параметры тестирования</h4>
            <p class="card-text">Отметьте необходимые параметры для тестирования.</p>
            <form>
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
                <button type="button" class="btn btn-success" disabled>Начать тестирование</button>
            </form>
        </div>

    </div>



    <div class="card-footer text-muted">
        2 days ago
    </div>
</div>
</body>
</html>


