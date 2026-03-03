import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\templates\login.html'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

# Fix the specific words corrupted
content = re.sub(r'Contrase.*a', 'Contraseña', content)
content = re.sub(r'Sesi.*n', 'Sesión', content)
content = re.sub(r'Electr.*nico', 'Electrónico', content)

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
print("Fixed login.html accents")
