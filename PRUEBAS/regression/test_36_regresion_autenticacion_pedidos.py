"""
CP36: Regresión de autenticación y pedidos (H01, H03, H04).
"""
from config import BASE_URL
from conftest import abrir_caja


def test_regresion_autenticacion_y_pedidos(vendedor_session):
    resp = vendedor_session.get(f"{BASE_URL}/dashboard", timeout=15)
    assert resp.status_code == 200

    abrir_caja(vendedor_session)

    resp = vendedor_session.get(f"{BASE_URL}/pedidos/create", timeout=15)
    assert resp.status_code == 200

    resp = vendedor_session.get(f"{BASE_URL}/pedidos", timeout=15)
    assert resp.status_code == 200
