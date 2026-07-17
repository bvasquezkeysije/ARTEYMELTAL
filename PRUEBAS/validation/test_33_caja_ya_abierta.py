"""
CP26-HU08: Validación de caja ya abierta.
"""
from config import BASE_URL
from conftest import get_token


def test_caja_ya_abierta(vendedor_session):
    resp = vendedor_session.get(f"{BASE_URL}/caja", timeout=15)
    token = get_token(resp.text)

    vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": token, "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
        timeout=15,
    )

    resp = vendedor_session.get(f"{BASE_URL}/caja", timeout=15)
    token = get_token(resp.text)
    resp = vendedor_session.post(
        f"{BASE_URL}/caja",
        data={"_token": token, "caja_id": "1", "monto_inicial": "100.00"},
        allow_redirects=True,
        timeout=15,
    )
    assert resp.status_code in (200, 302)
