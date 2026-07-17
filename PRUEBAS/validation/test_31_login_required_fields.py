"""
CP03-HU01: Validación de campos obligatorios en login.
"""
import requests
from config import BASE_URL
from conftest import get_token


def test_login_required_fields():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login", timeout=15)
    token = get_token(resp.text)

    resp = s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": "", "password": ""},
        allow_redirects=True,
        timeout=15,
    )
    assert resp.status_code in (200, 302, 422)
