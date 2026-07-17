"""
CP08-HU03: Crear pedido personalizado.
"""
from config import BASE_URL
from conftest import abrir_caja


def test_create_pedido_personalizado(vendedor_session):
    abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/pedidos/create", timeout=15)
    assert resp.status_code == 200
    assert "Pedido" in resp.text or "pedido" in resp.text
