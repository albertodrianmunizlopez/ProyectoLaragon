import glob

# Ensure actualizacion rapida link is present in all sidebars
for f in glob.glob(r'c:/Users/Joaquin/Desktop/trabajos upq/isay/ProyectoLaragon/proyectoFlask/templates/*.html'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Check if 'Actualización Rápida' is missing in the sidebar nav block
    if '<nav class=\"sidebar-nav\">' in content and 'Actualización Rápida' not in content:
        # We find the Inventario block to insert after it
        content = content.replace(
            '<a href=\"/inventario\" class=\"active\">\n                              <i data-feather=\"box\"></i>\n                              Inventario / Autopartes\n                          </a>\n                      </li>',
            '<a href=\"/inventario\" class=\"active\">\n                              <i data-feather=\"box\"></i>\n                              Inventario / Autopartes\n                          </a>\n                      </li>\n                      <li>\n                          <a href=\"/almacen/actualizar\">\n                              <i data-feather=\"repeat\"></i>\n                              Actualización Rápida\n                          </a>\n                      </li>'
        )
        content = content.replace(
            '<a href=\"/inventario\">\n                              <i data-feather=\"box\"></i>\n                              Inventario / Autopartes\n                          </a>\n                      </li>',
            '<a href=\"/inventario\">\n                              <i data-feather=\"box\"></i>\n                              Inventario / Autopartes\n                          </a>\n                      </li>\n                      <li>\n                          <a href=\"/almacen/actualizar\">\n                              <i data-feather=\"repeat\"></i>\n                              Actualización Rápida\n                          </a>\n                      </li>'
        )
        print(f"Added Actualizacion rapida to {f}")
        with open(f, 'w', encoding='utf-8') as file:
            file.write(content)
