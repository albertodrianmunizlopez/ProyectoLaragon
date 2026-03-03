import glob

for f in glob.glob(r'c:/Users/Joaquin/Desktop/trabajos upq/isay/ProyectoLaragon/proyectoFlask/templates/*.html'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Fix the literal \n replacements made by Powershell
    content = content.replace(r'\n                            <i data-feather="shopping-cart"></i>\n                            Pedidos\n                        </a>', '\n                            <i data-feather="shopping-cart"></i>\n                            Pedidos\n                        </a>')
    
    content = content.replace(r'\n                            <i data-feather="repeat"></i>\n                            Actualización Rápida\n                        </a>', '\n                            <i data-feather="repeat"></i>\n                            Actualización Rápida\n                        </a>')

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
