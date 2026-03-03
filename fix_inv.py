import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\templates\inventario.html'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

# See if it's already there
if 'href="/almacen/actualizar"' not in content:
    replacement = '''                    <li>

                        <a href="/inventario" class="active">

                            <i data-feather="box"></i>

                            Inventario / Autopartes

                        </a>

                    </li>
                    <li>
                        <a href="/almacen/actualizar">
                            <i data-feather="repeat"></i>
                            Actualización Rápida
                        </a>
                    </li>'''
    
    # We will just replace the block. Notice that powershell \r\n vs \n is tricky, let's use regex
    
    content = re.sub(
        r'<li>\s*<a href="/inventario" class="active">\s*<i data-feather="box"></i>\s*Inventario / Autopartes\s*</a>\s*</li>',
        replacement,
        content
    )
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print("Fixed inventario.html sidebar")
else:
    print("Already has link")
