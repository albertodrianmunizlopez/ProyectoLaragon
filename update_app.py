import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\app.py'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

replacement = '''        # TODO: Implementar petición HTTP/REST a FastAPI para obtener el token 

        print(f"Intento de login con: {email}") # Placeholder para debug        
        
        # Redirigir al panel principal
        from flask import redirect, url_for
        return redirect(url_for('inventario'))'''

content = re.sub(
    r'# TODO: Implement.*?print\(f"Intento de login con: \{email\}"\) # Placeholder para debug\s*',
    replacement + '\n\n',
    content,
    flags=re.DOTALL
)

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
print("Updated app.py redirect")
                                