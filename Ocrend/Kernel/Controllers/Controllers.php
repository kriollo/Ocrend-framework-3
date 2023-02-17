<?php

declare(strict_types=1);

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Controllers;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Router\IRouter;

/**
 * Clase para conectar todos los controladores del sistema y compartir la configuración.
 * Inicializa aspectos importantes de una página, como el sistema de plantillas twig.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

abstract class Controllers {

    protected $menu_user =[];
    protected $user_resetpass = [];
    /**
      * Obtiene el objeto del template
      *
      * @var \Twig\Environment
    */
    protected $template;

    /**
      * Verifica si está definida la ruta /id como un integer >= 1
      *
      * @var bool
    */
    protected $isset_id = false;

    /**
      * Tiene el valor de la ruta /método
      *
      * @var string|null
    */
    protected $method;

    /**
      * Arreglo con la información del usuario conectado actualmente.
      *
      * @var array
    */
    protected $user = [];

    /**
      * Contiene información sobre el estado del usuario, si está o no conectado.
      *
      * @var bool
    */
    private $is_logged = false;

    /** 
      * Parámetros de configuración para el controlador con la forma:
      * 'parmáetro' => (bool) valor
      *
      * @var array
    */
    private $controllerConfig;

    /**
      * Set inicial del valor del IVA
      *
      * @var double
    */
    protected $IVA = 0.19;

    /**
      * Configuración inicial de cualquier controlador
      *
      * @param IRouter $router: Instancia de un Router
      * @param array $configController: Arreglo de configuración con la forma
      *     'users_logged' => bool, # Configura el controlador para solo ser visto por usuarios logeados
      *     'users_not_logged' => bool, # Configura el controlador para solo ser visto por !(usuarios logeados)
      *
    */
    protected function __construct(IRouter $router, $configController = []) {
        global $config, $http, $session, $cookie;

        # Verificar si está logeado el usuario
        $this->is_logged = null != $session->get($cookie->get('session_hash'). $config['sessions']['unique'] . '__user_id');

        # Establecer la configuración para el controlador
        $this->setControllerConfig($configController);

        # Twig Engine http://gitnacho.github.io/Twig/
        $loader = new \Twig\Loader\FilesystemLoader('./app/templates/');
        $this->template = new \Twig\Environment($loader, [
            # ruta donde se guardan los archivos compilados
            'cache' => $config['twig']['compiled_dir'],
            # false para caché estricto, cero actualizaciones, recomendado para páginas 100% estáticas, pero se puede pasar desde el controller en caso de que sea necesario
            'auto_reload' => isset($this->controllerConfig['auto_reload_template'])? $this->controllerConfig['auto_reload_template']:!$config['twig']['cache'], 
            # en true, las plantillas generadas tienen un método __toString() para mostrar los nodos generados
            'debug' => !$config['build']['production'],
            # el charset utilizado por los templates
            'charset' => $config['twig']['charset'],
            # true para evitar ignorar las variables no definidas en el template
            'strict_variables' => $config['twig']['strict_variables'],
            # false para evitar el auto escape de html por defecto (no recomendado)
            'autoescape' => $config['twig']['autoescape']
        ]);

        # Request global
        $this->template->addGlobal('get', $http->query->all());
        $this->template->addGlobal('server', $http->server->all());
        $this->template->addGlobal('session', $session->all());
        $this->template->addGlobal('cookie', $cookie->all());
        $this->template->addGlobal('config', $config);
        $this->template->addGlobal('is_logged', $this->is_logged);
        $this->template->addGlobal('controller', $router->getController());
        $this->template->addGlobal('method', $router->getMethod());
        $this->template->addGlobal('IVA',$this->IVA);

        # Datos del usuario actual
        if ($this->is_logged) {
            $this->user = (new Model\Users)->getOwnerUser('id_user,name,rol,perfil,pagina_inicio,foto,name_foto,email,fecha_pass');
            $this->template->addGlobal('owner_user', $this->user);

            (new Model\Users)->update_online_user('in');

            $this->menu_user = (new Model\Users)->getMenuOwnerUser((int)$this->user['rol']);
            $this->template->addGlobal('menu_user', $this->menu_user );

            $this->user_resetpass = (new Model\Users)->validar_cambio_pass($this->user['fecha_pass']);
        }

        # Extensiones
        $this->template->addExtension(new Helper\Functions);

        # Debug disponible en twig
        if(!$config['build']['production']) {
          $this->template->addExtension(new \Twig\Extension\DebugExtension());
        }

        # Verificar para quién está permitido este controlador
        $this->knowVisitorPermissions($config['build']['url']);

        # Auxiliares
        $this->method = $router->getMethod();
        $this->isset_id = $router->getID(true);
    }

    /**
     * Establece los parámetros de configuración de un controlador
     *
     * @param IRouter $router: Instancia de un Router
     * @param array|null $config: Arreglo de configuración
     *
     * @return void
     */
    private function setControllerConfig($config) {
        $this->controllerConfig = array_merge([
            'users_logged' => false,
            'users_not_logged' => false,
            'only_admin' => false,
            'access_menu' => false,
            'valida_pass_vencida' => true
        ], $config);
    }

    /**
     * Valida si el usuario debe cambiar su contraseña
     *
     * @return void
     */
    private function valida_pass_vencida(string $urlConfig) {
      if ($this->user_resetpass && $this->controllerConfig['valida_pass_vencida']) {
        $urlActual = Helper\Functions::getFullUrl();
        if($urlConfig . 'portal/perfil_user' != $urlActual) {
          $helperFunction = new Helper\Functions;
          $helperFunction->redir($urlConfig. 'portal/perfil_user');
        }
      }
    }

    /**
     * Valida si está logeado el usuario
     *
     * @return void|bool
     */
    public function user_logged(string $urlConfig) {
        if ($this->controllerConfig['users_logged'] && !$this->is_logged) {
          $helperFunction = new Helper\Functions;
          $helperFunction->redir($urlConfig);
        }
        return $this->is_logged;
    }

    /**
     * Valida si no está logeado el usuario
     *
     * @return void
     */
    private function user_not_logged() {
        if ($this->controllerConfig['users_not_logged'] && $this->is_logged) {
          $helperFunction = new Helper\Functions;
          $helperFunction->redir();
        }
    }

    /**
     * Valida si el usuario es administrador
     *
     * @return void
     */
    private function acess_only_user_admin(string $urlConfig) {
        if ($this->controllerConfig['only_admin'] && $this->user['rol'] != 1 ) {
          $helperFunction = new Helper\Functions;
          $helperFunction->redir($urlConfig. 'error?e=404');
        }
    }

    /**
     * Acción que regula quién entra o no al controlador según la configuración
     *
     * @return void
     */
    private function knowVisitorPermissions(string $urlConfig) {
        # acceso sólo si usuario está logeado
        $this->user_logged($urlConfig);
        $this->user_not_logged();

        if ($this->is_logged) {

            $this->valida_pass_vencida($urlConfig);

            $this->acess_only_user_admin($urlConfig);

            # acceso opcion sólo si usuario tiene permiso en perfil
            if ($this->controllerConfig['access_menu'] != false && $this->controllerConfig['access_menu']['valida_acceso']) {

                $menuFound = array_filter($this->menu_user, function ($menu) {
                    return $menu['id_menu'] === $this->controllerConfig['access_menu']['menu']['id_menu'] &&
                           $menu['id_submenu'] === $this->controllerConfig['access_menu']['menu']['id_submenu'];
                });

                if (is_array($menuFound) && empty($menuFound)) {
                  $helperFunction = new Helper\Functions;
                  $helperFunction->redir($urlConfig . 'error?e=404');
                }
            }
        }

    }

}