import re

f = r'c:\Users\Joaquin\Desktop\trabajos upq\isay\ProyectoLaragon\proyectoFlask\app.py'
with open(f, 'r', encoding='utf-8') as file:
    content = file.read()

# Fix the mess and mojibake in that function
def fix_login(m):
    return '''@app.route('/', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        # Aquí se extraerán los datos para enviarlos a la API de FastAPI      
        email = request.form.get('email')
        password = request.form.get('password')

        # TODO: Implementar petición HTTP/REST a FastAPI para obtener el token 

        print(f"Intento de login con: {email}") # Placeholder para debug        

        from flask import redirect, url_for
        return redirect(url_for('inventario'))

    return render_template('login.html')'''

content = re.sub(r'@app\.route\(\'/\', methods=\[\'GET\', \'POST\'\]\).*?return render_template\(\'login\.html\'\)', fix_login, content, flags=re.DOTALL)

with open(f, 'w', encoding='utf-8') as file:
    file.write(content)
print("Fixed app.py login redirect properly")
