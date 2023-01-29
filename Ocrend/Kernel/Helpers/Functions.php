<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

/**
 * Funciones reutilizables dentro del sistema.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

final class Functions extends \Twig_Extension {

  /**
   * Verifica parte de una fecha, método privado usado en str_to_time
   * 
   * @param int $index: Índice del arreglo
   * @param array $detail: Arreglo
   * @param int $max: Valor a comparar
   *
   * @return bool con el resultado de la comparación
  */
  private static function check_str_to_time(int $index, array $detail, int $max) : bool {
    return !array_key_exists($index,$detail) || !is_numeric($detail[$index]) || intval($detail[$index]) < $max;
  }

  /**
   * Verifica la fecha completa
   *
   * @param array $detail: Arreglo
   * 
   * @return bool
  */
  private static function check_time(array $detail) : bool {
    return self::check_str_to_time(0,$detail,1) || self::check_str_to_time(1,$detail,1) || intval($detail[1]) > 12 || self::check_str_to_time(2,$detail,1970);
  }

  /**
  * Redirecciona a una URL
  *
  * @param string $url: Sitio a donde redireccionará, si no se pasa, por defecto
  * se redirecciona a la URL principal del sitio
  *
  * @return void
  */
  public static function redir($url = null)  {
    global $config;
    
    if (null == $url) {
      $url = $config['build']['url'];
    }
    
    \Symfony\Component\HttpFoundation\RedirectResponse::create($url)->send();
  }

  /**
   * Calcula el porcentaje de una cantidad
   *
   * @param float $por: El porcentaje a evaluar, por ejemplo 1, 20, 30 % sin el "%", sólamente el número
   * @param float $n: El número al cual se le quiere sacar el porcentaje
   *
   * @return float con el porcentaje correspondiente
   */
  public static function percent(float $por, float $n) : float {
    return $n*($por/100);
  }

  /**
   * Da unidades de peso a un integer según sea su tamaño asumida en bytes
   *
   * @param int $size: Un entero que representa el tamaño a convertir
   *
   * @return string del tamaño $size convertido a la unidad más adecuada
   */
  public static function convert(int $size) : string {
    $unit = array('bytes', 'kb', 'mb', 'gb', 'tb', 'pb');
    $result =  round($size/pow(1024, ($i = floor(log($size, 1024)))), 2);
    return $result . ' ' . $unit[$i];
  }

  /**
   * Retorna la URL de un gravatar, según el email
   *
   * @param string  $email: El email del usuario a extraer el gravatar
   * @param int $size: El tamaño del gravatar
   * @return string con la URl
  */
  public static function get_gravatar(string $email, int $size = 35) : string  {
    return 'http://www.gravatar.com/avatar/' . md5($email) . '?s=' . (int) abs($size) . '?d=robohash';
  }


  /**
   * Alias de Empty, más completo
   *
   * @param mixed $var: Variable a analizar
   *
   * @return bool con true si está vacío, false si no, un espacio en blanco cuenta como vacío
   */
  public static function emp($var) : bool {
    return (null === $var || empty(trim(str_replace(' ','',$var))));
  }

   //------------------------------------------------

   /**
     * Aanaliza que TODOS los elementos de un arreglo estén llenos, útil para analizar por ejemplo que todos los elementos de un formulario esté llenos
     * pasando como parámetro $_POST
     *
     * @param array $array, arreglo a analizar
     *
     * @return bool con true si están todos llenos, false si al menos uno está vacío
   */
   public static function all_full(array $array) : bool {
     foreach($array as $e) {
       if(self::emp($e) and $e != '0') {
         return false;
       }
     }
     return true;
   }

  /**
   * Alias de Empty() pero soporta más de un parámetro (infinitos)
   *
   * @return bool con true si al menos uno está vacío, false si todos están llenos
  */
  public static function e() : bool  {
    for ($i = 0, $nargs = func_num_args(); $i < $nargs; $i++) {
      if(self::emp(func_get_arg($i)) && func_get_arg($i) != '0') {
        return true;
      }
    }
    
    return false;
  }


  /**
   * Alias de date() pero devuele días y meses en español
   *
   * @param string $format: Formato de salida (igual que en date())
   * @param int $time: Tiempo, por defecto es time() (igual que en date())
   *
   * @return string con la fecha en formato humano (y en español)
  */
  public static function fecha(string $format, int $time = 0) : string  {
    $date = date($format,$time == 0 ? time() : $time);
    $cambios = array(
         'Monday'=> 'Lunes',
         'Tuesday'=> 'Martes',
         'Wednesday'=> 'Miércoles',
         'Thursday'=> 'Jueves',
         'Friday'=> 'Viernes',
         'Saturday'=> 'Sábado',
         'Sunday'=> 'Domingo',
         'January'=> 'Enero',
         'February'=> 'Febrero',
         'March'=> 'Marzo',
         'April'=> 'Abril',
         'May'=> 'Mayo',
         'June'=> 'Junio',
         'July'=> 'Julio',
         'August'=> 'Agosto',
         'September'=> 'Septiembre',
         'October'=> 'Octubre',
         'November'=> 'Noviembre',
         'December'=> 'Diciembre',
         'Mon'=> 'Lun',
         'Tue'=> 'Mar',
         'Wed'=> 'Mie',
         'Thu'=> 'Jue',
         'Fri'=> 'Vie',
         'Sat'=> 'Sab',
         'Sun'=> 'Dom',
         'Jan'=> 'Ene',
         'Aug'=> 'Ago',
         'Apr'=> 'Abr',
         'Dec'=> 'Dic'
    );
    return str_replace(array_keys($cambios), array_values($cambios), $date);
  }

  /**
   *  Devuelve la etiqueta <base> html adecuada para que los assets carguen desde allí.
   *  Se adapta a la configuración del dominio en general.
   *
   * @return string <base href="ruta" />
  */
  public static function base_assets() : string {
    global $config, $http;

    # Revisar protocolo
    $https = 'http://';
    if($config['router']['ssl']) {
      # Revisar el protocolo
      if(true == $http->server->get('HTTPS')
        || $http->server->get('HTTPS') == 'on' 
        || $http->server->get('HTTPS') == 1) {
        $https = 'https://';
      }
    }

    # Revisar el path
    $path = $config['router']['path'];
    if('/' != substr($path, -1)) {
      $path .= '/';
    }

    # Revisar subdominio
    $www = substr($http->server->get('SERVER_NAME'), 0, 2);
    $base = $path;
    if (strtolower($www) == 'www') {
      $base = 'www.' . $path;
    }
  
    return '<base href="' . $https . $base . '" />';
  }
  
  /**
   * Obtiene el último día de un mes específico
   *
   * @param int $mes: Mes (1 a 12)
   * @param int $anio: Año (1975 a 2xxx)
   *
   * @return string con el número del día
  */
  public static function last_day_month(int $mes, int $anio) : string {
    return date('d', (mktime(0,0,0,$mes + 1, 1, $anio) - 1));
  }
  
  /**
   * Pone un cero a la izquierda si la cifra es menor a diez
   *
   * @param int $num: cifra
   *
   * @return string cifra con cero a la izquirda
   */
  public static function cero_izq(int $num) : string {
    return (string) ($num < 10 ? '0' . $num : $num);
  }

  /**
   * Devuelve el timestamp de una fecha, y null si su formato es incorrecto.
   * 
   * @param string|null $fecha: Fecha con formato dd/mm/yy
   * @param string $hora: Hora de inicio de la $fecha
   *
   * @return int|null con el timestamp
   */
  public static function str_to_time($fecha, string $hora = '00:00:00') {
    $detail = explode('/',$fecha ?? '');

    // Formato de día incorrecto, mes y año incorrectos
    if(self::check_time($detail)) {
      return null;
    }

    // Verificar días según año y mes
    $day = intval($detail[0]);
    $month = intval($detail[1]);
    $year = intval($detail[2]);

    // Veriricar dia según mes
    if ($day > self::last_day_month($month, $year)) {
      return null;
    }

    return strtotime($detail[0] . '-' . $detail[1] . '-' . $detail[2] . ' ' . $hora);
  }

  /**
   * Devuelve la fecha en format dd/mm/yyy desde el principio de la semana, mes o año actual.
   *
   * @param int $desde: Desde donde
   *
   * @return mixed
   */
  public static function desde_date(int $desde) {
    # Obtener esta fecha
    $hoy = date('d/m/Y/D', time());
    $hoy = explode('/', $hoy);

    # Arreglo de condiciones y subcondiciones
    $fecha = array(
       1 => date('d/m/Y', time()),
       2 => date('d/m/Y', time() - (60*60*24)),
       3 => array(
         'Mon' => $hoy[0],
         'Tue' => intval($hoy[0]) - 1,
         'Wed' => intval($hoy[0]) - 2,
         'Thu' => intval($hoy[0]) - 3,
         'Fri' => intval($hoy[0]) - 4,
         'Sat' => intval($hoy[0]) - 5,
         'Sun' => intval($hoy[0]) - 6
       ),
       4 => '01/'. self::cero_izq($hoy[1]) .'/' . $hoy[2],
       5 => '01/01/' . $hoy[2]
    );

    if($desde == 3) {
      # Dia actual
      $dia = $fecha[3][$hoy[3]];

      # Mes anterior y posiblemente, año también.
      if($dia == 0) {
        # Restante de la fecha
        $real_fecha = self::last_day_month($hoy[1],$hoy[2]) .'/'. self::cero_izq($hoy[1] - 1) .'/';

        # Verificamos si estamos en enero
        if($hoy[1] == 1) {
          return  $real_fecha . ($hoy[2] - 1);
        }
        
        # Si no es enero, es el año actual
        return $real_fecha . $hoy[2];
      }
      
      return self::cero_izq($dia) .'/'. self::cero_izq($hoy[1]) .'/' . $hoy[2];
    } else if(array_key_exists($desde,$fecha)) {
      return $fecha[$desde];
    }

    throw new \RuntimeException('Problema con el valor $desde en desde_date()');
  }

  /**
   * Obtiene el tiempo actual
   *
   * @return int devuelve time()
   */
  public static function timestamp() : int {
    return time();
  }

  /**
   * Obtiene el dispositivo del cual esta ingresando el cliente
   *
   * @return mixed
   *
   */
  public static function getDeviceViewWeb() : String {
    $tablet_browser = 0;
    $mobile_browser = 0;
    $body_class = 'desktop';


    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $tablet_browser++;
        $body_class = "tablet";
    }


    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $mobile_browser++;
        $body_class = "mobile";
    }


    if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        $mobile_browser++;
        $body_class = "mobile";
    }


    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda ','xda-');


    if (in_array($mobile_ua,$mobile_agents)) {
        $mobile_browser++;
    }


    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
        $mobile_browser++;
        //Check for tablets on opera mini alternative headers
        $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
          $tablet_browser++;
        }
    }
    if ($tablet_browser > 0) {
    // Si es tablet has lo que necesites
      return 'tablet';
    }
    else if ($mobile_browser > 0) {
    // Si es dispositivo mobil has lo que necesites
      return 'mobil';
    }
    else {
    // Si es ordenador de escritorio has lo que necesites
      return 'pc';
    }
  }

  /**
   * Obtiene la url actual del sitio
   *
   * @return string
   *
   */
  public static function getFullUrl(): string
  {
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
    $host = $_SERVER['HTTP_HOST'];
    $params = $_SERVER['REQUEST_URI'];

    $currentUrl = $protocol . '://' . $host . $params;

    return $currentUrl;
  }

  /**
   * Obtiene la diferncia de dias entre dos fechas
   *
   * @return string
   *
   */
  public static function getDiffDays($fecha1, $fecha2) : string
  {
    $fecha1 = new \DateTime($fecha1);
    $fecha2 = new \DateTime($fecha2);
    $interval = $fecha1->diff($fecha2);

    return $interval->format('%R%a');
  }

  /**
   * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
   *
   * @return array con todas las funciones con sus respectivos nombres de acceso en plantillas twig
   */
  public function getFunctions() : array {
    return array(
       new \Twig_Function('percent', array($this, 'percent')),
       new \Twig_Function('convert', array($this, 'convert')),
       new \Twig_Function('get_gravatar', array($this, 'get_gravatar')),
       new \Twig_Function('emp', array($this, 'emp')),
       new \Twig_Function('e_dynamic', array($this, 'e')),
       new \Twig_Function('all_full', array($this, 'all_full')),
       new \Twig_Function('fecha', array($this, 'fecha')),
       new \Twig_Function('base_assets', array($this, 'base_assets')),
       new \Twig_Function('timestamp', array($this, 'timestamp')),
       new \Twig_Function('desde_date', array($this, 'desde_date')),
       new \Twig_Function('cero_izq', array($this, 'cero_izq')),
       new \Twig_Function('last_day_month', array($this, 'last_day_month')),
       new \Twig_Function('str_to_time', array($this, 'str_to_time')),
       new \Twig_Function('desde_date', array($this, 'desde_date'))
    );
   }



  /**
   * Identificador único para la extensión de twig
   *
   * @return string con el nombre de la extensión
   */
  public function getName() : string {
        return 'ocrend_framework_func_class';
  }
}
