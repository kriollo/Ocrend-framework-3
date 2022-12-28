# Ocrend-Framework 3.2

## Introducción
### ¿Qué es Ocrend Framework 3.2? (compatible con PHP8)

Es un framework sencillo y robusto, escrito en **PHP 7** que utiliza la arquitectura **MVC** y componentes de symfony como base de su aplicación en el desarrollo web, adicionalmente pretende acelerar el proceso de desarrollo con unas cuantas herramientas. La curva de aprendizaje es bastante baja, el concepto del framework es ofrecer una arquitectura de sencillo manejo, inclusive para aquellos que jamás han programado utilizando MVC.

Desarrollado por [Brayan Narváez] https://github.com/prinick96 y mantenido por [Jorge Jara H.] https://github.com/kriollo

## Requisitos

Para colocar el framework se requiere un servidor que cumpla con las siguientes características:

* PHP 7 (Actualmente compatible con PHP 8)
* APACHE 2 (debes tener habilitada la opción de **mod_rewrite**)
* MYSQL 5.5 (o superior) o MARIADB 10.0 (o superior)

## Instalación

Es muy simple instalar Ocrend Framework, solo debes seguir los siguientes pasos:

el repositorio de github es el siguiente: [https://github.com/kriollo/Ocrend-framework-3.2]
puedes descargarlo en formato zip o clonarlo con git.

1. Para clonar el repositorio en tu servidor web utiliza el siguiente comando:
    - git clone https://github.com/kriollo/Ocrend-framework-3.2

2. Una vez clonado el repositorio, debes configurar el archivo **Ocrend.ini.yml** que se encuentra en la carpeta **./Ocrend/Kernel/Config/** . En este archivo se encuentran las configuraciones básicas del framework, como el nombre de la aplicación, el nombre de la base de datos, el usuario y la contraseña de la base de datos, entre otras cosas.

# Configuración

Abrir el fichero **./Ocrend/Kernel/Config/Ocrend.ini.yml**

## Configuración de la base de datos
debes buscar el siguiente apartado en el archivo de configuración y modificarlo de acuerdo a tus necesidades, por ejemplo si usas mysql:

```yml
mysql:
    host: 127.0.0.1
    user: usuario
    pass: contraseña
    name: base_de_datos
    port: default
    socket: default
````


# Configuración del sitio

site:
    production: Establecer en true, sólamente cuando esté en el servidor de producción
    name: Nombre de su aplicación web
    url: URL completa para acceder al framework, es importante el "/" del final

```yml
build:
    production: Establecer en true, sólamente cuando esté en el servidor de producción
    name: Nombre de su aplicación web
    url: URL completa para acceder al framework, es importante el "/" del final (http://127.0.0.1/nombre_proyecto/) o (https://127.0.0.1/nombre_proyecto/)
````

# Configuración de ruta

```yml
router:
    ssl: establecer en true, para especificar si se trabaja con el protocolo HTTPS
    roots: carpeta raiz del proyecto (nombre_proyecto)
    path: ruta de la instalación (127.0.0.1/proyectos/nombre_proyecto/)
````


# Documentación del framework
Actualmente no existe una documentación oficial del framework, pero puedes encontrar documentación ded las versiones anteriores en el siguiente enlace: [https://youtu.be/ZFvz4cTCdjg] - Canal de Youtube de Brayan Narváez

Estamos trabajando en la documentación oficial del framework, pero mientras tanto puedes revisar el código fuente del framework para entender su funcionamiento.

# Cómo contribuir

- Realizar un fork
- Crear una rama con el nombre del feature o bugfix
- Realizar el pull request de la rama
- Esperar por el merge
- ayuda con la documentación oficial del framework
