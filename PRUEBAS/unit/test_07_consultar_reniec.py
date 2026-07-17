"""
CP21-HU07: Consultar DNI por RENIEC.
"""
from config import BASE_URL


def test_consultar_dni_reniec(vendedor_session):
    resp = vendedor_session.get(
        f"{BASE_URL}/clientes/consulta-documento?tipo=reniec&numero=00000000",
        timeout=15,
    )
    assert resp.status_code in (200, 422)
