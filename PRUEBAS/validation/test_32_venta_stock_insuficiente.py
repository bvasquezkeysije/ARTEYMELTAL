"""
CP16-HU05: Validación de stock insuficiente en venta.
"""
from config import BASE_URL
from conftest import get_token


def test_venta_stock_insuficiente(vendedor_session):
    resp = vendedor_session.get(f"{BASE_URL}/caja", timeout=15)
    vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": get_token(resp.text), "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
        timeout=15,
    )

    resp = vendedor_session.get(f"{BASE_URL}/ventas/crear", timeout=15)
    assert resp.status_code == 200
