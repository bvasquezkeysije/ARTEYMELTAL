import time


LOGIN_ROUTE = "/login"
PRODUCTOS_ROUTE = "/productos"
PRODUCTOS_CREATE_ROUTE = "/productos/create"


def open_login(page, settings):
    page.goto(f"{settings['base_url']}{LOGIN_ROUTE}", wait_until="domcontentloaded")


def submit_login(page, login_value, password_value):
    page.locator("#login").fill(login_value)
    page.locator("#password").fill(password_value)
    page.get_by_role("button", name="Entrar al sistema").click()
    page.wait_for_load_state("networkidle")


def login(page, settings):
    open_login(page, settings)
    submit_login(page, settings["login_user"], settings["login_password"])
    assert "/dashboard" in page.url


def unique_producto_data():
    suffix = str(int(time.time() * 1000))[-6:]
    return {
        "nombre": f"Producto QA {suffix}",
        "descripcion": f"Descripcion QA {suffix}",
        "precio": "19.90",
        "stock": "12",
    }


def test_rf10_hu07_registrar_producto_valido(page, settings):
    """
    Cobertura:
    - RF-10 Gestion de productos
    - HU07 Gestion de productos
    - RFC-05 Gestion de productos, categorias, imagenes y stock
    """
    login(page, settings)

    page.goto(f"{settings['base_url']}{PRODUCTOS_CREATE_ROUTE}", wait_until="domcontentloaded")
    page.wait_for_load_state("networkidle")

    data = unique_producto_data()

    page.locator("#nombre").fill(data["nombre"])
    page.locator("#categoria").select_option(index=1)
    page.locator("#precio_referencia").fill(data["precio"])
    page.locator("#stock_actual").fill(data["stock"])
    page.locator("#descripcion").fill(data["descripcion"])
    page.get_by_role("button", name="Guardar producto").click()
    page.wait_for_load_state("networkidle")

    assert page.locator("text=Producto registrado correctamente.").count() > 0
    assert page.locator(f"text={data['nombre']}").count() > 0
    assert page.locator("text=PROD-").count() > 0


def test_hu07_producto_creado_aparece_en_busqueda(page, settings):
    login(page, settings)

    page.goto(f"{settings['base_url']}{PRODUCTOS_CREATE_ROUTE}", wait_until="domcontentloaded")
    page.wait_for_load_state("networkidle")

    data = unique_producto_data()

    page.locator("#nombre").fill(data["nombre"])
    page.locator("#categoria").select_option(index=1)
    page.locator("#precio_referencia").fill(data["precio"])
    page.locator("#stock_actual").fill(data["stock"])
    page.locator("#descripcion").fill(data["descripcion"])
    page.get_by_role("button", name="Guardar producto").click()
    page.wait_for_load_state("networkidle")

    search_input = page.locator("form#search-form input[name='q']")
    search_input.fill(data["nombre"])
    page.get_by_title("Buscar").click()
    page.wait_for_load_state("networkidle")

    assert page.locator(f"text={data['nombre']}").count() > 0
    assert page.locator(f"text={data['stock']}").count() > 0
