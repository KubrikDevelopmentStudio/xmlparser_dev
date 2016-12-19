/**
 * Created by ruslan on 23.11.16.
 */

$(document).ready(function () {


    /**
     * Путь к выбранной XML.
     *
     * @type {{selected: boolean, full_path: null}}
     */
    selXmlDoc = {
        selected: false,
        full_path: null,
    };


    /**
     * Путь к выбранной XSD.
     *
     * @type {{selected: boolean, full_path: null}}
     */
    selXsdDoc = {
        selected: false,
        full_path: null,
    };


    /**
     * Выбранные параметры тестирования.
     *
     * @type {{selected: boolean, params: Array}}
     */
    selTestParams = {
        selected: false,
        params: []
    }


    /**
     * XML документ для сравнения.ы
     */
    xml_comp_text = "";


    /**
     * Выбираем XML из таблички и подсвечиваем выбранный элемент.
     */
    $('tr').not('.no-clickable').on('click', function () {
        var current_tr = $(this);
        if(!selXmlDoc.selected) {
            selXmlDoc.full_path = $(current_tr).data('path');
            selXmlDoc.selected = true;

            $(current_tr).find('td:last').append('<i class="fa fa-check fa-1x check-status" aria-hidden="true"></i>');
            $(current_tr).css('border', '3px solid #ffc849');

            $('#select_message_block').fadeIn(200);
            $('#select_test_params_block').fadeIn(200);
        } else {
            if($(current_tr).find('i.check-status').length > 0) {
                selXmlDoc.selected = false;

                $(current_tr).css('border', '');
                $(current_tr).find('i.check-status').remove();

                $('#select_message_block').fadeOut(200);
                $('#select_test_params_block').fadeOut(200);
            } else {
                 $('#xml_table_list').find('tr').each(function(index, obj) {
                    if($(obj).find('i.check-status').length > 0) {
                        $(obj).css('border', '');
                        $(obj).find('i.check-status').remove();
                    }
                });

                selXmlDoc.full_path = $(current_tr).data('path');
                selXmlDoc.selected = true;

                $(current_tr).find('td:last').append('<i class="fa fa-check fa-1x check-status" aria-hidden="true"></i>');
                $(current_tr).css('border', '3px solid #ffc849')
            }
        }
       
    });


    /**
     * Выбираем XSD из списка для тестирования.
     *
     */
    $('#message_type_select').not('.no-clickable').on('change', function () {
        var valid_param = $('#default_validation_param');
        var checkbox_default_valid = $("#test_params_form input:checkbox:first");

        if($('#message_type_select :selected').val() === "Не использовать XSD") {
            $(checkbox_default_valid).prop('disabled', true);

            if($(checkbox_default_valid).prop('checked')) {
                $(checkbox_default_valid).prop('checked', false);
                $(checkbox_default_valid).trigger('change');
            }

            if(!$(valid_param).hasClass('text-muted')) {
                $(valid_param).addClass('text-muted');
            }
        } else {
            $(checkbox_default_valid).prop('disabled', false);

            if($(valid_param).hasClass('text-muted')) {
                $(valid_param).removeClass('text-muted');
            }                          
        }
        selXsdDoc.full_path = $('#message_type_select :selected').data('path');
        selXsdDoc.selected = true;
    });


    /**
     * Выбираем параметры тестирования.
     *
     */
    $('#select_test_params_block').find('input:checkbox').not('.no-clickable').on('change', function () {
        var selParam =  $(this).data('param');
        if(selParam != null) {
            if(selTestParams.selected) {
                var index = 0;
                if((index = $.inArray(selParam, selTestParams.params)) == -1) {
                    selTestParams.params[selTestParams.params.length] = selParam;
                } else {
                    selTestParams.params.splice(index, 1);
                    if(selTestParams.params.length < 1) {
                        selTestParams.selected = false;
                    }
                }
            } else {
                selTestParams.params[selTestParams.params.length] = selParam;
                selTestParams.selected = true;
            }
        } else {
            console.log("Упс! У выбранного параметра отсутствует информация об привязанном параметре теста. Аттрибут: data-param");
        }
    });
   

    /**
     * Кнопка запуска тестирования.
     */
    $('#launch_test').not('.no-clickable').on('click', function () {
        clearLastResults();
         if(startValidation()) {
            $('#validation_results_block').fadeIn(200);
        }       
    });


    /**
     * Постоянно подгружаем данные, которые вносятся в текстовую область.
     */
    $('#xml_input_area').not('.no-clickable').on('input', function() {
        xml_comp_text = $(this).val();
    });
    
    // selXmlDoc.selected = true;
    // selXsdDoc.selected = true;

    // selXmlDoc.full_path = "../data/custom_xmls/the_best.xml";
    // selXsdDoc.full_path = "../data/custom_xmls/different.xml";

    // // selXsdDoc.full_path = "../data/xsds/CC/cc_AnketaRequestDataRs.xsd";

    // selTestParams.selected = true;
    // selTestParams.params[0] = "MY_TEST";

    // startValidation();
});


