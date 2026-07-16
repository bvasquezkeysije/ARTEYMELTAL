"""
Pruebas de regresión.
Cubre CP36 (H01, H03, H04) y CP37 (H05, H08, H10).
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


def test_regresion_autenticacion_y_pedidos(session):
    """CP36: Regresión de autenticación y pedidos."""
    # Login
    resp = session.get(f"{BASE_URL}/dashboard")
    assert resp.status_code == 200

    # Crear pedido
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

    # Derivar por todo el flujo
    for estado in ["en_diseno", "en_produccion", "listo_entrega"]:
        resp = session.put(
            f"{BASE_URL}/pedidos/1",
            data={"_token": token, "estado": estado},
        )
        assert resp.status_code in (200, 302)


def test_regresion_ventas_caja_reportes(session):
    """CP37: Regresión de ventas, caja y reportes."""
    resp = session.get(f"{BASE_URL}/cajas")
    token_match = re.search(r'name="_token"\s+value="([^"]+)"', resp.text)
    token = token_match.group(1) if token_match else ""

    # Abrir caja
    resp = session.post(
        f"{BASE_URL}/cajas/1/abrir",
        data={"_token": token, "monto_inicial": "100.00"},
    )
    assert resp.status_code in (200, 302)

    # Registrar venta
    resp = session.post(
        f"{BASE_URL}/ventas",
        data={
            "_token": token,
            "caja_id": "1",
            "cliente_id": "1",
            "productos[0][producto_id]": "1",
            "productos[0][cantidad]": "1",
            "productos[0][precio]": "50.00",
            "metodo_pago": "efectivo",
        },
    )
    assert resp.status_code in (200, 302)

    # Cerrar caja
    resp = session.post(
        f"{BASE_URL}/cajas/1/cerrar",
        data={"_token": token},
    )
    assert resp.status_code in (200, 302)

    # Generar reporte
    resp = session.get(
        f"{BASE_URL}/reportes/ventas?fecha_inicio=2026-01-01&fecha_fin=2026-12-31"
    )
    assert resp.status_code == 200


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
