"""
Pruebas unitarias y de API del sistema ARTE Y METAL.
Cubre los casos de prueba CP01-HU01, CP05-HU02, CP08-HU03,
CP12-HU04, CP15-HU05, CP18-HU06, CP21-HU07, CP24-HU08,
CP27-HU09 y CP31-HU10.
"""
import re
import pytest
import requests
from config import BASE_URL, ADMIN_EMAIL, ADMIN_PASSWORD


@pytest.fixture
def session():
    """Sesión autenticada contra el sistema."""
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login")
    assert resp.status_code == 200

    html = resp.text
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', html)
    token = token_match.group(1) if token_match else ""

    login_resp = s.post(
        f"{BASE_URL}/login",
        data={
            "_token": token,
            "email": ADMIN_EMAIL,
            "password": ADMIN_PASSWORD,
        },
    )
    assert login_resp.status_code in (200, 302)
    return s


def test_login_valid_credentials(session):
    """CP01-HU01: Inicio de sesión con credenciales válidas."""
    resp = session.get(f"{BASE_URL}/dashboard")
    assert resp.status_code == 200
    assert "dashboard" in resp.text.lower() or "panel" in resp.text.lower()


def test_password_recovery_code_generation():
    """CP05-HU02: Recuperación de contraseña por código."""
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})

    resp = s.get(f"{BASE_URL}/forgot-password")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = s.post(
        f"{BASE_URL}/forgot-password",
        data={"_token": token, "email": ADMIN_EMAIL},
    )
    assert resp.status_code in (200, 302)


def test_create_pedido_personalizado(session):
    """CP08-HU03: Crear pedido personalizado."""
    resp = session.get(f"{BASE_URL}/pedidos/create")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/pedidos",
        data={
            "_token": token,
            "cliente_id": "1",
            "caja_id": "1",
            "tipo_entrega": "local",
            "estado_pago": "pendiente_adelanto",
            "productos[0][producto_id]": "1",
            "productos[0][cantidad]": "1",
            "productos[0][precio]": "100.00",
        },
    )
    assert resp.status_code in (200, 302)


def test_change_pedido_state_to_produccion(session):
    """CP12-HU04: Cambiar estado de pedido a producción."""
    resp = session.get(f"{BASE_URL}/pedidos/1/edit")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.put(
        f"{BASE_URL}/pedidos/1",
        data={"_token": token, "estado": "en_produccion"},
    )
    assert resp.status_code in (200, 302)


def test_registrar_venta_directa(session):
    """CP15-HU05: Registrar venta directa."""
    resp = session.get(f"{BASE_URL}/ventas/create")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/ventas",
        data={
            "_token": token,
            "caja_id": "1",
            "cliente_id": "1",
            "productos[0][producto_id]": "1",
            "productos[0][cantidad]": "1",
            "productos[0][precio]": "100.00",
            "metodo_pago": "efectivo",
        },
    )
    assert resp.status_code in (200, 302)


def test_crear_producto(session):
    """CP18-HU06: Crear producto."""
    resp = session.get(f"{BASE_URL}/productos/create")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/productos",
        data={
            "_token": token,
            "nombre": "Producto Prueba Unit",
            "categoria_id": "1",
            "stock_tienda": "10",
            "stock_almacen": "20",
            "precio": "50.00",
        },
    )
    assert resp.status_code in (200, 302)


def test_consultar_dni_reniec(session):
    """CP21-HU07: Consultar DNI por RENIEC."""
    resp = session.get(f"{BASE_URL}/consulta-documento?tipo=reniec&numero=00000000")
    assert resp.status_code in (200, 422)


def test_apertura_caja(session):
    """CP24-HU08: Apertura de caja."""
    resp = session.get(f"{BASE_URL}/cajas")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/cajas/1/abrir",
        data={"_token": token, "monto_inicial": "100.00"},
    )
    assert resp.status_code in (200, 302)


def test_registrar_entrada_almacen(session):
    """CP27-HU09: Registrar entrada de almacén."""
    resp = session.get(f"{BASE_URL}/almacen/movimientos/create")
    assert resp.status_code == 200

    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/almacen/movimientos",
        data={
            "_token": token,
            "tipo": "entrada",
            "producto_id": "1",
            "cantidad": "5",
            "motivo": "Prueba unitaria",
        },
    )
    assert resp.status_code in (200, 302)


def test_exportar_reporte_ventas_csv(session):
    """CP31-HU10: Exportar reporte de ventas a CSV."""
    resp = session.get(
        f"{BASE_URL}/reportes/ventas/exportar/csv?fecha_inicio=2026-01-01&fecha_fin=2026-12-31"
    )
    assert resp.status_code == 200
    assert "text/csv" in resp.headers.get("Content-Type", "")


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
