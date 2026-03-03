import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\templates\login.html'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

# Fix the over-replaced words
content = content.replace('Contraseñabel>', 'Contraseña</label>')
content = content.replace('ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢ñ¢â¬Â¢', '')

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
print("Adjusted login.html accents properly")
