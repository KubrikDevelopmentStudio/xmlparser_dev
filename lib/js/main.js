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
            } else {
                console.log('Внимание! Вы уже выбрали XML для тестирования! Единовременно можно выбрать только одну XML.');
            }
        } else {
            selItem.selected = false;
            $(this).find('i').remove();
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
});
