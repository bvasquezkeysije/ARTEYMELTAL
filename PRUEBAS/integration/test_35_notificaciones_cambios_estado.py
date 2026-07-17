"""
CP35: Integración notificaciones y cambios de estado (H02, H04, H09).
"""
from config import BASE_URL


def test_notificaciones_y_cambios_de_estado(vendedor_session):
    resp = vendedor_session.get(f"{BASE_URL}/notificaciones", timeout=15)
    assert resp.status_code == 200
