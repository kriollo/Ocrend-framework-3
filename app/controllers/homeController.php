<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace app\controllers;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;

/**
 * Controlador home/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class homeController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        global $config;
        parent::__construct($router);

        // Si el usuario está conectado, redireccionar a su página de inicio
        if ($this->user_logged($config['build']['url'])) {
            $helperFunction = new Helper\Functions;
            $helperFunction->redir(
                $config['build']['url'] . $this->user['pagina_inicio']
            );
        }

        switch($this->method){
            case 'forgot':
                $this->template->display('home/forgot');
            break;
            case 'logout':
                $u = new Model\Users;
                $u->logout();
            break;
            default:
                $this->template->display('home/home');
            break;
        }
    }
}