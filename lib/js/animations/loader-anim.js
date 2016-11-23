/**
 * Created by root on 18.11.16.
 */

$(document).ready(function () {
    /**
     * Анимация загрузки скрипта и показ окна
     * с выбором модуля
     */
    var loading_animation = setInterval(function () {

        var value = $('progress').attr('value');

        if(++value <= 100) {

            $('progress').attr('value', value);

        } else {

            clearInterval(loading_animation);

            $('progress').addClass('progress-striped');

            $('#load_header_text').text('Модули успешно загружены');

            $('#module_list').modal('show');
        }
    }, 10);
});
