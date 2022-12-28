<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

/**
 * Helper con funciones útiles para el tratamiento de archivos.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

class Files extends \Twig_Extension {

  /**
   * Devuelve un string con el contenido de un archivo
   *
   * @param string $dir: Directorio del archivo a leer
   *
   * @return string con contenido del archivo
   */
  public static function read_file(string $dir) : string {
    $lines = '';
    $f = new \SplFileObject($dir);
    while (!$f->eof()) {
        $lines .= $f->fgets();
    }

    return (string) $lines;
  }

  /**
   * Escribe un string completo en un archivo, si este no existe lo crea
   *
   * @param string $dir: Directorio del archivo escribir/crear
   * @param string $content: Contenido a escribir
   *
   * @return int catidad de bytes escritos en el archivo
   */
  public static function write_file(string $dir, string $content) : int {
    $f = new \SplFileObject($dir,'w');
    return (int) $f->fwrite($content);
  }

  /**
   * Escribe un string al final, en un archivo existente
   *
   * @param string $dir: Directorio del archivo sobre el cual se va a escribir
   * @param string $content: Contenido a escribir
   *
   * @return int : catidad de bytes escritos en el archivo
   */
  public static function write_in_file(string $dir, string $content) : int {
    $f = new \SplFileObject($dir,'a+');
    return (int) $f->fwrite("\n\r" . $content);
  }

  /**
   * Borra un archivo en un directorio
   *
   * @param string $route: Ruta del fichero
   *
   * @return bool true si borró el fichero, false si no (porque no existía)
   */
  public static function delete_file(string $route) : bool {
    if (file_exists($route)) {
      unlink($route);

      return true;
    }

    return false;
  }

  /**
   * Devuelve la extensión de un archivo cualquiera, da igual si es solo el nombre o la ruta con el nombre
   *
   * @param string $file_name: Nombre del archivo, da igual si es solo el nombre o la ruta con el nombre
   *
   * @return mixed string con la extensión, devuelve un string '' si no existe información alguna acerca de la extensión
  */
  public static function get_file_ext(string $file_name) {
    return pathinfo($file_name, PATHINFO_EXTENSION);
  }

  /**
   * Dice si un elemento es una imagen o no según su extensión
   *
   * @param string $file_name: Nombre del archivo, da igual si es solo el nombre o la ruta con el nombre
   *
   * @return bool true si es una imagen, false si no lo es
  */
  public static function is_image(string $file_name) : bool {
    return (bool) in_array(self::get_file_ext($file_name), ['jpg', 'png', 'jpeg', 'gif', 'JPG', 'PNG', 'JPEG', 'GIF']);
  }

  /**
   * Devuelve el tamaño en Kbytes de un fichero
   *
   * @param string $file: path del fichero
   *
   * @return int con el tamaño del fichero
   */
  public static function file_size(string $file) : int {
  	return (int) round(filesize($file)*0.0009765625, 1);
  }

  /**
   * Devuelve la fecha y hora exacta de creación de un fichero
   *
   * @param string $file: path del fichero
   *
   * @return string con la fecha del fichero en el formato d-m-y h:i:s
  */
  public static function date_file(string $file) : string {
  	return date('d-m-Y h:i:s', filemtime($file));
  }

  /**
   * Devuelve en un arreglo numérico, la ruta de todos los ficheros en un directorio filtrado por tipos
   *
   * @param string $dir: directorio completo
   * @param string $types: tipos de archivos a buscar, por defecto '' significa todos, se puede pasar por ejemplo 'jpg'
   *
   * @return array con las rutas de todos los ficheros encontrados, un array vacío si no encontró ficheros
  */
  public static function get_files_in_dir(string $dir, string $types = '') : array {
    $array = array();
    if (is_dir($dir)) {
      foreach (glob($dir . '*' . $types) as $file) {
        $array[] = $file;
      }
    }
    return $array;
  }

  /**
   * Crea un directorio
   *
   * @param string $dir: Directorio a crear
   * @param int $permisos: Permisos del directorio a crear, por defecto es 0655
   *
   * @return bool con true si fue creado con éxito, false si el directorio ya existía o hubo algún error
  */
  public static function create_dir(string $dir, int $permisos = 0655) : bool {
    if(is_dir($dir)) {
      return false;
    }
    
    return (bool) mkdir($dir,$permisos,true);
  }

  /**
   * Valida si Existe Directorio
   *
   * @param string $dir: Directorio a consultar
   * @param int $permisos: Permisos del directorio a crear, por defecto es 0655
   *
   * @return bool con true si fue creado con éxito, false si el directorio ya existía o hubo algún error
  */
  public static function exists_dir(string $dir) : bool {
    if(is_dir($dir)) {
      return (bool) true;
    }

    return false;
  }


  /**
   * Valida si existe un archivo
   *
   * @param string $path_file: ruta y archivo a eliminar
   *
   * @return bool true si existe el archivo, false si no 
  */
  public static function exists_file(string $path_file) : bool {
    if (file_exists($path_file)) {
      return true;
    }

    return false;
  }


  /**
   * cambia nombre de archivo
   *
   * @param string $path_file: ruta y archivo a modificar
   * @param string $new_name: nuevo nombre del archivo
   *
   * @return bool true si se modificó el nombre, false si no
  */
  public static function rename_file(string $path_file, string $new_name) : bool {
    if (file_exists($path_file)) {
      rename($path_file, $new_name);

      return true;
    }

    return false;
  }


  /**
   * Elimina de forma recursiva un directorio con su contenido
   *
   * @author brandonwamboldt
   *
   * @param string $dir: Directorio a borrar
   *
   * @return bool true si todo se borró con éxito
   */
  public static function rm_dir(string $dir) : bool {

    # Evitar una desgracia
    if (in_array($dir, [
      'api',
      'api/http',
      'app',
      'app/controllers',
      'app/models',
      'app/templates',
      'Ocrend/',
      'Ocrend/Kernel',
      'Ocrend/vendor',
      'Ocrend/KernelConfig',
      'Ocrend/Kernel/Controllers',
      'Ocrend/Kernel/Database',
      'Ocrend/Kernel/Helpers',
      'Ocrend/Kernel/Models',
      'Ocrend/Kernel/Router'
    ])) {
      throw new \RuntimeException('No puede eliminar la ruta ' . $dir . ', ya que es crítica.');
    }

    foreach (glob($dir . "/*") as $archivos_carpeta) { 
      if (is_dir($archivos_carpeta)) {
        self::rm_dir($archivos_carpeta);
      } else {
        unlink($archivos_carpeta);
      }
    }
    rmdir($dir);

    return true;
  }

  /**
   * Devuelve la cantidad de imágenes contenidas dentro de un directorio
   *
   * @param string $dir: Directorio en donde están las imagenes
   *
   * @return int cantidad de  imágenes
   */
  public static function images_in_dir(string $dir) : int {
    return sizeof(glob($dir . '{*.jpg,*.gif,*.png,*.gif,*.jpeg,*.JPG,*.GIF,*.PNG,*.JPEG}', GLOB_BRACE));
  }

  //------------------------------------------------

  /**
   * Copia todos los ficheros de un directorio a un directorio nuevo, si el directorio nuevo no existe, es creado.
   * Si en el directorio nuevo existe un archivo con el mismo nombre de alguno en el viejo, este será sobreescrito.
   *
   * @param string $old_dir: Ruta del directorio viejo (de donde se moverán los ficheros)
   * @param string $new_dir: Ruta del directorio nuevo (hacia donde se moverán los ficheros)
   * @param bool $only_images: Pasar como TRUE, si sólo quiere pasar imagenes
   * @param bool $delete_old: Pasar como TRUE, si se quiere borrar todo el contenido del viejo directorio al pasarse
   *
   * @return void
   */
  public static function move_from_dir(string $old_dir, string $new_dir, bool $only_images = false, bool $delete_old = false)  {
    self::create_dir($new_dir);

    foreach (glob($old_dir . ($only_images ? '{*.jpg,*.gif,*.png,*.gif,*.jpeg,*.JPG,*.GIF,*.PNG,*.JPEG}' : '*'), GLOB_BRACE) as $file) {
      if (file_exists($file)) {
          unlink($file);
      }
      
      $name = explode('/', $file);
      $name = end($name);
      copy($file, $new_dir . $name);

      if ($delete_old) {
        unlink($file);
      }
    }
  }

  /**
   * Busca un archivo desde una ruta base devolviendose carpetas hasta llegar a la raiz
   * @param mixed $basePath
   * @param mixed $filename
   * @param mixed $targetPath
   * @return bool|string
   */
  public static function findFile($root, $filename, $targetPath) {
    // Comprobamos si el archivo existe en la ruta base
    if (file_exists($targetPath . '/' . $filename)) {
        return $targetPath . '/' . $filename;
    }

    // Si no existe, comprobamos si la ruta base es la raíz del volumen
    if ($targetPath == $root) {
        // Si la ruta base es la raíz del volumen, devolvemos FALSE
        return FALSE;
    }

    // Si no es la raíz del volumen, buscamos el archivo en la carpeta padre de la ruta base
    $parentPath = dirname($targetPath);
    return self::findFile($root, $filename, $parentPath);
  }

  /**
   * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
   *
   * @return array con todas las funciones con sus respectivos nombres de acceso en plantillas twig
   */
  public function getFunctions() : array {
    return array(
      new \Twig_Function('images_in_dir', array($this, 'images_in_dir')),
      new \Twig_Function('get_files_in_dir', array($this, 'get_files_in_dir')),
      new \Twig_Function('date_file', array($this, 'date_file')),
      new \Twig_Function('file_size', array($this, 'file_size')),
      new \Twig_Function('is_image', array($this, 'is_image')),
      new \Twig_Function('get_file_ext', array($this, 'get_file_ext'))
    );
  }

  /**
   * Identificador único para la extensión de twig
   *
   * @return string con el nombre de la extensión
   */
  public function getName() : string {
    return 'ocrend_framework_helper_files';
  }

}