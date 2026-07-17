"""
CP34: Flujo completo pedido-venta-almacén (H03, H04, H05, H09).
"""
from config import BASE_URL
from conftest import abrir_caja


def test_flujo_pedido_venta_almacen(vendedor_session, session):
    abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/pedidos/create", timeout=15)
    assert resp.status_code == 200

    resp = vendedor_session.get(f"{BASE_URL}/ventas", timeout=15)
    assert resp.status_code == 200

    resp = session.get(f"{BASE_URL}/almacen", timeout=15)
    assert resp.status_code == 200
