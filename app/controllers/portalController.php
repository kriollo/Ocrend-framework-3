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

use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;
use  \Kint;

/**
 * Controlador portal/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class portalController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router,[
            'users_logged' => true, // Solo usuarios logueados
            'valida_pass_vencida' => false, // valida si la contraseña esta vencida
            'auto_reload_template' => false, // permite la recarga automatica de la plantilla
        ]);

        switch($this->method){
            case "perfil_user":
                $this->template->display('users/perfil_user',[
                    'id_user' => $this->user['id_user'],
                    'reset_pass' => $this->user_resetpass
                ]);
            break;
            default:
                $this->template->display('adminwys/portal', [
                    'id_user' => $this->user['id_user'],
                ]);
            break;
        }
    }
}