"""
CP15-HU05: Registrar venta directa.
"""
from config import BASE_URL
from conftest import abrir_caja


def test_registrar_venta_directa(vendedor_session):
    abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/ventas/crear", timeout=15)
    assert resp.status_code == 200
