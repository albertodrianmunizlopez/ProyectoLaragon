import glob
import re

for f in glob.glob(r'c:/Users/Joaquin/Desktop/trabajos upq/isay/ProyectoLaragon/proyectoFlask/templates/*.html'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Check if Actualizacion Rapida exist in this file
    has_link = 'Actualización Rápida' in content
    print(f"{f}: has Actualización Rápida? {has_link}")
