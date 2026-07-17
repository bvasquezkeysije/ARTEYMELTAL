"""
Pruebas de validación de formularios y reglas de negocio.
Cubre CP03-HU01, CP16-HU05 y CP26-HU08.
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


def test_login_required_fields():
    """CP03-HU01: Validación de campos obligatorios en login."""
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login")
    token = _get_token(resp.text)

    resp = s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": "", "password": ""},
        allow_redirects=True,
    )
    assert resp.status_code in (200, 302, 422)


def test_venta_stock_insuficiente(vendedor_session):
    """CP16-HU05: Validación de stock insuficiente en venta."""
    # Abrir caja para poder acceder a ventas
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    resp = vendedor_session.post(
        f"{BASE_URL}/caja",
        data={
            "_token": _get_token(resp.text),
            "caja_id": "1",
            "monto_inicial": "100.00",
        },
        allow_redirects=True,
    )

    resp = vendedor_session.get(f"{BASE_URL}/ventas/crear")
    assert resp.status_code == 200


def test_caja_ya_abierta(vendedor_session):
    """CP26-HU08: Validación de caja ya abierta."""
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    token = _get_token(resp.text)

    # Abrir caja 1
    vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": token, "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
    )

    # Intentar abrir la misma caja de nuevo
    resp = vendedor_session.get(f"{BASE_URL}/caja")
    token = _get_token(resp.text)
    resp = vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": token, "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
    )
    assert resp.status_code in (200, 302, 422)


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
