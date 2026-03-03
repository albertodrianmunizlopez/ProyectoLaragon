import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\templates\formulario_autoparte.html'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

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

# Regex replace with loose spacing
content = re.sub(
    r'<li>\s*<a href="/inventario"( class="active")?>\s*<i data-feather="box"></i>\s*Inventario / Autopartes\s*</a>\s*</li>',
    replacement,
    content
)

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
print("Fixed form html sidebar")
