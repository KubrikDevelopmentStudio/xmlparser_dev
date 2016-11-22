<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.11.16
 * Time: 19:56
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

    <!--Форма авторизации-->
    <form enctype="multipart/form-data" method="post" action="">
        <div class="card text-xs-center" style="width: 20%;height: auto;margin: 150px auto;">

            <!--Заголовок окна-->
           <div class="card-header">
                V1.0
           </div>
           <div class="card-block" >

               <!--Заголовок формы-->
               <h4 class="card-title">Авторизация</h4>

               <!--Логин-->
                <div class="input-group">
                    <span class="input-group-addon" id="sizing-addon2">
                        <i class="fa fa-user-o" aria-hidden="true"></i>
                    </span>

                    <input type="text" name="userLogin" class="form-control" placeholder="Ваш логин" aria-describedby="sizing-addon2">
                </div>

                <br>

               <!--Пароль-->
                <div class="input-group">
                    <span class="input-group-addon" id="sizing-addon2">
                         <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                    <input type="password" name="userPass" class="form-control" placeholder="Ваш пароль" aria-describedby="sizing-addon2">
                </div>

                <br>

               <!--Кнопка войти-->
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-outline-primary" style="width: 100%; height: auto; margin: 5px auto; border-radius: 3px">Войти</button>
                </div>
            </div>

            <!--Ссылка с помощью-->
            <div class="card-footer text-muted">
                <a href="#"  data-toggle="modal" data-target="#info">Необходима помощь?</a>
            </div>

        </div>
    </form>
    <!--Конец формы авторизации-->

    <!--Окно со всплывающийся подсказкой-->
    <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Справка с помощью</h4>
                </div>
                <div class="modal-body">
                    Вам уже ни что не поможет...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Спасибо</button>
                </div>
            </div>
        </div>
    </div>
    <!--Конец окна с подсказкой-->

    </body>
</html>
