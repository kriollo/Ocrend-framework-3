<?php

namespace Ocrend\Kernel\Models\Traits;

use Ocrend\Kernel\Database\Database;

use RedBeanPHP\R;

/**
 * Añade características para conectarse a una base de datos utilizando RedBeanPHP
 */
trait RedBeanModel {

    /**
     * Inicia la conexión a la base de datos utilizando RedBeanPHP
     *
     * @return void
     */
    public static function startRedBeanConexion($driver = null) {
        global $config;

        if($driver == null) {
            $driver = $config['database']['default_driver'];
        }
        switch ($driver) {
            case 'mysql':
                if (R::$currentDB == null) {
                    R::setup(
                        'mysql:host=' . $config['database']['drivers']['mysql']['host'] . ';dbname=' . $config['database']['drivers']['mysql']['name'],
                        $config['database']['drivers']['mysql']['user'],
                        $config['database']['drivers']['mysql']['pass']
                    );
                    // R::debug(true);
                }
            break;
            case 'sqlite3':
                R::setup('sqlite:'.$config['database']['drivers']['sqlite3']['file'], '', '');
            break;
        }
    }

    public static function closeRedBeanConexion() {
        R::close();
    }

    function __destruct() {
        self::closeRedBeanConexion();
    }
}
