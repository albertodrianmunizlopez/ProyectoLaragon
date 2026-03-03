import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\templates\login.html'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

content = re.sub(r'placeholder="ñ[^"]+"', 'placeholder=""', content)

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