/**
 * Очистка модального диалогового окна, от предыдущей информации.
 *
 * Очищаем модальное окно.
 * @param header Заголовок окна.
 * @param text Текст в окне.
 */
function clear_modal() {

    var modal_window      = $('#info');

    var modal_header_text = $('#info_header_text');
    var modal_body_text   = $('#info_body_text');

    var modal_footer = $('#info_footer');

    modal_header_text.text();
    modal_body_text.text('');
    modal_body_text.children().remove();
    modal_footer.children().remove();
}
/**
 * Вывод диалогового окна с пользовательским заголовком и текстом.
 *
 * Выводим кастомное модальное окно.
 * @param header Заголовок окна.
 * @param text Текст в окне.
 */
function show_modal_info(header, text, button, btn_types) {

    clear_modal();

    var modal_window      = $('#info');

    var modal_header_text = $('#info_header_text');
    var modal_body_text   = $('#info_body_text');

    var modal_footer      = $('#info_footer');

    modal_header_text.text(header);
    modal_body_text.append(text);

    var ready_btns = "";
    for(var i = 0; i < button.length; i++) {
        switch(btn_types[i]) {
            case 'primary':
                ready_btns += "<button type='button' class='btn btn-outline-primary'>" + button[i] + "</button> ";
                break;
            case 'secondary':
                ready_btns += "<button type='button' class='btn btn-outline-secondary'>" + button[i] + "</button> ";
                break;
            case 'success':
                ready_btns += "<button type='button' class='btn btn-outline-success'>" + button[i] + "</button> ";
                break;
            case 'info':
                ready_btns += "<button type='button' class='btn btn-outline-info'>" + button[i] + "</button> ";
                break;
            case 'warning':
                ready_btns += "<button type='button' class='btn btn-outline-warning'>" + button[i] + "</button> ";
                break;
            case 'danger':
                ready_btns += "<button type='button' class='btn btn-outline-danger' data-dismiss='modal'>" + button[i] + "</button> ";
                break;            
        }
    }
    
    $(modal_footer).append(ready_btns);

    modal_window.modal('show');
}
/**
 * Функция показа сообщения об ошибке.
 *
 * Внимание! После закрытия алерта с сообщением - он удаляется!
 * Реализовать программное создание алерта, чтобы избежать ошибок при вывода новых сообщений.
 *
 * @param message Текст сообщения.
 * @param type Тип сообщения.
 */
function addMsg(header, message, type) {
    if(message != null && type != null) {
        var _message =  message;
        var _header = header != null ? header : "";

        var _type = null;
        switch (type) {
            case 'warn':
                _type = "alert-warning";
                break;
            case 'succ':
                _type = "alert-success";
                break;
            case 'inf':
                _type = "alert-info";
                break;
            case 'dng':
                _type = "alert-danger";
                break;
        }

        var alrt_obj = $('#alert_message');

        $(alrt_obj).find('text:first').text(_header);
        $(alrt_obj).find('text:last').text(_message);

        $(alrt_obj).addClass(_type);

        $(alrt_obj).css('display', 'block');

    } else {
        console.log("Ошибка добавления информативного сообщения! Один из параметров: message или type пришёл null!");
    }
}


/**
 * Очистка результатов от предыдущего тестирования.
 * 
 */
function clearLastResults() {
    $('#validation_results_block').find('div.row').remove();
}


/**
 * Выполнить валидацию для выбранной XML по выбранной XSD схеме и
 * с выбранным параметрам.
 *
 */
function startValidation() {
    if(!selXmlDoc.selected || (!selXsdDoc.selected && xml_comp_text === "")) {
        console.log("Ошибка при отправке на валидацию! Один из документов не выбран: XSD или XML!");
        return false;
    }

    var responseStatus = false;
    var sendXml = {
        url: "../functions/handler.php",
        type: "POST",
        dataType: "JSON",
        async: false,
        data: {
            action: 'validateXml',

            xml_doc: selXmlDoc.full_path,
            xsd_doc: selXsdDoc.full_path,

            xml_comparer_text: xml_comp_text,

            test_params: selTestParams.params
        },

        error: function (e) {
            console.log("Ошибка: пришел некорректный ответ от сервера!\n" + e.responseText);
            responseStatus = false;
        },

        success: function (data) {
            console.log("Запрос тестирования XML выполнен успешно. Парсим полученные результаты ...");
            responseStatus = true;
        }
    };

    var result = $.ajax(sendXml);

    if(result != null && responseStatus) {
        var dataJSON = JSON.parse(result.responseText);
        create_result_block(dataJSON);
    } else {
        console.log("Не удалось прочитать данные из ответа сервера: возможно запрос завершился с ошибкой или результат пустой!");
        return false;
    }

    return true;
}


