"""
Pruebas de validación de formularios y reglas de negocio.
Cubre CP03-HU01, CP16-HU05 y CP26-HU08.
"""
import re
import pytest
import requests
from config import BASE_URL, ADMIN_EMAIL, ADMIN_PASSWORD


@pytest.fixture
def session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""
    s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": ADMIN_EMAIL, "password": ADMIN_PASSWORD},
    )
    return s


def test_login_required_fields():
    """CP03-HU01: Validación de campos obligatorios en login."""
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": "", "password": ""},
    )
    assert resp.status_code == 302 or "correo" in resp.text.lower()


def test_venta_stock_insuficiente(session):
    """CP16-HU05: Validación de stock insuficiente en venta."""
    resp = session.get(f"{BASE_URL}/ventas/create")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/ventas",
        data={
            "_token": token,
            "caja_id": "1",
            "cliente_id": "1",
            "productos[0][producto_id]": "1",
            "productos[0][cantidad]": "999999",
            "productos[0][precio]": "10.00",
            "metodo_pago": "efectivo",
        },
    )
    assert resp.status_code in (200, 422, 302)
    assert "stock" in resp.text.lower() or "insuficiente" in resp.text.lower()


def test_caja_ya_abierta(session):
    """CP26-HU08: Validación de caja ya abierta."""
    resp = session.get(f"{BASE_URL}/cajas")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/cajas/1/abrir",
        data={"_token": token, "monto_inicial": "100.00"},
    )
    assert resp.status_code in (200, 302, 422)
    assert "abierta" in resp.text.lower() or "ya" in resp.text.lower()


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
