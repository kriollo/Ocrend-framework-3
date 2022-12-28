<?php

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
    $app->post('/update_perfil_usuario', function() use($app) {
        $u = new Model\Users;
        return $app->json($u->update_perfil_usuario());
    });
       $app->post('/get_data_perfil', function() use($app) {
        $u = new Model\Adminwys();
        return $app->json($u->get_data_perfil());
    });
    $app->post('/new_perfil', function() use($app) {
        $u = new Model\Adminwys();
        return $app->json($u->new_perfil());
    });
    $app->post('/update_gest_perfil', function() use($app) {
        $u = new Model\Adminwys();
        return $app->json($u->update_gest_perfil());
    });
    $app->post('/delete_gest_perfil', function() use($app) {
        $u = new Model\Adminwys();
        return $app->json($u->delete_gest_perfil());
    });
    $app->post('/get_menu_user_by_POST', function() use($app) {
        $u = new Model\Adminwys();
        return $app->json($u->getMenuUserByPOST());
    });
//
// GETS

   $app->post('/getPerfiles', function() use($app) {
        $u = new Model\Adminwys();
        return $app->json($u->getPerfiles());
    });