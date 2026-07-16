"""
Pruebas de integración entre módulos.
Cubre CP34 (H03, H04, H05, H09) y CP35 (H02, H04, H09).
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


def test_flujo_pedido_venta_almacen(session):
    """CP34: Flujo completo pedido-venta-almacén."""
    # 1. Crear pedido
    resp = session.get(f"{BASE_URL}/pedidos/create")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.post(
        f"{BASE_URL}/pedidos",
        data={
            "_token": token,
            "cliente_id": "1",
            "caja_id": "1",
            "tipo_entrega": "local",
            "estado_pago": "pendiente_adelanto",
            "productos[0][producto_id]": "1",
            "productos[0][cantidad]": "1",
            "productos[0][precio]": "100.00",
        },
    )
    assert resp.status_code in (200, 302)

    # 2. Derivar a producción
    resp = session.put(
        f"{BASE_URL}/pedidos/1",
        data={"_token": token, "estado": "en_produccion"},
    )
    assert resp.status_code in (200, 302)

    # 3. Registrar venta relacionada
    resp = session.post(
        f"{BASE_URL}/ventas",
        data={
            "_token": token,
            "caja_id": "1",
            "cliente_id": "1",
            "productos[0][producto_id]": "1",
            "productos[0][cantidad]": "1",
            "productos[0][precio]": "100.00",
            "metodo_pago": "efectivo",
        },
    )
    assert resp.status_code in (200, 302)

    # 4. Verificar stock en almacén
    resp = session.get(f"{BASE_URL}/almacen/stock/1")
    assert resp.status_code == 200


def test_notificaciones_y_cambios_de_estado(session):
    """CP35: Integración notificaciones y cambios de estado."""
    # Derivar pedido
    resp = session.get(f"{BASE_URL}/pedidos/1/edit")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    resp = session.put(
        f"{BASE_URL}/pedidos/1",
        data={"_token": token, "estado": "en_produccion"},
    )
    assert resp.status_code in (200, 302)

    # Verificar notificaciones
    resp = session.get(f"{BASE_URL}/notificaciones")
    assert resp.status_code == 200


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
