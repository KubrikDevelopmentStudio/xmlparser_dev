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
     * Отображение таблицы с выбором XML, если ввели XML для тестирования.
     */
    $('#xml_input_area').on('input', function() {
        var input_xml_block = $('#input_xml_block');
        var input_area = $(this);

        if($.trim($(this).val()) !== "") {

            $(input_area).removeClass('form-control-warning').addClass('form-control-success');
            $(input_xml_block).removeClass('has-warning').addClass('has-success');


            $('#xml_table_list_block').fadeIn(500);
        } else {

            $(input_area).removeClass('form-control-success').addClass('form-control-warning');
            $(input_xml_block).removeClass('has-success').addClass('has-warning');
   
            $('#xml_table_list_block').fadeOut(500);
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
    $(document).on('click', 'input, select, tr, textarea', function () {
        $('#validation_results_block').fadeOut(500);
    })

});