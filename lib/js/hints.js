$(document).ready(function() {


     /**
     * Показываем подсказку про вставку XML для сравнения.
     *
     */
    $('#xml_setup_help').not('.no-clickable').on('click', function () {
        var header = "Вставка XML";
        var body_text = "В данное текстовое поле, Вам необходимо вставить xml-текст, который вы хотите сравнить.<br><br>" +
            "Обратите внимание на то, что вставляемый текст должен быть отформатирован! (не должен быть в одну строку или кучу).";

        var button    = ['Я понял'];
        var btn_types = ['danger'];

        show_modal_info(header, body_text, button, btn_types);
    });


     /**
     * Показываем подсказку про выбор XML из таблицы.
     *
     */
    $('#xml_table_help').not('.no-clickable').on('click', function () {
        var header = "Выбор XML из таблицы";
        var body_text = "В таблице ниже предоствлен список всех документов с форматом XML из рабочий папки data/custom_xml.<br><br>" +
            "Чтобы Ваша XML отобразилась в этом списке и была доступна для выбора, необходимо переместить " +
            "её в вышеуказанную папку. После чего обновить страницу и ваша XML появится в таблице."+
            "<br><br>Обратите внимание на то, что выбранная вами XML из таблицы будет являться эталонной при сравнении!";

         var button    = ['Я понял'];
         var btn_types = ['danger'];

        show_modal_info(header, body_text, button, btn_types);
    });


     /**
     * Показываем подсказку про выбор XML из таблицы.
     *
     */
    $('#select_xsd_help').not('.no-clickable').on('click', function () {
        var header = "Выбор XSD схемы";
        var body_text = "Выберите XSD схему из списка, если хотите использовать тестирование, которое включает в себя проверку по XSD схеме. <br><br>" +
            "В противном случае оставьте вариант 'Не использовать XSD'.";

         var button    = ['Я понял'];
         var btn_types = ['danger'];

        show_modal_info(header, body_text, button, btn_types);
    });


     /**
     * Показываем подсказку про выбор XML из таблицы.
     *
     */
    $('#test_param_help').not('.no-clickable').on('click', function () {
        var header = "Выбор параметров тестирования";
        var body_text = "Выберите один или несколько вариантов из доступного тестирования. <br><br>" +
            "В случае выбора тестирования с несколькими параметрами, результаты будут предоставлены по каждому тестированию отдельно.";

         var button    = ['Я понял'];
         var btn_types = ['danger'];

        show_modal_info(header, body_text, button, btn_types);
    });
});