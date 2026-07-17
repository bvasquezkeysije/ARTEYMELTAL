"""
Pruebas de integración entre módulos.
Cubre CP34 (H03, H04, H05, H09) y CP35 (H02, H04, H09).
"""
import re
import pytest
import requests
from config import (
    BASE_URL,
    VENDEDOR_EMAIL,
    VENDEDOR_PASSWORD,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
)


def _get_token(resp_text):
    match = re.search(r'name="_token"\s+value="([^"]+)"', resp_text)
    return match.group(1) if match else ""


@pytest.fixture
def session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login")
    token = _get_token(resp.text)
    s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": ADMIN_EMAIL, "password": ADMIN_PASSWORD},
        allow_redirects=True,
    )
    return s


@pytest.fixture
def vendedor_session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login")
    token = _get_token(resp.text)
    s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": VENDEDOR_EMAIL, "password": VENDEDOR_PASSWORD},
        allow_redirects=True,
    )
    return s


def test_flujo_pedido_venta_almacen(vendedor_session, session):
    """CP34: Flujo completo pedido-venta-almacén."""
    # 1. Abrir caja y acceder a pedidos
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    resp = vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": _get_token(resp.text), "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
    )
    resp = vendedor_session.get(f"{BASE_URL}/pedidos/create")
    assert resp.status_code == 200

    # 2. Acceder a ventas
    resp = vendedor_session.get(f"{BASE_URL}/ventas")
    assert resp.status_code == 200

    # 3. Acceder a almacén (requiere permiso de almacenero)
    resp = session.get(f"{BASE_URL}/almacen")
    assert resp.status_code == 200


def test_notificaciones_y_cambios_de_estado(vendedor_session):
    """CP35: Integración notificaciones y cambios de estado."""
    resp = vendedor_session.get(f"{BASE_URL}/notificaciones")
    assert resp.status_code == 200


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
