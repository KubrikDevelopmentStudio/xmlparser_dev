$(document).ready(function() {

    var queryResult = null;

    var aResponse = {
        url: "../functions/handler.php",
        type: "POST",
        dataType: "JSON",
        async: false,
        data: {
            action: "getXSDschemas",
        },

        error: function(e) {
            console.log('Во время загрузки XSD схем с сервера, произошла ошибка!');
            console.log('Описание ошибки: ' + e.responseText);

            queryResult = false;
        },

        success: function(data) {
            console.log('Список XSD схем успешно получен! Парсим результаты...');
            queryResult = true;
        }
    }

    var respResult = $.ajax(aResponse);

    if(respResult != null && queryResult) {
        print_xsd_table(respResult);
    }
});


/**
 * Добавляет элементы в список выбора XSD схемы.
 * 
 * Добавляет все полученные от сервера элементы в список с выбором типов XSD схем.
 */
function print_xsd_table(data) {
    //Получаем в виде JSON объекта.
    var dataJSON = JSON.parse(data.responseText);

    if(dataJSON.has_values === true) {      
        $('#message_type_select').append(dataJSON.xsd_table);
    }
}