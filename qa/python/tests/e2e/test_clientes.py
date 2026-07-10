import json
import time


LOGIN_ROUTE = "/login"
CLIENTES_ROUTE = "/clientes"


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


def open_clientes(page, settings):
    page.goto(f"{settings['base_url']}{CLIENTES_ROUTE}", wait_until="domcontentloaded")
    page.wait_for_load_state("networkidle")


def unique_cliente_data():
    suffix = str(int(time.time() * 1000))[-8:]
    return {
        "nombre": f"Cliente QA {suffix}",
        "documento": suffix,
        "telefono": f"9{suffix[:8]}",
        "correo": f"cliente.qa.{suffix}@mail.test",
        "direccion": f"Calle QA {suffix}",
        "observaciones": f"Registro QA {suffix}",
    }


def open_new_client_modal(page):
    page.get_by_title("Nuevo cliente").click()
    page.locator("#nuevo-cliente-form").wait_for(state="visible")


def fill_client_form(page, data):
    page.locator("#nuevo-cliente-form #nombre_completo").fill(data["nombre"])
    page.locator("#nuevo-cliente-form #documento").fill(data["documento"])
    page.locator("#nuevo-cliente-form #telefono").fill(data["telefono"])
    page.locator("#nuevo-cliente-form #correo").fill(data["correo"])
    page.locator("#nuevo-cliente-form #direccion").fill(data["direccion"])
    page.locator("#nuevo-cliente-form #observaciones").fill(data["observaciones"])


def create_client(page, data):
    open_new_client_modal(page)
    fill_client_form(page, data)
    page.get_by_role("button", name="Guardar cliente").click()
    page.wait_for_load_state("networkidle")


def search_client(page, query):
    search_input = page.locator("form#search-form input[name='q']")
    search_input.fill(query)
    page.get_by_title("Buscar").click()
    page.wait_for_load_state("networkidle")


def test_rf07_hu05_registrar_cliente_valido(page, settings):
    """
    Cobertura:
    - RF-07 Registro de clientes
    - HU05 Gestion de clientes
    """
    login(page, settings)
    open_clientes(page, settings)

    cliente = unique_cliente_data()
    create_client(page, cliente)

    assert page.locator("text=Cliente registrado correctamente.").count() > 0
    assert page.locator(f"text={cliente['nombre']}").count() > 0


def test_rf08_hu05_busqueda_cliente_por_documento(page, settings):
    """
    Cobertura:
    - RF-08 Busqueda de clientes
    - HU05 Gestion de clientes
    """
    login(page, settings)
    open_clientes(page, settings)

    cliente = unique_cliente_data()
    create_client(page, cliente)

    search_client(page, cliente["documento"])

    assert page.locator(f"text={cliente['nombre']}").count() > 0
    assert page.locator(f"text={cliente['documento']}").count() > 0


def test_hu05_no_permite_documento_duplicado(page, settings):
    login(page, settings)
    open_clientes(page, settings)

    cliente = unique_cliente_data()
    create_client(page, cliente)

    open_new_client_modal(page)
    fill_client_form(page, cliente)
    page.get_by_role("button", name="Guardar cliente").click()
    page.wait_for_load_state("networkidle")

    assert page.locator("text=The documento has already been taken.").count() > 0 or page.locator("text=ya ha sido registrado").count() > 0


def test_rf09_hu06_consulta_documento_cliente_local(page, settings):
    """
    Cobertura:
    - RF-09 Validacion de DNI y RUC
    - HU06 Validacion de DNI/RUC
    """
    login(page, settings)
    open_clientes(page, settings)

    cliente = unique_cliente_data()
    create_client(page, cliente)

    result = page.evaluate(
        """async (numero) => {
            const response = await fetch(`/clientes/consulta-documento?numero=${numero}`, {
                credentials: 'same-origin'
            });
            return {
                status: response.status,
                body: await response.text(),
            };
        }""",
        cliente["documento"],
    )

    payload = json.loads(result["body"])

    assert result["status"] == 200
    assert payload["ok"] is True
    assert payload["fuente"] == "local"
    assert payload["cliente"]["documento"] == cliente["documento"]
    assert payload["cliente"]["nombre"] == cliente["nombre"]


def test_hu06_consulta_documento_invalido(page, settings):
    login(page, settings)

    result = page.evaluate(
        """async () => {
            const response = await fetch('/clientes/consulta-documento?numero=123', {
                credentials: 'same-origin'
            });
            return {
                status: response.status,
                body: await response.text(),
            };
        }"""
    )

    payload = json.loads(result["body"])

    assert result["status"] == 422
    assert payload["ok"] is False
    assert "8 digitos" in payload["message"]
