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
 * Controlador portal/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class usersController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        global $config;
        $op = (new Model\Adminwys)->getIdMenu($router->getController(),$router->getMethod());
        parent::__construct($router,[
            'users_logged' => true,
            'only_admin' => true,
            'auto_reload_template' => true,
        ]);

        $u = new Model\Users($router);
        switch($this->method){
            case 'perfiles':
                $this->template->display('users/gest_perfiles', [
                    'menu_op' => $op,
                    'db_all_menu' => (new Model\Adminwys)->getAllMenu(),
                    'db_perfiles' => (new Model\Adminwys)->getPerfiles()
                 ]);
            break;
            case 'usuarios':
                $this->template->display('users/listar_users', [
                    'menu_op' => $op,
                    'db_users' => (new Model\Prueba)->getAllUserRB()
                ]);
            break;
            case 'register_user': 
                $this->template->display('users/master_user', [
                    'menu_op' => $op,
                    'id_user' => ""
                ]);
            break;
            case 'update_user':
                if($this->isset_id and false !== ($data = $u->getUserById($router->getId(true)))) {
                    $this->template->display('users/master_user', [
                        'menu_op' => $op,
                        'id_user' => $router->getId(true)
                    ]);
                } else {
                    Helper\Functions::redir($config['build']['url'] . 'users/&error=true');
                }
            break;
            case "estado_user":
                $u->update_estado_user();
            break;
            case 'editar_perfil_user':
                if($this->isset_id and false !== ($data = $u->getUserById($router->getId(true)))) {
                    $this->template->display('users/update_perfil_user', [
                        'menu_op' => $op,
                        'db_users' => $data[0],
                        'db_all_menu' => (new Model\Adminwys)->getAllMenu(),
                        'db_menu_user' => (new Model\Adminwys)->getMenuUser($data[0]['id_user'])
                    ]);
                } else {
                    Helper\Functions::redir($config['build']['url'] . 'users/&error=true');
                }
            break;
            default:
                $this->template->display('users/principal',[
                    'menu_op' => $op,
                    'q_perfiles' => count((new Model\Adminwys)->getPerfiles()),
                    'q_users' => count($u->getUsers('*','estado = 1')),
                    'q_users_online' => count($u->getUsers('*','online_fecha > 0')),
                    'db_user_online' => $u->getUsers('name,email,perfil','online_fecha > 0'),
                    'last_user' => $u->getUsers('name,email,perfil','estado = 1 order by id_user desc Limit 5')
                ]);
            break;
        }
    }
}