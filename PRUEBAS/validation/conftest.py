"""
Fixtures y utilidades compartidas para las pruebas de validación.
"""
import re
import pytest
import requests
from config import (
    BASE_URL,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
    VENDEDOR_EMAIL,
    VENDEDOR_PASSWORD,
)


def get_token(resp_text):
    match = re.search(r'name="_token"\s+value="([^"]+)"', resp_text)
    return match.group(1) if match else ""


def abrir_caja(session):
    for caja_id in ["1", "2", "3"]:
        resp = session.get(f"{BASE_URL}/caja", timeout=15)
        if resp.status_code != 200:
            continue
        resp = session.post(
            f"{BASE_URL}/caja",
            data={"_token": get_token(resp.text), "caja_id": caja_id, "monto_inicial": "100.00"},
            allow_redirects=True,
            timeout=15,
        )
        if resp.status_code in (200, 302):
            return True
    return False


@pytest.fixture
def session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login", timeout=15)
    token = get_token(resp.text)
    s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": ADMIN_EMAIL, "password": ADMIN_PASSWORD},
        allow_redirects=True,
        timeout=15,
    )
    return s


@pytest.fixture
def vendedor_session():
    s = requests.Session()
    s.headers.update({"X-Requested-With": "XMLHttpRequest"})
    resp = s.get(f"{BASE_URL}/login", timeout=15)
    token = get_token(resp.text)
    s.post(
        f"{BASE_URL}/login",
        data={"_token": token, "email": VENDEDOR_EMAIL, "password": VENDEDOR_PASSWORD},
        allow_redirects=True,
        timeout=15,
    )
    return s
