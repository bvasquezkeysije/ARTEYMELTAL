"""
CP18-HU06: Crear producto.
"""
from config import BASE_URL
from conftest import get_token


def test_crear_producto(admin_session):
    resp = admin_session.get(f"{BASE_URL}/productos/create", timeout=15)
    assert resp.status_code == 200

    resp = admin_session.post(
        f"{BASE_URL}/productos",
        data={
            "_token": get_token(resp.text),
            "nombre": "Producto Prueba Unit",
            "categoria": "medallas",
            "stock_tienda": "10",
            "stock_almacen": "20",
            "precio_referencia": "50.00",
            "activo": "1",
        },
        allow_redirects=True,
        timeout=15,
    )
    assert resp.status_code in (200, 302)
