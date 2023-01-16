<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\RedBeanModel;
use Ocrend\Kernel\Router\IRouter;
use RedBeanPHP\R;

/**
 * Modelo Prueba para conexión con RedBeanPHP
 */
class Prueba extends Models implements IModels {
    use RedBeanModel;
    public function getAllUserRB()
    {
        $users = R::findAll('users');
        return $users;
    }

    public function __construct(IRouter $router = null) {
        RedBeanModel::startRedBeanConexion();

        parent::__construct($router);
    }

}