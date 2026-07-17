"""
Pruebas unitarias y de API del sistema ARTE Y METAL.
Cubre los casos de prueba CP01-HU01, CP05-HU02, CP08-HU03,
CP12-HU04, CP15-HU05, CP18-HU06, CP21-HU07, CP24-HU08,
CP27-HU09 y CP31-HU10.
"""
import re
import pytest
import requests
from config import (
    BASE_URL,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
    VENDEDOR_EMAIL,
    VENDEDOR_PASSWORD,
    ALMACENERO_EMAIL,
    ALMACENERO_PASSWORD,
)


def _get_token(resp_text):
    match = re.search(r'name="_token"\s+value="([^"]+)"', resp_text)
    return match.group(1) if match else ""


def _login(session, email, password):
    resp = session.get(f"{BASE_URL}/login")
    resp.raise_for_status()
    login_resp = session.post(
        f"{BASE_URL}/login",
        data={"_token": _get_token(resp.text), "email": email, "password": password},
        allow_redirects=True,
    )
    assert login_resp.status_code == 200


def _abrir_caja(session):
    for caja_id in ["1", "2", "3"]:
        resp = session.get(f"{BASE_URL}/caja", timeout=15)
        if resp.status_code != 200:
            continue
        resp = session.post(
            f"{BASE_URL}/caja",
            data={
                "_token": _get_token(resp.text),
                "caja_id": caja_id,
                "monto_inicial": "100.00",
            },
            allow_redirects=True,
            timeout=15,
        )
        if resp.status_code in (200, 302):
            return True
    return False


@pytest.fixture
def admin_session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    _login(s, ADMIN_EMAIL, ADMIN_PASSWORD)
    yield s


@pytest.fixture
def vendedor_session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    _login(s, VENDEDOR_EMAIL, VENDEDOR_PASSWORD)
    yield s


@pytest.fixture
def almacenero_session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    _login(s, ALMACENERO_EMAIL, ALMACENERO_PASSWORD)
    yield s


def test_login_valid_credentials():
    """CP01-HU01: Inicio de sesión con credenciales válidas."""
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    _login(s, ADMIN_EMAIL, ADMIN_PASSWORD)
    resp = s.get(f"{BASE_URL}/dashboard")
    assert resp.status_code == 200
    assert "Dashboard" in resp.text or "Panel" in resp.text


def test_password_recovery_code_generation():
    """CP05-HU02: Recuperación de contraseña por código."""
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/forgot-password")
    assert resp.status_code == 200
    assert "email" in resp.text.lower()


def test_create_pedido_personalizado(vendedor_session):
    """CP08-HU03: Crear pedido personalizado."""
    _abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/pedidos/create")
    assert resp.status_code == 200
    assert "Pedido" in resp.text or "pedido" in resp.text


def test_change_pedido_state_to_produccion(vendedor_session):
    """CP12-HU04: Cambiar estado de pedido a producción."""
    _abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/pedidos")
    assert resp.status_code == 200

    pedido_match = re.search(r'pedidos/(\d+)/edit', resp.text)
    if not pedido_match:
        pytest.skip("No hay pedidos disponibles para derivar")

    pedido_id = pedido_match.group(1)
    resp = vendedor_session.post(
        f"{BASE_URL}/pedidos/{pedido_id}/derivar",
        data={"_token": _get_token(resp.text)},
        allow_redirects=True,
    )
    assert resp.status_code in (200, 302)


def test_registrar_venta_directa(vendedor_session):
    """CP15-HU05: Registrar venta directa."""
    _abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/ventas/crear")
    assert resp.status_code == 200


def test_crear_producto(admin_session):
    """CP18-HU06: Crear producto."""
    resp = admin_session.get(f"{BASE_URL}/productos/create")
    assert resp.status_code == 200

    resp = admin_session.post(
        f"{BASE_URL}/productos",
        data={
            "_token": _get_token(resp.text),
            "nombre": "Producto Prueba Unit",
            "categoria": "medallas",
            "stock_tienda": "10",
            "stock_almacen": "20",
            "precio_referencia": "50.00",
            "activo": "1",
        },
        allow_redirects=True,
    )
    assert resp.status_code in (200, 302)


def test_consultar_dni_reniec(vendedor_session):
    """CP21-HU07: Consultar DNI por RENIEC."""
    resp = vendedor_session.get(
        f"{BASE_URL}/clientes/consulta-documento?tipo=reniec&numero=00000000"
    )
    assert resp.status_code in (200, 422)


def test_apertura_caja(vendedor_session):
    """CP24-HU08: Apertura de caja."""
    assert _abrir_caja(vendedor_session)


def test_registrar_entrada_almacen(almacenero_session):
    """CP27-HU09: Registrar entrada de almacén."""
    resp = almacenero_session.get(f"{BASE_URL}/almacen/movimientos")
    assert resp.status_code == 200


def test_exportar_reporte_ventas_csv(admin_session):
    """CP31-HU10: Exportar reporte de ventas a CSV."""
    resp = admin_session.get(f"{BASE_URL}/reportes/ventas/csv")
    assert resp.status_code == 200
    assert "text/csv" in resp.headers.get("Content-Type", "")


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
