"""
CP12-HU04: Cambiar estado de pedido a producción.
"""
import re
import pytest
from config import BASE_URL
from conftest import abrir_caja, get_token


def test_change_pedido_state_to_produccion(vendedor_session):
    abrir_caja(vendedor_session)
    resp = vendedor_session.get(f"{BASE_URL}/pedidos", timeout=15)
    assert resp.status_code == 200

    pedido_match = re.search(r'pedidos/(\d+)/edit', resp.text)
    if not pedido_match:
        pytest.skip("No hay pedidos disponibles para derivar")

    pedido_id = pedido_match.group(1)
    resp = vendedor_session.post(
        f"{BASE_URL}/pedidos/{pedido_id}/derivar",
        data={"_token": get_token(resp.text)},
        allow_redirects=True,
        timeout=15,
    )
    assert resp.status_code in (200, 302)
