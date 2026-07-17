"""
Pruebas de regresión.
Cubre CP36 (H01, H03, H04) y CP37 (H05, H08, H10).
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


def test_regresion_autenticacion_y_pedidos(vendedor_session):
    """CP36: Regresión de autenticación y pedidos."""
    # Login
    resp = vendedor_session.get(f"{BASE_URL}/dashboard")
    assert resp.status_code == 200

    # Abrir caja
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    resp = vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": _get_token(resp.text), "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
    )

    # Crear pedido
    resp = vendedor_session.get(f"{BASE_URL}/pedidos/create")
    assert resp.status_code == 200

    # Listar pedidos
    resp = vendedor_session.get(f"{BASE_URL}/pedidos")
    assert resp.status_code == 200


def test_regresion_ventas_caja_reportes(vendedor_session, session):
    """CP37: Regresión de ventas, caja y reportes."""
    # Abrir caja
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    resp = vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": _get_token(resp.text), "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
    )

    # Acceder a ventas
    resp = vendedor_session.get(f"{BASE_URL}/ventas/crear")
    assert resp.status_code == 200

    # Cerrar caja
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    apertura_match = re.search(r'caja/(\d+)/cerrar', resp.text)
    if apertura_match:
        apertura_id = apertura_match.group(1)
        resp = vendedor_session.post(
            f"{BASE_URL}/caja/{apertura_id}/cerrar",
            data={"_token": _get_token(resp.text), "monto_final": "100.00"},
            allow_redirects=True,
        )
        assert resp.status_code in (200, 302)

    # Generar reporte (admin)
    resp = session.get(f"{BASE_URL}/reportes")
    assert resp.status_code == 200


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
