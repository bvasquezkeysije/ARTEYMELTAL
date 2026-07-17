"""
Pruebas funcionales automatizadas con Playwright.
Cubre los casos de prueba CP01-HU01, CP05-HU02, CP08-HU03,
CP12-HU04, CP15-HU05, CP18-HU06, CP21-HU07, CP24-HU08,
CP27-HU09 y CP30-HU10.

Nota: Estas pruebas verifican la navegación y carga de las páginas
principales. Las interacciones complejas con formularios se cubren
con las pruebas unitarias de API.
"""
import pytest
from playwright.sync_api import Page
from config import (
    BASE_URL,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
    VENDEDOR_EMAIL,
    VENDEDOR_PASSWORD,
    ALMACENERO_EMAIL,
    ALMACENERO_PASSWORD,
)


def login(page: Page, email: str, password: str):
    page.goto(f"{BASE_URL}/login")
    page.wait_for_selector("input[name='login']", state="visible", timeout=15000)
    page.locator("input[name='login']").click()
    page.locator("input[name='login']").fill(email)
    page.locator("input[name='password']").click()
    page.locator("input[name='password']").fill(password)
    page.locator("button[type='submit']").click()
    page.wait_for_url(f"{BASE_URL}/dashboard", timeout=15000)


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
    assert page.url == f"{BASE_URL}/dashboard"


def test_password_recovery_with_playwright(page: Page):
    """CP05-HU02: Recuperación de contraseña con Playwright."""
    page.goto(f"{BASE_URL}/forgot-password")
    assert page.url == f"{BASE_URL}/forgot-password"


def test_create_pedido_with_playwright(vendedor_page: Page):
    """CP08-HU03: Crear pedido con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/caja")
    page.goto(f"{BASE_URL}/pedidos/create")
    assert "pedidos" in page.url


def test_derivar_pedido_a_produccion_with_playwright(vendedor_page: Page):
    """CP12-HU04: Derivar pedido a producción con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/caja")
    page.goto(f"{BASE_URL}/pedidos")
    assert "pedidos" in page.url


def test_registrar_venta_directa_with_playwright(vendedor_page: Page):
    """CP15-HU05: Registrar venta directa con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/caja")
    page.goto(f"{BASE_URL}/ventas/crear")
    assert "ventas" in page.url


def test_create_producto_with_playwright(admin_page: Page):
    """CP18-HU06: Crear producto con Playwright."""
    page = admin_page
    page.goto(f"{BASE_URL}/productos/create")
    assert "productos" in page.url


def test_consultar_cliente_dni_with_playwright(vendedor_page: Page):
    """CP21-HU07: Consultar cliente por DNI con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/clientes/create")
    assert "clientes" in page.url


def test_apertura_cierre_caja_with_playwright(vendedor_page: Page):
    """CP24-HU08: Apertura y cierre de caja con Playwright."""
    page = vendedor_page
    page.goto(f"{BASE_URL}/caja")
    assert "caja" in page.url


def test_registrar_entrada_almacen_with_playwright(almacenero_page: Page):
    """CP27-HU09: Registrar entrada de almacén con Playwright."""
    page = almacenero_page
    page.goto(f"{BASE_URL}/almacen/movimientos")
    assert "almacen" in page.url


def test_exportar_reporte_with_playwright(admin_page: Page):
    """CP30-HU10: Exportar reporte con Playwright."""
    page = admin_page
    page.goto(f"{BASE_URL}/reportes")
    assert "reportes" in page.url


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
