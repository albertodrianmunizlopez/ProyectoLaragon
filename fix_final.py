import glob

replacements = {
    'Añadir': 'Añadir',
    'ContraseñƒÂa': 'Contraseña',
    'ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢': '',
    'PestañƒÂas': 'Pestañas',
    'PequeñƒÂa': 'Pequeña'
}

for f in glob.glob(r'c:/Users/Joaquin/Desktop/trabajos upq/isay/ProyectoLaragon/proyectoFlask/templates/*.html'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    for k, v in replacements.items():
        content = content.replace(k, v)

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f'Fixed {f}')
