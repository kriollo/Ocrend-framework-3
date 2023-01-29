"""
Minificador de JavaScript
Se debe ingresar la carpeta donde estan ubicados los archivos a minificar
si existe un archivo llamado ".min.js" en la carpeta se sobreescribe
"""
import os
import re
import sys

Acciones = ["build", "remove"]

def main():

    accion = ""
    ruta = ""

    #validar si trae parametros
    if(len(sys.argv) > 1):
        accion = sys.argv[1]
    if(len(sys.argv) > 2):
        ruta = sys.argv[2]

    if(len(sys.argv) == 1):
        accion = "build"

    if(len(sys.argv) > 1):
        accion = sys.argv[1]

    if(len(sys.argv) > 2):
        ruta = sys.argv[2]

    if accion not in Acciones:
        accion = "build"

    if os.path.isdir(ruta) is False:
        jscontrollers_path = find_folder_jscontrollers(os.path.dirname(os.path.abspath(__file__)))
        if(jscontrollers_path is None):
            print("No se encontro la carpeta jscontrollers")
            return 0
        ruta = jscontrollers_path

    # Navega el directorio
    navegadirectorio(accion, ruta)

def navegadirectorio(accion, ruta):
    """Navega el directorio y obtiene los archivos"""

    # Obtiene la lista de archivos
    archivos = os.listdir(ruta)
    # Recorre la lista de archivos
    for archivo in archivos:
        # Verifica si el archivo es un archivo .js
        if archivo.endswith(".js") and accion == "build":
            # Minifica el archivo
            minificar(ruta + "/" + archivo)
        elif archivo.endswith(".min.js") and accion == "remove":
            #elimina el archivo .min.js
            os.remove(ruta + "/" + archivo)
        elif os.path.isdir(ruta + "/" + archivo):
            # Navega el directorio
            navegadirectorio(accion, ruta + "/" + archivo)

def minificar(file):

    #nombre archivo
    nombre = file.split(".")
    #nombre archivo minificado
    nombre_min = nombre[0] + ".min.js"

    # """Minifica el archivo"""
    # Abre el archivo para lectura
    with open(file, "r", encoding="utf-8") as archivo:
        # Lee el archivo
        texto = archivo.read()
        # Cierra el archivo
        archivo.close()
        # Minifica el archivo

        #eliminar comentarios
        texto = re.sub(r"//.*", "", texto)
        texto = re.sub(r"/\*.*?\*/", "", texto, flags=re.DOTALL)
        texto = re.sub(r"//.*", "", texto)

        #eliminar los console.log
        texto = re.sub(r"console.log\(.*?\);", "", texto, flags=re.DOTALL)

        #agregar .min a archivos js dentro del archivo js
        texto = re.sub(r".js(?![a-zA-Z])", ".min.js", texto, 0, re.MULTILINE)
        #eliminar el doble min
        texto = re.sub(r"\.min.min.js", ".min.js", texto)

        #eliminar tabulaciones
        texto = re.sub(r"\t", "", texto)
        #eliminar saltos de linea
        texto = re.sub(r"\n", "", texto)
        #eliminar espacios
        texto = re.sub(r"\s+", " ", texto)

        #eliminar espacios antes de {
        texto = re.sub(r"\s{", "{", texto)
        #eliminar espacios antes de }
        texto = re.sub(r"\s}", "}", texto)
        #eliminar espacios antes de (
        texto = re.sub(r"\s\(", "(", texto)
        #eliminar espacios antes de )
        texto = re.sub(r"\s\)", ")", texto)
        #eliminar espacios antes de [
        texto = re.sub(r"\s\[", "[", texto)
        #eliminar espacios antes de ]
        texto = re.sub(r"\s\]", "]", texto)
        #eliminar espacios antes de +
        texto = re.sub(r"\s\+", "+", texto)
        #eliminar espacios antes de -
        texto = re.sub(r"\s\-", "-", texto)
        #eliminar espacios antes de *
        texto = re.sub(r"\s\*", "*", texto)
        #eliminar espacios antes de /
        texto = re.sub(r"\s\/", "/", texto)
        #eliminar espacios antes de =
        texto = re.sub(r"\s\=", "=", texto)
        #eliminar espacios antes de ;
        texto = re.sub(r"\s\;", ";", texto)
        #eliminar espacios antes de :
        #texto = re.sub(r"\s\:", ":", texto)
        #eliminar espacios antes de <
        texto = re.sub(r"\s\<", "<", texto)
        #eliminar espacios antes de >
        texto = re.sub(r"\s\>", ">", texto)
        #eliminar espacios antes de ,
        texto = re.sub(r"\s\,", ",", texto)

        #eliminar espacios despues de {
        texto = re.sub(r"{\s", "{", texto)
        #eliminar espacios despues de }
        texto = re.sub(r"}\s", "}", texto)
        #eliminar espacios despues de (
        texto = re.sub(r"\(\s", "(", texto)
        #eliminar espacios despues de )
        texto = re.sub(r"\)\s", ")", texto)
        #eliminar espacios despues de [
        texto = re.sub(r"\[\s", "[", texto)
        #eliminar espacios despues de ]
        texto = re.sub(r"\]\s", "]", texto)
        #eliminar espacios despues de +
        texto = re.sub(r"\+\s", "+", texto)
        #eliminar espacios despues de -
        texto = re.sub(r"\-\s", "-", texto)
        #eliminar espacios despues de *
        texto = re.sub(r"\*\s", "*", texto)
        #eliminar espacios despues de /
        texto = re.sub(r"\/\s", "/", texto)
        #eliminar espacios despues de =
        texto = re.sub(r"\=\s", "=", texto)
        #eliminar espacios despues de ;
        texto = re.sub(r"\;\s", ";", texto)
        #eliminar espacios despues de :
        texto = re.sub(r"\:\s", ":", texto)
        #eliminar espacios despues de >
        texto = re.sub(r">\s", ">", texto)
        #eliminar espacios despues de <
        texto = re.sub(r"<\s", "<", texto)
        #eliminar espacios despues de ,
        texto = re.sub(r",\s", ",", texto)

        #agrega espacio para )in
        texto = re.sub(r"\)in", ") in", texto)
        #agregar ; para )$(
        texto = re.sub(r"\)\$", ");$", texto)
        #agregar ; para )setTimeout
        texto = re.sub(r"\)setTimeout", ");setTimeout", texto)
        #agregar ; para )setInterval
        texto = re.sub(r"\)setInterval", ");setInterval", texto)
        #agregar ; para )if
        texto = re.sub(r"\)if", ");if", texto)
        #agregar ; para )Form
        texto = re.sub(r"\)Form", ");Form", texto)
        #agregar ; para )localStorage
        texto = re.sub(r"\)localStorage", ");localStorage", texto)

    # Eliminar archivo antes de crearlo
    if os.path.exists(nombre_min):
        os.remove(nombre_min)

    # Abre el archivo
    with open(nombre_min, "w",encoding="utf-8") as newarchivo:
        # Escribe el archivo
        newarchivo.write(texto)
        # Cierra el archivo
        newarchivo.close()



#Funcion que navega por todas las carpetas desde su primera ubicación buscando la carpeta jscontrollers,
#si no la encuentra vuelve un nivel arriva hasta llegar a la raiz
#return path de la carpeta jscontrollers

def find_folder_jscontrollers(carpeta):
    for root, dirs, files in os.walk(carpeta):
        for name in dirs:
            if name == "jscontrollers":
                return root + "/" + name

    if(carpeta == "/"):
        return False

    return find_folder_jscontrollers(os.path.dirname(carpeta))


main()
