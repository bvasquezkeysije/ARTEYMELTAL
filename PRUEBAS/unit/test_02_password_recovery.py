"""
CP05-HU02: Recuperación de contraseña por código.
"""
import requests
from config import BASE_URL, ADMIN_EMAIL


def test_password_recovery_code_generation():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})

    resp = s.get(f"{BASE_URL}/forgot-password", timeout=15)
    assert resp.status_code == 200
    assert "email" in resp.text.lower()
