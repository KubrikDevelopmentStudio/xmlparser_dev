$(document).ready(function() {


    /**
     * Объекты параметров тестирования.
     * 
     */
    var Params = [];


    /**
     * Создаем пару параметров для тестирования.
     * 
     */
    Params[0] = {
        main_id: "",
        main_class: "",

        label_id: "default_validation_param",
        label_class: "text-muted",

        data_param: "DEFAULT",
        
        disabled: true,

        label_text: " Стандартная валидация (требуется XSD схема)11"

    };

    Params[1] = {
        main_id: "",
        main_class: "",

        label_id: "",
        label_class: "",

        data_param: "MY_TEST",
        
        disabled: false,

        label_text: " Сравнение двух XML"

    }

    generate_tests_param(Params);
});


/**
 * Функция генерирует объекты-параметры тестирования.
 * 
 * Создаёт HTML объекты в форме test_param_form отвечающие за выбор тестирования.
 */
function generate_tests_param(param_obj) {

    var param_form = $('#test_params_form');

    for(var i = 0; i < param_obj.length; i++) {
        main_id     = (param_obj[i].main_id     != "" ? "id='" + param_obj[i].main_id + "'"  : false);
        main_class  = (param_obj[i].main_class  != "" ?          param_obj[i].main_class     : false);
        label_id    = (param_obj[i].label_id    != "" ? "id='" + param_obj[i].label_id + "'" : false);
        label_class = (param_obj[i].label_class != "" ?          param_obj[i].label_class    : false);

        var tag_html = 
            "<div " + (main_id !== false ? main_id : "") + " class='form-check " + (main_class !== false ? main_class : "") + "'>" +
                "<label " + (label_id !== false ? label_id : "") + " class='form-check-label " + (label_class !== false ? label_class : "") + "'>" +
                    "<input type='checkbox' data-param='" + param_obj[i].data_param + "' class='form-check-input' " + (param_obj[i].disabled ? "disabled" : "") + ">" +
                        param_obj[i].label_text +
                "</label>" +
            "</div>";
        param_form.append(tag_html);
    }
}