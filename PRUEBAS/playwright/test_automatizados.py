"""
Pruebas funcionales automatizadas con Playwright.
Cubre los casos de prueba CP01-HU01, CP05-HU02, CP08-HU03,
CP12-HU04, CP15-HU05, CP18-HU06, CP21-HU07, CP24-HU08,
CP27-HU09 y CP30-HU10.
"""
import re
import pytest
from playwright.sync_api import Page, expect
from config import BASE_URL, ADMIN_EMAIL, ADMIN_PASSWORD, VENDEDOR_EMAIL, VENDEDOR_PASSWORD, ALMACENERO_EMAIL, ALMACENERO_PASSWORD


def login(page: Page, email: str, password: str):
    page.goto(f"{BASE_URL}/login")
    page.locator("input[name='email']").fill(email)
    page.locator("input[name='password']").fill(password)
    page.locator("button[type='submit']").click()
    page.wait_for_url(f"{BASE_URL}/dashboard")


@pytest.fixture
def admin_page(page: Page):
    login(page, ADMIN_EMAIL, ADMIN_PASSWORD)
    yield page


@pytest.fixture
def vendedor_page(page: Page):
    login(page, VENDEDOR_EMAIL, VENDEDOR_PASSWORD)
    yield page


@pytest.fixture
def almacenero_page(page: Page):
    login(page, ALMACENERO_EMAIL, ALMACENERO_PASSWORD)
    yield page


def test_login_with_playwright(page: Page):
    """CP01-HU01: Inicio de sesión con Playwright."""
    login(page, ADMIN_EMAIL, ADMIN_PASSWORD)
    expect(page.locator("body")).to_contain_text("Dashboard")


def test_password_recovery_with_playwright(page: Page):
    """CP05-HU02: Recuperación de contraseña con Playwright."""
    page.goto(f"{BASE_URL}/forgot-password")
    page.locator("input[name='email']").fill(ADMIN_EMAIL)
    page.locator("button[type='submit']").click()
    expect(page.locator("body")).to_contain_text("código")


def test_create_pedido_with_playwright(vendedor_page: Page):
    """CP08-HU03: Crear pedido con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/pedidos/create")
    page.locator("select[name='cliente_id']").select_option(index=1)
    page.locator("select[name='caja_id']").select_option(index=1)
    page.locator("button[type='submit']").click()
    expect(page.locator("body")).to_contain_text("PED-")


def test_derivar_pedido_a_produccion_with_playwright(vendedor_page: Page):
    """CP12-HU04: Derivar pedido a producción con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/pedidos")
    page.locator("a[href*='/pedidos/']").first.click()
    page.locator("select[name='estado']").select_option("en_produccion")
    page.locator("button[type='submit']").click()
    expect(page.locator("body")).to_contain_text("en producción")


def test_registrar_venta_directa_with_playwright(vendedor_page: Page):
    """CP15-HU05: Registrar venta directa con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/ventas/create")
    page.locator("select[name='cliente_id']").select_option(index=1)
    page.locator("select[name='producto_id']").select_option(index=1)
    page.locator("input[name='cantidad']").fill("1")
    page.locator("button[type='submit']").click()
    expect(page.locator("body")).to_contain_text("VENT-")


def test_create_producto_with_playwright(admin_page: Page):
    """CP18-HU06: Crear producto con Playwright."""
    page = admin_page
    page.goto(f"{BASE_URL}/productos/create")
    page.locator("input[name='nombre']").fill("Producto Playwright")
    page.locator("select[name='categoria_id']").select_option(index=1)
    page.locator("input[name='precio']").fill("99.99")
    page.locator("button[type='submit']").click()
    page.wait_for_url(re.compile(r".*/productos.*"))
    expect(page.locator("body")).to_contain_text("Producto Playwright")


def test_consultar_cliente_dni_with_playwright(vendedor_page: Page):
    """CP21-HU07: Consultar cliente por DNI con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/clientes/create")
    page.locator("input[name='numero_documento']").fill("00000000")
    page.locator("button#btn-consultar-documento").click()
    expect(page.locator("body")).to_contain_text("RENIEC")


def test_apertura_cierre_caja_with_playwright(vendedor_page: Page):
    """CP24-HU08: Apertura y cierre de caja con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/cajas")
    page.locator("button.btn-abrir-caja").first.click()
    page.locator("input[name='monto_inicial']").fill("200.00")
    page.locator("button[type='submit']").click()
    expect(page.locator("body")).to_contain_text("abierta")


def test_registrar_entrada_almacen_with_playwright(almacenero_page: Page):
    """CP27-HU09: Registrar entrada de almacén con Playwright."""
    page = almacenero_page
    page.goto(f"{BASE_URL}/almacen/movimientos/create")
    page.locator("select[name='tipo']").select_option("entrada")
    page.locator("select[name='producto_id']").select_option(index=1)
    page.locator("input[name='cantidad']").fill("10")
    page.locator("button[type='submit']").click()
    expect(page.locator("body")).to_contain_text("Movimiento")


def test_exportar_reporte_with_playwright(admin_page: Page):
    """CP30-HU10: Exportar reporte con Playwright."""
    page = admin_page
    page.goto(f"{BASE_URL}/reportes/ventas")
    page.locator("input[name='fecha_inicio']").fill("2026-01-01")
    page.locator("input[name='fecha_fin']").fill("2026-12-31")
    with page.expect_download() as download_info:
        page.locator("button#btn-exportar-csv").click()
    download = download_info.value
    assert download.suggested_filename.endswith(".csv")


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
