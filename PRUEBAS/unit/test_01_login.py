"""
CP01-HU01: Inicio de sesión con credenciales válidas.
"""
import requests
from config import BASE_URL, ADMIN_EMAIL, ADMIN_PASSWORD


def test_login_valid_credentials():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})

    resp = s.get(f"{BASE_URL}/login", timeout=15)
    resp.raise_for_status()

    from conftest import get_token
    s.post(
        f"{BASE_URL}/login",
        data={"_token": get_token(resp.text), "email": ADMIN_EMAIL, "password": ADMIN_PASSWORD},
        allow_redirects=True,
        timeout=15,
    )

    resp = s.get(f"{BASE_URL}/dashboard", timeout=15)
    assert resp.status_code == 200
    assert "Dashboard" in resp.text or "Panel" in resp.text
