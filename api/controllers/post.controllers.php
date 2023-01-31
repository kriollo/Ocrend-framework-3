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

use app\models as Model;

// USERS
    $app->post('/login', function() use($app) {
        $u = new Model\Users;   

        return $app->json($u->login());   
    });
    $app->post('/registeruser', function() use($app) {
        $u = new Model\Users; 

        return $app->json($u->registeruser());   
    });
    $app->post('/updateuser', function() use($app) {
        $u = new Model\Users; 

        return $app->json($u->updateuser());   
    });
    $app->post('/lostpass', function() use($app) {
        $u = new Model\Users; 

        return $app->json($u->lostpass());   
    });
    $app->post('/updateAvatar', function() use($app) {
        $u = new Model\Users; 

        return $app->json($u->updateAvatar());   
    });
    $app->post('/resetpass', function() use($app) {
        $u = new Model\Users; 

        return $app->json($u->resetpass());   
    });
    $app->post('/getUserByIdPOST', function() use($app) {
        $u = new Model\Users();
        return $app->json($u->getUserByIdPOST());
    });
    $app->post('/updatePerfilUsuario', function() use($app) {
        $u = new Model\Users;
        return $app->json($u->updatePerfilUsuario());
    });
    $app->post('/getPerfilUser', function() use($app) {
        $u = new Model\Users;
        return $app->json($u->getOpcionesMenu());
    });