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
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Adminwys
 */
class Adminwys extends Models implements IModels {
    use DBModel;

    public function getPerfiles(string $select = '*') {
        if ($select == '*') {
            $perfiles = $this->db->query_select("SELECT nombre FROM tbladm_perfiles GROUP BY nombre ORDER BY nombre");
            if($perfiles != false)
                return $perfiles;
            else
                return [];
        }else{
            $perfiles = $this->db->select($select,'tbladm_perfiles',"",'Limit 1');
            return $perfiles[0];
        }
    }
    public function get_data_perfil(){
        try {
            global $http;
            $id = $http->request->get('id');
            
            $query = "SELECT asm.id_menu,asm.id_submenu,asm.descripcion,if(ap.nombre IS NULL,FALSE,TRUE) AS checked FROM tbladm_submenu asm LEFT JOIN tbladm_perfiles ap ON asm.id_menu=ap.id_menu AND asm.id_submenu=ap.id_submenu and nombre='$id' ORDER BY asm.id_menu,asm.id_submenu";
            $result = $this->db->query_select($query);
            if ($result != false){
                return ['success' => 1, 'data' => $result];    
            }
            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil no encontrado o con problemas'];
            
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
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
            $this->db->insert('tbladm_perfiles', [
                'nombre' => $new_perfil,
                'id_menu' => 0,
                'id_submenu' => 0
            ]);

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Creado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function update_gest_perfil(){
        try {
            global $http;
  
            $perfil = $http->request->get('perfil');
  
            $this->db->query("Delete from tbladm_perfiles
            WHERE nombre='$perfil';");
  
            $p = $this->getAllMenu();
            foreach ($p as $value => $data) {
                $a = $http->request->get('check-'.$data['id_menu'].'-'.$data['id_submenu']);

                if (true == $a){
                    $id_menu = $data['id_menu'];
                    $id_submenu = $data['id_submenu'];
                    
                    $this->db->query("Insert tbladm_perfiles(nombre,id_menu,id_submenu) value('$perfil',$id_menu,$id_submenu);");
                }
            }
  
            //regulariza todos los usuario con actalización de opciones
            $this->db->query("delete p from (users u inner join tbladm_perfilesuser p on u.id_user=p.id_user )
            where u.perfil='$perfil';");

            $this->db->query("Insert Into tbladm_perfilesuser(id_user,id_menu,id_submenu)
            select id_user,id_menu,id_submenu from (tbladm_perfiles p inner join users u on p.nombre=u.perfil ) where p.nombre='$perfil';");
  
            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Actualizado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function delete_gest_perfil() : array {
        try {
            global $http;

            # Obtener los datos $_POST
            $perfil = $http->request->get('id');

            # Verificar que no están vacíos
            if (Helper\Functions::e($perfil)) {
                throw new ModelsException('Todos los datos son necesarios');
            }elseif ($perfil == '--'){
                throw new ModelsException('Debe seleccionar un perfil valido');
            }elseif ($perfil == 'DEFINIDO'){
                throw new ModelsException('Perfil por defecto no puede ser eliminado');
            }
            
            # Elimina perfil
            $this->db->query("DELETE FROM tbladm_perfiles WHERE nombre='$perfil'");
            
            //actualiza como DEFINIDO a todos los usuarios para que no pierdan opciones
            $this->db->update('users',array(
              'perfil' => 'DEFINIDO'
            ),"perfil='$perfil'");

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Eliminado exitosamente'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function getIdMenu(string $controller, $metodo ){
        $url = $controller;
        $result = $this->db->query_select("SELECT id_menu FROM tbladm_submenu WHERE url LIKE '$url%' limit 1");

        if($metodo != "" AND $metodo != NULL ){
            $url.= "/".$metodo;
        }

        if($result != false){
            $result = $result[0];
            $result2 = $this->db->query_select("SELECT id_submenu FROM tbladm_submenu WHERE url='$url' limit 1");
        }else{
            $result2 = $this->db->query_select("SELECT id_menu,id_submenu FROM tbladm_submenu WHERE url='$url' limit 1");
            $result = [];
        }

        if ($result2 == false){
            $result2 = [];
        }else{
            $result2 = $result2[0];
        }


        return array_merge($result,$result2);
    }
    public function getMenuUser($id_user) {
        return $this->db->query_select("select m.id_menu,m.posi,m.seccion,m.descripcion menu,m.glyphicon,sm.id_submenu,sm.url,sm.descripcion 
        from (tbladm_perfilesuser pu inner join tbladm_menu m on pu.id_menu=m.id_menu) inner join tbladm_submenu sm on pu.id_menu=sm.id_menu and pu.id_submenu=sm.id_submenu where pu.id_user=$id_user order by m.PosI,sm.PosS");
    }
    public function getAllMenu() {
        $result = $this->db->query("SELECT m.id_menu,sm.id_submenu,m.seccion,m.descripcion menu,sm.descripcion,m.glyphicon,sm.url
        from tbladm_menu m LEFT JOIN tbladm_submenu sm ON m.id_menu=sm.id_menu  AND sm.estado=1 WHERE m.estado = 1 ORDER BY m.posi,sm.poss");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getMenuUserByPOST(){
        $user = (New Model\Users)->getUserById($this->id_user);
        if($user[0]['rol']==1)
            return $this->getAllMenu();
        else
            return $this->getMenuUser($this->id_user);
    }
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }


    // funciones globales
    public function ValidarRutCliente($rut){

        $rut = str_replace('.','',$rut);
        $rut = str_replace('-','',$rut);
        $rut = str_replace(' ','',$rut);
        $rut = str_replace('_','',$rut);
        $rut = strtoupper($rut);
        //$rut = preg_replace('/[^k0-9]/i', '', $rut);
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, strlen($rut) - 1);

        if(!is_numeric($numero)){
            return false;
        }

        $result = $this->db->query_select("SELECT digitoverificador('".$numero."') as dv");
        if($result !== false){
            if($result[0]['dv'] === $dv){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }
}