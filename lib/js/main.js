/**
 * Created by ruslan on 23.11.16.
 */

$(document).ready(function () {
    /**
     * Путь к выбранной XML.
     *
     * @type {{selected: boolean, full_path: null}}
     */
    var selXmlDoc = {
        selected: false,
        full_path: null,
    };
    /**
     * Путь к выбранной XSD.
     *
     * @type {{selected: boolean, full_path: null}}
     */
    var selXsdDoc = {
        selected: false,
        full_path: null,
    };
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

        }
    });
    /**
     * Выбираем XSD из списка для тестирования.
     */
    $('#message_type_select').on('change', function () {
        selXsdDoc.full_path = $(this).data('path');
        selXsdDoc.selected = true;
    });
    /**
     * Показываем панель с настройками XSD
     */
    $('#setup_xsd').on('click', function () {
        $('#xsd_setup_info').modal('show');
    });
});
/**
 * Функция показа сообщения об ошибке.
 *
 * @param message Текст сообщения.
 * @param type Тип сообщения.
 */
function addMsg(message, type) {
    if(message != null && type != null) {
        var _message = "<strong>Внимание!</strong> " + message;

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

        $(alrt_obj).text(_message);
        $(alrt_obj).addClass(_type);

        $(alrt_obj).css('display', 'block');

    } else {
        console.log("Ошибка добавления информативного сообщения! Один из параметров: message или type пришёл null!");
    }
}
/**
 * Выполнить валидацию для выбранной XML по выбранной XSD схеме.
 *
 */
function startValidation() {
    if(!selXmlDoc.selected || !selXsdDoc.selected) {
        console.log("Ошибка при отправке на валидацию! Один из документов не выбран: XSD или XML!");
        return
    }

    var sendXml = {
        url: "../../functions/handler.php",
        type: "POST",
        dataType: "JSON",
        async: false,
        data: {
            xml_doc: selXmlDoc.full_path,
            xsd_doc: selXsdDoc.full_path
        },

        error: function (e) {
            console.log("Ошибка: " + e.responseText);
        },

        success: function (data) {
            alert(data.responseText);
        }
    };

    var result = $.ajax(sendXml);
}
