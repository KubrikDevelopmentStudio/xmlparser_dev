/**
 * Created by root on 18.11.16.
 */

$(document).ready(function () {
    /**
     * Анимация загрузки скрипта и показ окна
     * с выбором модуля
     */
    var loading = setInterval(function () {
        var value = $('progress').attr('value');
        if(++value <= 100) {
            $('progress').attr('value', value);
        } else {
            clearInterval(loading);
            $('#loading_block').css('display', 'none');

            $('#info').modal('show');
        }
    }, 10);


    /**
     * Загрузка модуля тестирования XML.
     */
    $('#load_test_module_button').click(function () {
       window.location.href = "xml_test.php";
    });

    /**
     * Загрузка модуля создания XML.
     */
    $('#load_create_module_button').click(function () {
        window.location.href = "xml_creator.php";
    });
});
