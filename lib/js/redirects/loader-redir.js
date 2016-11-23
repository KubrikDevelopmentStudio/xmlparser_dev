/**
 * Created by ruslan on 23.11.16.
 */

$(document).ready(function () {
    /**
     * Загрузка модуля тестирования XML.
     */
    $('#load_test_module_button').click(function () {
        window.location.href = "sources/xml_test.php";
    });

    /**
     * Загрузка модуля создания XML.
     */
    $('#load_create_module_button').click(function () {
        window.location.href = "sources/xml_creator.php";
    });
});
