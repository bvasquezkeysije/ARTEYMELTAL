"""
CP31-HU10: Exportar reporte de ventas a CSV.
"""
from config import BASE_URL


def test_exportar_reporte_ventas_csv(admin_session):
    resp = admin_session.get(f"{BASE_URL}/reportes/ventas/csv", timeout=15)
    assert resp.status_code == 200
    assert "text/csv" in resp.headers.get("Content-Type", "")
