<?php

/**
 * @Author: SisSoftwares WEB (Sistemas PHP)
 * @Date:   2019-09-12 11:40:47
 * @Last Modified by: Leonam, Carlos
 * @Last Modified time: 2021-11-27 17:03:03
 */

 /*
  * Add the below line, uncommented of course, before include this file
  * with: include('vendor/carlosleonam/tdatagrid_dynamic_limit/src/include_counter.php');
 */
 // $class_counter = __CLASS__; // Put in onReload of class
$append_selector = $append_selector ?? '.panel-footer:first';

TScript::create("

    has_counter = $('[id=\"select_counter\"]');

    if(has_counter.length == 0) {

        var arr = [
        {val : 5, text: '5'},
        {val : 10, text: '10'},
        {val : 20, text: '20'},
        {val : 50, text: '50'},
        {val : 100, text: '100'},
        {val : 500, text: '500'},
        {val : 1000, text: '1000'}
        ];

        var value_selected = 20;
        var year_selected = new Date().getFullYear();

        $('<label id=\"select_counter\" style=\"margin-left:10px; padding-left=10px; padding-right:10px; font-size: 12px;\">Qtde por página</label>').appendTo('$append_selector');
        var sel = $('<select id=\"limit_page\">').appendTo('$append_selector');
        $(arr).each(function() {
            sel.append($(\"<option>\").attr('value',this.val).text(this.text));
        });

        if (!!$.cookie('profile_limit_". self::$formName ."_per_page')) {
            var select_actual = $.cookie('profile_limit_". self::$formName ."_per_page');
            $('#limit_page').val(parseInt(select_actual));
            $('#limit_page').change();
        }

        $('#limit_page').on('change', function(){
            value_selected = $(this).val();
            document.cookie=\"profile_limit_". self::$formName ."_per_page = \" + value_selected;
            __adianti_load_page(\"index.php?class=" . $class_counter . "&method=onReload\"); /* url para voltar*/
        });

    }


    ");
