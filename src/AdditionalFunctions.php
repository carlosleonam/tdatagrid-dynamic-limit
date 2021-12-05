<?php
namespace CarlosLeonam\TDatagridDynamicLimit;

/**
 * @Author: SisSoftwares WEB (Sistemas PHP)
 * @Date:   2018-09-21 09:04:17
 * @Last Modified by: Leonam, Carlos
 * @Last Modified time: 2021-03-26 15:57:37
 */
/**
* Class with General Functins
*/
class AdditionalFunctions
{

    private static $database = 'unit_database';

	/**
	 * Remove Accents, Trilling Spaces, and Allow only ASCII Chars from a STRING
	 * @param type $string
	 * @return type string
	 */
	public static function getCleanString($string)
	{
		$string_clean = strtoupper( trim( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', self::removerAcentosSoASCII( $string ))));
		return $string_clean;
	}


	/**
     * method numberBR()
     * receives a number, can be float, English type
     * and turns it into a Brazilian type number (ex. 1.524,36)
     * @param $num string with number to transform
     * @returns string return formated value
     */
    public static function numberBR($num, $decimal = 2)
    {
        if($num)
        {
            return number_format($num, $decimal, ',', '.');
        }
    }


    /**
     * method numberUS()
     * receive a number, can be float, Brazilian type
     * and turns it into a number accepted by the database (ex. 1524.36)
     * @param $num string with numbers
     * @returns string with formated numbers
     */
    public static function numberUS($num)
    {
        if($num)
        {
            $source  = array('.', ',');
            $replace = array('', '.');
            return str_replace($source, $replace, $num); //remove os pontos e substitui a virgula pelo ponto
        }
    }


    /**
     * Description: Check if text contain numbers
     * @param string $text
     * @return boolean
     */
    public static function thisContainsNumbers($text){
        return preg_match('/\\d/', $string) > 0;
    }



    /**
     * Extract number from text
     *
     * @param [type] $text
     * @param boolean $signals
     * @return void
     */
    public static function getNumberFromText($text, $signals = false)
    {
		$text_value = $text;
		if ($signals) {
			preg_match('/[+-]{0,1}\d*\.?\d*\.?\d*\.?\d+,\d+/', $text_value, $matches );
		} else {
			preg_match('/\d*\.?\d*\.?\d*\.?\d+,\d+/', $text_value, $matches );
		}

        if (count($matches) === 0) {
            $text_value = 0;
        } else {
            $text_value = $matches[0];
        }

    	return floatval( AdditionalFunctions::numberUS( $text_value ) );
    }


    public static function sendArrayToJS($array, $show_console = false)
    {
        $array_json = json_encode( $array );
        $script_text = "var items_obj = JSON.parse( '" . $array_json . "' );";
        if ($show_console) {
            $script_text .= "console.log(items_obj);";
        }
        TScript::create( $script_text );
    }

    /**
     * Passing PHP array to JS like OBJ
     * @param  array $array       Array to passing
     * @param  string $js_var_name JS variable name
     */
    public static function passArrayToJS($array, $js_var_name = 'var_php_obj')
    {
        $array_json = json_encode( $array );
        $script_text = "var ". $js_var_name ." = JSON.parse( '" . $array_json . "' );
                          console.log(items_obj);";
        TScript::create( $script_text );
    }


    /**
     * Get Current Windowws Dimensions
     * @return array  ['width' => '0000', 'height' => '0000']
     */
    public static function getCurrentWindowSize()
    {
        // Get Current Window Size
        TScript::create("$.post('engine.php?class=SaveDimension&largura='+window.innerWidth+'&altura='+window.innerHeight);");

        $arraySize = array('width' => \Adianti\Registry\TSession::getValue('JanelaLargura'), 'height' => \Adianti\Registry\TSession::getValue('JanelaAltura') );
        return $arraySize;

    }


    public static function getFormName( \Adianti\Wrapper\BootstrapFormBuilder $form)
    {
        // Pegando o nome da form
        $reflectionProperty = new \ReflectionProperty(\Adianti\Wrapper\BootstrapFormBuilder::class, 'title');
        $reflectionProperty->setAccessible(true);
        // Once the property is made accessible, you can read it..
        $form_name = $reflectionProperty->getValue($form);

        return $form_name;

    }


    public static function checkCookieForLimit($cookie_name)
    {
        if(isset($_COOKIE[ $cookie_name ])){

            $limit_l = $_COOKIE[ $cookie_name ];;
            if (!is_numeric($limit_l) || $limit_l == 0 ) {
                $limit = 10;
            } else {
                $limit = (int) $limit_l;
            }
        } else {
            $limit = 10;
        }

        $result = $limit;

        return $result;
    }


    public static function checkCookieForGroup($cookie_name)
    {
            if(isset($_COOKIE[ $cookie_name ])){
                $group_l = $_COOKIE[ $cookie_name ];;
                if ($group_l == '' ) {
                    $group = 'day';
                } else {
                    $group = $group_l;
                }
            } else {
                $group = 'day';
            }
            $result = $group;

        return $result;
    }


    public static function checkCookieForTDatagrid($cookie_name)
    {
            if(isset($_COOKIE[ $cookie_name ])){
                $widths_1 = $_COOKIE[ $cookie_name ];;
                if ($widths_1 == '' ) {
                    $widths = null;
                } else {
                    $widths = explode(',', $widths_1);
                }
            } else {
                $widths = null;
            }
            $result = $widths;

        return $result;
    }


    // Wrapper do Wrapper para o SweetAlert JS ( composer require varunsridharan/sweetalert2-php )
    public static function swalert($title = '', $content = '', $type = 'success')
    {
        $data = swal2($title,$content,$type);
        echo '<script>'.$data.'</script>';
    }


    public static function getTotalModelByFilter($parameters)
    {
        $filters = \Adianti\Registry\TSession::getValue($parameters['session_var']);
        $model = $parameters['model'];

        $criteria_full = new \Adianti\Database\TCriteria;
        if($filters)
        {
            foreach ($filters as $filter)
            {
                // $criteria_full->add($filter);

                $filter_sql = $filter->dump();
                if (substr_count($filter_sql,'||') > 0 ) {

                    // TODO: LEONAM - 09-06-2021_03h16m - create new feature to use new filter (with OR TExpression::OR_OPERATOR and delimiter "||" )

                    $filter_sql = str_replace(['%',"'"],['',''], $filter_sql);
                    $filter_field = explode(' ', $filter_sql)[0];
                    $filter_operator = explode(' ', $filter_sql)[1];
                    $filters_or = explode('||', explode(' ', $filter_sql)[2]);

                    $operator_complement = $filter_operator == 'like' ? '%' : '';

                    $criteria_or = new \Adianti\Database\TCriteria;
                    foreach ($filters_or as $filter_value) {

                        // $filter_sub = new TFilter( $filter_field, $filter_operator, "NOESC:'%$filter_value%'" ) ;  // operator =, <, >, BETWEEN, IN, NOT IN, LIKE, IS NOT
                        $filter_sub = new TFilter( $filter_field, $filter_operator, "NOESC:'$operator_complement$filter_value$operator_complement'" ) ;  // operator =, <, >, BETWEEN, IN, NOT IN, LIKE, IS NOT

                        $criteria_sub = new \Adianti\Database\TCriteria;
                        $criteria_sub->add($filter_sub);
                        $criteria_or->add($criteria_sub, TExpression::OR_OPERATOR);
                    }
                    $criteria_full->add($criteria_or);

                } else {

                $criteria_full->add($filter);
                }

            }
        }

        $total_by_model_filtered = 0;

        try
        {
            // \Adianti\Database\TTransaction::open('unit_database'); // open a transaction
            \Adianti\Database\TTransaction::open(self::$database); // open a transaction

            $total_by_model = new $model;
            $total_by_model_filtered = $total_by_model->get_TotalFiltered( $criteria_full );

            \Adianti\Database\TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new \Adianti\Widget\Dialog\TMessage('error', $e->getMessage()); // shows the exception error message
            \Adianti\Database\TTransaction::rollback(); // undo all pending operations
        }

        return $total_by_model_filtered;

    }


	/**
     * method numeroBrasileiro()
     * recebe um numero, pode ser float, do tipo do ingles
     * e o transforma num numero do tipo brasileiro (ex. 1.524,36)
     * @param $num string com os numeros
     * @returns string com o valor formatado
     */
    public static function numeroBR($num, $decimal = 2)
    {
        if($num || $num == 0)
        {
            return number_format($num, $decimal, ',', '.');
        }
    }


    /**
     * method numeroIngles()
     * recebe um numero, pode ser float, do tipo do brasileiro
     * e o transforma num numero aceito pelo banco de dados (ex. 1524.36)
     * @param $num string com os numeros
     * @returns string com os valor formatado
     */
    public static function numeroUS($num)
    {
        $num_is_numeric = is_numeric($num);
        if($num && !$num_is_numeric && strpos($num,',') )                    // if in BR format, convert to USA (database compatible)
        {
            $source  = array('.', ',');
            $replace = array('', '.');
            return str_replace($source, $replace, $num); //remove os pontos e substitui a virgula pelo ponto
        } else {
            return $num;
        }
    }


    public static function parseSQLFields($table_name, $sql_expression)
    {
        $result = $sql_expression;
        try
        {
            \Adianti\Database\TTransaction::open('dicionario'); // open a transaction

            $conn = \Adianti\Database\TTransaction::get();

            $textSQL = "
                SELECT
                    CAMPOST.NOME AS FIELD_NAME,
                    CAMPOST.TITULO_C AS FIELD_TITLE
                FROM
                    CAMPOST
                INNER JOIN
                    TABELAS ON (CAMPOST.NUMERO = TABELAS.NUMERO)
                WHERE TABELAS.NOME = '$table_name'
                ORDER BY
                CAMPOST.SEQUENCIA
            ;";

            $field_names = [];
            $field_titles = [];
            $field_names[]  = ' AND ';
            $field_titles[] = ' E ';
            $field_names[]  = ' OR ';
            $field_titles[] = ' OU ';
            $field_names[]  = 'EXTRACT(YEAR';
            $field_titles[] = '(ANO';
            $field_names[]  = ' FROM ';
            $field_titles[] = ' DE ';
            $field_names[]  = ' is ';
            $field_titles[] = ' É ';
            $field_names[]  = ' not ';
            $field_titles[] = ' NÃO ';
            $field_names[]  = 'NULL';
            $field_titles[] = 'NULA';
            // $field_names[]  = '';
            // $field_titles[] = '';

            $query = $conn->query( $textSQL );

            foreach ($query as $row) {
                $field_names[] = $row['FIELD_NAME'];
                $field_titles[] = $row['FIELD_TITLE'];
            }

            $result = str_replace( $field_names, $field_titles, $sql_expression );

            \Adianti\Database\TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
    {
            new \Adianti\Widget\Dialog\TMessage('error', $e->getMessage()); // shows the exception error message
            \Adianti\Database\TTransaction::rollback(); // undo all pending operations
        }

        return $result;
    }


    public static function is_decimal($value='')
    {
        return is_numeric( $value ) && floor( $value ) != $value;
    }


    public static function get_number_wo_decimals($value='', $decimal = 0)
    {
        if (self::is_decimal($value)) {
            return number_format($value, $decimal, ',', '.');
        } else {
            return number_format($value, 0, ',', '.');
        }

    }



}