/**
 * Создание блоков с отображением результатов тестирования XML.
 *
 * @param JSONdata Результат валидации в формате JSON.
 */
function create_result_block(JSONdata) {
    //Получаем количество выполненных тестов.
    var block_count = JSONdata.length;
    //Необходимое количество блоков на строке.
    var need_block_create = (block_count > 1) ? 2 : 1;
    //Количество необходимых строк.
    var row_count = (block_count > 1) ? block_count / 2 | 0 : 1;
    //Текущий создаваемый блок.
    var current_block = 0;

    for(var i = 0; i < row_count; i++) {
        var row_html = "<div class='row'>";
        for(var j = 0; j < need_block_create; j++) {
            var block_html = 
                "<div class='col-sm-6'>" + 
                    "<div id='RESULT_BLOCK_" + current_block + "' class='card card-block'>" +
                        "<h3 id='HEADER_BLOCK_" + current_block + "' class='card-title'>" + JSONdata[current_block].test_caption + "</h3>" +
                        "<p  id='DESCRIPTION_BLOCK_" + current_block + "' class='card-text'>" + JSONdata[current_block].test_description + "</p>" +
                        get_message_by_id(JSONdata[current_block]) +
                    "</div>" +
                "</div>";
            
            row_html += block_html; 

            current_block++;
        }
        row_html += "</div>";
        
        //Тут пременение append() в JQuery для отображения всего блока row!
        $('#TEST_DESCRIPTION').after(row_html);
    }
}


/**
 * Получение всех сообщений с ошибками из конкретного теста.
 *
 * @param dataJSON Объект-результат конкретного теста.
 */
function get_message_by_id(dataJSON) {
    
    var warnings_count    = 0;
    var error_count       = 0;
    var fatal_error_count = 0;

    var sorted_arr = [];    
    for(var i = 0; i < dataJSON.errorList.length; i++) {
        var ERROR_TYPE   = dataJSON.errorList[i].ERROR_TYPE ? dataJSON.errorList[i].ERROR_TYPE : "";      
        if(ERROR_TYPE == "FATAL_ERROR") sorted_arr[sorted_arr.length] = dataJSON.errorList[i]; 
    }

    for(var i = 0; i < dataJSON.errorList.length; i++) {
        var ERROR_TYPE   = dataJSON.errorList[i].ERROR_TYPE ? dataJSON.errorList[i].ERROR_TYPE : "";      
        if(ERROR_TYPE == "ERROR") sorted_arr[sorted_arr.length] = dataJSON.errorList[i]; 
    }

    for(var i = 0; i < dataJSON.errorList.length; i++) {
        var ERROR_TYPE   = dataJSON.errorList[i].ERROR_TYPE ? dataJSON.errorList[i].ERROR_TYPE : "";      
        if(ERROR_TYPE == "WARNING") sorted_arr[sorted_arr.length] = dataJSON.errorList[i]; 
    }

    var message_arr = "";
    for(var i = 0; i < sorted_arr.length; i++) {

        var ERROR_TYPE   = sorted_arr[i].ERROR_TYPE ? sorted_arr[i].ERROR_TYPE : ""; 
        var HEADER       = sorted_arr[i].HEADER     ? sorted_arr[i].HEADER     : "";
        var MESSAGE      = sorted_arr[i].MESSAGE    ? sorted_arr[i].MESSAGE    : "";
        var LINE         = sorted_arr[i].LINE       ? sorted_arr[i].LINE       : "";
        var FILE         = sorted_arr[i].FILE       ? sorted_arr[i].FILE       : "";

        switch (ERROR_TYPE) {
            case "FATAL_ERROR":               
                message_arr += 
                    "<div class='alert alert-danger' role='alert'>" +
                        "<strong>"+ HEADER + " " +   "</strong> " + MESSAGE + FILE +
                    "</div>";              
                fatal_error_count++;
                break;
            case "ERROR":
               message_arr += 
                    "<div class='alert alert-danger' role='alert'>" +
                        "<strong>"+ HEADER + " " +   "</strong> " + MESSAGE + FILE +
                    "</div>"; 
                error_count++;
                break;
            case "WARNING":
                message_arr += 
                    "<div class='alert alert-warning' role='alert'>" +
                        "<strong>"+ HEADER + " " +   "</strong> " + MESSAGE + FILE +
                    "</div>"; 
                warnings_count++;
                break;
        }
    }
    

    var all_events = fatal_error_count + error_count + warnings_count;

    var error_statistic = "";

    error_statistic += "<div class='infos-msg'><br>Всего фатальных ошибок: " + fatal_error_count + "</div>";
    error_statistic += "<div class='infos-msg'>Всего ошибок: " + error_count + "</div>";
    error_statistic += "<div class='infos-msg'>Всего предупреждений: " + warnings_count + "</div>";
    error_statistic += "<div class='infos-msg'><br>Всего событий: " + all_events + "</div>";

    return error_statistic + message_arr;
}
