"""
CP37: Regresión de ventas, caja y reportes (H05, H08, H10).
"""
import re
from config import BASE_URL
from conftest import abrir_caja, get_token


def test_regresion_ventas_caja_reportes(vendedor_session, session):
    abrir_caja(vendedor_session)

    resp = vendedor_session.get(f"{BASE_URL}/ventas/crear", timeout=15)
    assert resp.status_code == 200

    resp = vendedor_session.get(f"{BASE_URL}/caja", timeout=15)
    apertura_match = re.search(r'caja/(\d+)/cerrar', resp.text)
    if apertura_match:
        apertura_id = apertura_match.group(1)
        resp = vendedor_session.post(
            f"{BASE_URL}/caja/{apertura_id}/cerrar",
            data={"_token": get_token(resp.text), "monto_final": "100.00"},
            allow_redirects=True,
            timeout=15,
        )
        assert resp.status_code in (200, 302)

    resp = session.get(f"{BASE_URL}/reportes", timeout=15)
    assert resp.status_code == 200
