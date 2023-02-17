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

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Router\IRouter;
use Ocrend\Kernel\Models\Traits\RedBeanModel;
use RedBeanPHP\R;

/**
 * Modelo Adminwys
 */
class Adminwys extends Models implements IModels {
    use RedBeanModel;

    /**
     * Constructor de la clase y conexión con RedBeanPHP
     *
     * @param IRouter: Objeto de enrutamiento
     *
     * @return void
     */
    public function __construct(IRouter $router = null)
    {
        RedBeanModel::startRedBeanConexion();

        parent::__construct($router);
    }

    /**
     * Destructor de la clase y cierre de conexión con RedBeanPHP
     *
     * @return void
     */
    public function __destruct()
    {
        RedBeanModel::closeRedBeanConexion();
    }

    /**
     * Obtiene los perfiles de la base de datos
     *
     * @return array
     */
    public function getPerfiles(string $select = '*') {
        if ($select == '*') {
            $perfiles = R::getAll('SELECT nombre,url FROM tbladmperfiles GROUP BY nombre ORDER BY nombre');
            return $perfiles;
        }else{
            $perfiles = R::getAll('SELECT { $select } FROM tbladmperfiles limit 1');
            return $perfiles;
        }
    }
    /**
     * Crea un nuevo perfil en la base de datos
     *
     * @return array
     */
    public function new_perfil(){
        try {
            global $http;

            # Obtener los datos $_POST
            $new_perfil = strtoupper($http->request->get('new_perfil'));

            # Verificar que no están vacíos
            if (Helper\Functions::e($new_perfil)) {
                throw new ModelsException('Todos los datos son necesarios');
            }
            # Registrar perfil
            $perfil = R::dispense('tbladmperfiles');
                $perfil->nombre = $new_perfil;
                $perfil->id_menu = 0;
                $perfil->id_submenu = 0;
            R::store($perfil);

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Creado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    /**
     * Actualiza el nombre de un perfil de la base de datos
     *
     * @return array
     */
    public function update_perfil() {
        try{
            global $http;

            # Obtener los datos $_POST
            $new_perfil = strtoupper($http->request->get('new_perfil'));
            $old_perfil = strtoupper($http->request->get('old_perfil'));


            # Verificar que no están vacíos
            if (Helper\Functions::e($new_perfil)) {
                throw new ModelsException('Todos los datos son necesarios');
            }
            # Registrar perfil
            R::exec('UPDATE tbladmperfiles SET nombre = ? WHERE nombre = ?', [$new_perfil, $old_perfil]);

            R::exec('UPDATE tbladmperfiles SET nombre = ? WHERE nombre = ?', [$new_perfil, $old_perfil]);

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Actualizado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    /**
     * Obtiene los perfiles de la base de datos
     *
     * @return array
     */
    public function get_data_perfil(){
        try {
            global $http;
            $perfil = $http->request->get('perfil');


            $query = "SELECT am.descripcion AS menu,glyphicon as icon,asm.id_menu,asm.id_submenu,asm.descripcion,asm.url, ap.url as url_inicio ,ap.nombre,if(ap.nombre IS NULL,0,1) AS checked
            FROM (tbladmsubmenu as asm INNER JOIN tbladmmenu AS am ON asm.id_menu = am.id_menu)
            LEFT JOIN tbladmperfiles as ap ON asm.id_menu=ap.id_menu AND asm.id_submenu=ap.id_submenu and ap.nombre=?
            ORDER BY asm.id_menu,asm.id_submenu";
            $result = R::getAll($query,[$perfil]);

            if($result != []){
                $data = [];
                $url_inicio = 'portal';
                foreach($result as $item){
                    if(!array_key_exists($item['menu'],$data)){
                        $data[$item['menu']] = [
                            'id_menu' => $item['id_menu'],
                            'menu' => $item['menu'],
                            'icon' => $item['icon'],
                            'submenu' => []
                        ];
                    }
                    $data[$item['menu']]['submenu'][] = [
                        'id_menu' => $item['id_menu'],
                        'id_submenu' => $item['id_submenu'],
                        'descripcion' => $item['descripcion'],
                        'checked' => $item['checked'] === '1' ? true : false,
                        'url' => $item['url']
                    ];
                    if($item['url_inicio'] != null)
                        $url_inicio = $item['url_inicio'];
                }
                return ['result_puro' => $result, 'result_formateado' => $data, 'url_inicio' => $url_inicio];
            }
            return [];

        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    /**
     * Actualiza los permisos de un perfil
     *
     * @return array
     */
    public function update_gest_perfil(){
        try {
            global $http;

            $all = $http->request->all();

            $perfil = $all['perfil'];
            $url_inicio = $all['url_inicio'];
            $data = $all['data_perfil'];

            R::exec('DELETE FROM tbladmperfiles WHERE nombre = ?', [$perfil]);

            foreach($data as $item){
                foreach($item['submenu'] as $sub){
                    if($sub['checked'] == true){
                        $perfildb = R::dispense('tbladmperfiles');
                            $perfildb->nombre = $perfil;
                            $perfildb->id_submenu = $sub['id_submenu'];
                            $perfildb->url = $url_inicio;
                            $perfildb->id_menu = $item['id_menu'];
                        R::store($perfildb);
                    }
                }
            }

            //regulariza todos los usuario con actalización de opciones
            R::exec('DELETE FROM tbladmperfilesuser WHERE id_user IN (SELECT id_user FROM users WHERE perfil = ?)', [$perfil]);
            R::exec("Insert Into tbladmperfilesuser(id_user,id_menu,id_submenu)
            select id_user,id_menu,id_submenu from (tbladmperfiles p inner join users u on p.nombre=u.perfil ) where p.nombre='$perfil';");

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Actualizado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    /**
     * Obtiene el id del menu y submenu para marcar opcion activa en menu html
     * @return array
     */
    public function getIdMenu(string $controller, string $metodo = null ){
        global $http;

        $urlAnt = $http->server->get('HTTP_REFERER');
        $urlAnt = explode("/",$urlAnt);
        $pos = array_search($controller,$urlAnt);
        if ($pos !== false) {
            if (isset($urlAnt[$pos+1])) {
                $urlAnt = $urlAnt[$pos]."/".$urlAnt[$pos+1];
            }else{
                $urlAnt = $urlAnt[$pos];
            }
        }

        $url = $controller;
        if($metodo != "" AND $metodo != NULL ){
            $url.= "/".$metodo;
        }
        $result = R::getRow("SELECT id_menu,id_submenu FROM tbladmsubmenu WHERE url = ?",["$url"]);
        if($result != null) return $result;

        $result2 = R::getRow("SELECT id_menu,id_submenu FROM tbladmsubmenu WHERE url = ?",["$urlAnt"]);
        return $result2;
    }
    /**
     * Obtiene las opciones del menu para el usuario logueado
     * @param int $id_user
     * @return array<array>
     */
    public function getMenuUser($id_user) {
        return R::getAll("SELECT m.id_menu,m.posi,m.seccion,m.descripcion menu,m.glyphicon,sm.id_submenu,sm.url,sm.descripcion
        FROM (tbladmperfilesuser pu INNER JOIN tbladmmenu m ON pu.id_menu=m.id_menu) INNER JOIN tbladmsubmenu sm ON pu.id_menu=sm.id_menu AND pu.id_submenu=sm.id_submenu WHERE pu.id_user=? ORDER BY m.PosI,sm.PosS",[$id_user]);
    }
    /**
     * Obtiene todas las opciones activas del sistema
     * @return array<array>
     */
    public function getAllMenu() {
        $result = R::getAll("SELECT m.id_menu,sm.id_submenu,m.seccion,m.descripcion menu,sm.descripcion,m.glyphicon,sm.url
        from tbladmmenu m LEFT JOIN tbladmsubmenu sm ON m.id_menu=sm.id_menu  AND sm.estado=1 WHERE m.estado = 1 ORDER BY m.posi,sm.poss");
        return $result;
    }
    public function getMenuUserByPOST(){
        $user = (New Model\Users)->getUserById($this->id_user);
        if($user['rol']==1)
            return $this->getAllMenu();
        else
            return $this->getMenuUser($this->id_user);
    }
}