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

    if os.path.isdir(ruta) == False:
        jscontrollers_path = Find_Folder_jscontrollers(os.path.dirname(os.path.abspath(__file__)))
        if(jscontrollers_path == None):
            print("No se encontro la carpeta jscontrollers")
            return 0
        ruta = jscontrollers_path

    

    navegaDirectorio(accion, ruta)

def navegaDirectorio(accion, ruta):
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
            navegaDirectorio(accion, ruta + "/" + archivo)

def minificar(archivo):

    #nombre archivo
    nombre = archivo.split(".")
    #nombre archivo minificado
    nombre_min = nombre[0] + ".min.js"

    """Minifica el archivo"""
    # Abre el archivo para lectura
    archivo = open(archivo, "r")
    # Lee el archivo
    texto = archivo.read()
    # Cierra el archivo
    archivo.close()
    # Minifica el archivo

    #eliminar comentarios
    texto = re.sub(r"//.*", "", texto)
    texto = re.sub(r"/\*.*?\*/", "", texto, flags=re.DOTALL)
    texto = re.sub(r"//.*", "", texto)

    #eliminar fila import
    texto = re.sub(r"import.*", "", texto)
    #eliminar palabra export antes de const
    texto = re.sub(r"export const", "const", texto)

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


    # Abre el archivo
    archivo = open(nombre_min, "w")
    # Escribe el archivo
    archivo.write(texto)
    # Cierra el archivo
    archivo.close()


""" Funcion que navega por todas las carpetas desde su primera ubicación buscando la carpeta jscontrollers, 
si no la encuentra vuelve un nivel arriva hasta llegar a la raiz"""
def Find_Folder_jscontrollers(carpeta):
    for root, dirs, files in os.walk(carpeta):
        for name in dirs:
            if name == "jscontrollers":
                return root + "/" + name

    if(carpeta == "/"):
        return False
    else:
        return Find_Folder_jscontrollers(os.path.dirname(carpeta))


main()
