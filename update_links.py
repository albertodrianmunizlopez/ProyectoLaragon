import os
import glob
import re

folder = r'c:\\Users\\Joaquin\\Desktop\\trabajos upq\\isay\\ProyectoLaragon\\proyectoFlask\\templates\\*.html'

for filepath in glob.glob(folder):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We look for <a href="#"> that encloses Reportes
    new_content = re.sub(
        r'<a\s+href=[\"\']#[\"\']>\s*<i\s+data-feather=[\"\']bar-chart-2[\"\']></i>\s*Reportes\s*</a>',
        r'<a href=\"/reportes\">\n                            <i data-feather=\"bar-chart-2\"></i>\n                            Reportes\n                        </a>',
        content
    )
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'Updated {filepath}')
