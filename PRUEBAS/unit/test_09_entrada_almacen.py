"""
CP27-HU09: Registrar entrada de almacén.
"""
from config import BASE_URL


def test_registrar_entrada_almacen(almacenero_session):
    resp = almacenero_session.get(f"{BASE_URL}/almacen/movimientos", timeout=15)
    assert resp.status_code == 200
