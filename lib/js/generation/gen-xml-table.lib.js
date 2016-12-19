$(document).ready(function() {

    var queryResult = null;

    var aResponse = {
        url: "../functions/handler.php",
        type: "POST",
        dataType: "JSON",
        async: false,
        data: {
            action: "getXMLtables",
        },

        error(e) {
            console.log('Во время загрузки XML таблицы с сервера, произошла ошибка!');
            console.log('Описание ошибки: ' + e.responseText);

            queryResult = false;
        },

        success(data) {
            console.log('XML таблица успешна получена! Парсим результаты...');
            queryResult = true;
        }
    }

    var respResult = $.ajax(aResponse);

    
    if(respResult != null && queryResult) {
       print_xml_table(respResult);
    }
});


/**
 * Добавляет элементы в список выбора XSD схемы.
 * 
 * Добавляет все полученные от сервера элементы в список с выбором типов XSD схем.
 */
function print_xml_table(data) {
    
    //Получаем в виде JSON объекта.
    var dataJSON = JSON.parse(data.responseText);
    
    //Если данные имеются, выводим их на страницу.
    if(dataJSON.has_values === true) {      
        $('#xml_table_body').append(dataJSON.xml_table);
    }
}