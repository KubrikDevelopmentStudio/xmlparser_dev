/**
 * Created by ruslan on 23.11.16.
 */

$(document).ready(function () {
    /**
     * Тестовая анимация появления блока с выбором типа сообщения
     * после выполнения определенных условий.
     */
    $('#load_from_file_btn').on('click', function () {
       $('#select_message_block').fadeIn(500);
    });


    /**
     * Тестовая анимация появления блока выбора параметров тестирования
     * после выполнения определенных условий.
     */
    $('#message_type_select').on('change', function () {
       var currentSeelct = $('#message_type_select').val();

        if(currentSeelct != "" && currentSeelct != "Выбрать") {
            $('#select_test_params_block').fadeIn(500);
        } else {
            $('#select_test_params_block').fadeOut(500);
        }
    });


    /**
     * Тестовая анимация появления кнопки начала тестирования
     * после выполнения определенных условий.
     */
    $('#select_test_params_block').find('input:checkbox').on('change', function () {
        var selectFlag = false;

        $.each($(this).parents('form').find('input:checkbox'), function (index, obj) {
            if($(obj).prop('checked')) {
                selectFlag = true;
            }
        });

        if(selectFlag) {
            $('#launch_test').removeAttr('disabled');
        } else {
            $('#launch_test').attr('disabled', true);
        }
    });

    /**
     * Скрываем блок с результатами тестирования, если изменили хоть один параметр
     * в настройках тестирования (чтобы всегда видели только актуальные результаты).
     */
    $(document).on('click', 'input, select', function () {
        $('#validation_results_block').fadeOut(500);
    })

});