import re

f = 'c:/Users/Joaquin/Desktop/trabajos upq/isay/ProyectoLaragon/proyectoFlask/templates/actualizacion_rapida.html'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

content = re.sub(r'Añ.*adir', 'Añadir', content)

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
