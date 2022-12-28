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

/**
 * Controlador error/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class errorController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router);


        global $http;
        $e = $http->query->get("e");
        switch ($e) {
            case '403':
                $this->template->display('error/403');
            break;
            case '404':
                $this->template->display('error/404');
            break;
            default:
                $this->template->display('error/404');
            break;
        }
    }
}