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
     * Выбираем XML из таблички и подсвечиваем выбранный элемент.
     */
    $('tr').on('click', function () {
        if($(this).find('i').length < 1) {
            if(!selXmlDoc.selected) {

                selXmlDoc.full_path = $(this).data('path');
                selXmlDoc.selected = true;

                $(this).find('td:last').append('<i class="fa fa-check fa-1x" aria-hidden="true"></i>');
                $(this).css('border', '3px solid #ffc849')

                $('#select_message_block').fadeIn(500);

            } else {
                console.log('Внимание! Вы уже выбрали XML для тестирования! Единовременно можно выбрать только одну XML.');
            }
        } else {

            selXmlDoc.selected = false;

            $(this).css('border', '');
            $(this).find('i').remove();

            $('#select_message_block').fadeOut(500);
            $('#select_test_params_block').fadeOut(500);
        }
    });
    /**
     * Выбираем XSD из списка для тестирования.
     *
     */
    $('#message_type_select').on('change', function () {
        selXsdDoc.full_path = $('#message_type_select :selected').data('path');
        selXsdDoc.selected = true;
    });
    /**
     * Выбираем параметры тестирования.
     *
     */
    $('#select_test_params_block').find('input:checkbox').on('change', function () {
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
     * Показываем панель с настройками XSD
     *
     */
    $('#setup_xsd').on('click', function () {
        var header = "Настройки XSD параметров";
        var body_text = "Тут очень важные настройки XSD параметров без которых нельзя проводить тестирование!";

        show_modal_info(header, body_text);
    });
    /**
     * Показываем информацию о таблице выбора XML.
     *
     */
    $('#xml_help').on('click', function () {
        var header = "Выбор XML из таблицы";
        var body_text = "В таблице ниже предоствлен список всех документов с форматом XML из рабочий папки data/custom_xml." +
            "Чтобы Ваша XML отобразилась в этом списке и была доступна для выбора, необходимо переместить " +
            "её в вышеуказанную папку. После чего обновить страницу и ваша XML появится в таблице.";

        show_modal_info(header, body_text);
    });

    $('#launch_test').on('click', function () {
        clearLastResults();
        if(startValidation()) {
            $('#validation_results_block').fadeIn(500);
        }
    });

    selXmlDoc.selected = true;
    selXsdDoc.selected = true;

    selXmlDoc.full_path = "../data/custom_xmls/the_best.xml";
    selXsdDoc.full_path = "../data/custom_xmls/different.xml";

    // selXsdDoc.full_path = "../data/xsds/CC/cc_AnketaRequestDataRs.xsd";

    selTestParams.selected = true;
    selTestParams.params[0] = "MY_TEST";

    startValidation();
});
/**
 * Вывод диалогового окна с пользовательским заголовком и текстом.
 *
 * Выводим кастомное модальное окно.
 * @param header Заголовок окна.
 * @param text Текст в окне.
 */
function show_modal_info(header, text) {
    var header_text = (header != "") ? header : null;
    var body_text = (text != "") ? text : null;

    if(header_text != null && body_text != null) {
        $('#info_header_text').text(header_text);
        $('#info_body_text').text(body_text);

        $('#info').modal('show');
    }
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
    //Стираем старую информацию от прошлой попытки тестирования.
    $('#SECOND_TEST_RESULT_BLOCK').find('div.alert').each(function (index, obj) {
        $(obj).remove();
    });
}
/**
 * Выполнить валидацию для выбранной XML по выбранной XSD схеме и
 * выбранным параметрам.
 *
 */
function startValidation() {
    if(!selXmlDoc.selected || !selXsdDoc.selected) {
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

            test_params: selTestParams.params
        },

        error: function (e) {
            console.log("Ошибка: пришел некорректный ответ от сервера!" + e.responseText);
            responseStatus = false;
        },

        success: function (data) {
            console.log("Запрос на валидацию выполнен успешно. Парсим полученные результаты ...");
            responseStatus = true;
        }
    };

    var result = $.ajax(sendXml);

    console.log(result);

    if(result != null && responseStatus) {
        parseValidResults(result);
    } else {
        console.log("Не удалось прочитать данные из ответа сервера: возможно запрос завершился с ошибкой или результат пустой!");
        return false;
    }

    return true;
}
/**
 * Парсинг результатов валидации.
 *
 * @param data Результат валидации в формате JSON.
 */
function parseValidResults(data) {
    var dataJSON = JSON.parse(data.responseText);

    /*if(dataJSON.hasErrors === true) {
        alert("залупа приключилась");
        return false;
    }*/

    var block_body = $('#SECOND_DESCRIPTION_BLOCK');

    clearLastResults();

    for(var i = 0; i < dataJSON.errorList.length; i++) {

        var TAGS_COUNT_IN_XML          = dataJSON.errorList[i].TAGS_COUNT_IN_XML ? dataJSON.errorList[i].TAGS_COUNT_IN_XMLE : "";
        var TAGS_COUNT_IN_COMPARER_XML = dataJSON.errorList[i].TAGS_COUNT_IN_COMPARER_XML ? dataJSON.errorList[i].TAGS_COUNT_IN_COMPARER_XML : "";
        var ERROR_TYPE                 = dataJSON.errorList[i].ERROR_TYPE ? dataJSON.errorList[i].ERROR_TYPE : "";
        var HEADER                     = dataJSON.errorList[i].HEADER     ? dataJSON.errorList[i].HEADER     : "";
        var MESSAGE                    = dataJSON.errorList[i].MESSAGE    ? dataJSON.errorList[i].MESSAGE    : "";
        var LINE                       = dataJSON.errorList[i].LINE       ? dataJSON.errorList[i].LINE       : "";
        var FILE                       = dataJSON.errorList[i].FILE       ? dataJSON.errorList[i].FILE       : "";

        if(TAGS_COUNT_IN_XML != "" && TAGS_COUNT_IN_COMPARER_XML != "") {
            $(block_body).text = "Всего тегов в эталонной XML: " + TAGS_COUNT_IN_XML +
                                 "\nВсего тегов в проверяемой XML: " + TAGS_COUNT_IN_COMPARER_XML;
        }
        
        switch (ERROR_TYPE) {
            case "FATAL_ERROR":
                $(block_body).after(
                    "<div class='alert alert-danger' role='alert'>" +
                        "<strong>"+ HEADER + "</strong>" + MESSAGE + LINE + FILE +
                    "</div>"
                );
                break;
            case "ERROR":
                $(block_body).after(
                    "<div class='alert alert-danger' role='alert'>" +
                    "<strong>"+ HEADER + "</strong>" + MESSAGE + LINE + FILE +
                    "</div>"
                );
                break;
            case "WARNING":
                $(block_body).after(
                    "<div class='alert alert-warning' role='alert'>" +
                    "<strong>"+ HEADER + "</strong>" + MESSAGE + LINE + FILE +
                    "</div>"
                );
                break;
        }
    }
}
