from urllib.parse import urlparse

import pytest


LOGIN_ROUTE = "/login"


def open_login(page, settings):
    page.goto(f"{settings['base_url']}{LOGIN_ROUTE}", wait_until="domcontentloaded")


def submit_login(page, login_value, password_value):
    page.locator("#login").fill(login_value)
    page.locator("#password").fill(password_value)
    page.get_by_role("button", name="Entrar al sistema").click()


def assert_on_dashboard(page):
    page.wait_for_load_state("networkidle")
    assert "/dashboard" in page.url


def test_rf01_hu01_login_con_usuario_valido(page, settings):
    """
    Cobertura:
    - RF-01 Inicio de sesion
    - RFC-01 Autenticacion segura
    - HU01 Inicio de sesion
    """
    open_login(page, settings)
    submit_login(page, settings["login_user"], settings["login_password"])
    assert_on_dashboard(page)


def test_rf01_hu01_login_con_correo_valido(page, settings):
    open_login(page, settings)
    submit_login(page, settings["login_email"], settings["login_password"])
    assert_on_dashboard(page)


def test_rf01_hu01_login_invalido_muestra_error(page, settings):
    open_login(page, settings)
    submit_login(page, settings["login_user"], "clave-invalida")
    page.wait_for_load_state("networkidle")
    assert page.locator("text=Estas credenciales no coinciden").count() > 0 or page.locator("text=These credentials do not match").count() > 0


def test_rf01_hu01_usuario_inactivo_no_debe_ingresar(page, settings):
    if not settings["inactive_login"] or not settings["inactive_password"]:
        pytest.skip("No hay credenciales configuradas para un usuario inactivo.")

    open_login(page, settings)
    submit_login(page, settings["inactive_login"], settings["inactive_password"])
    page.wait_for_load_state("networkidle")
    assert page.locator("text=Tu usuario esta inactivo").count() > 0
    assert urlparse(page.url).path.endswith("/login")


def test_hu01_logout_cierra_sesion(page, settings):
    open_login(page, settings)
    submit_login(page, settings["login_user"], settings["login_password"])
    assert_on_dashboard(page)

    page.get_by_role("button", name=settings["login_user"]).click()
    page.get_by_role("button", name="Cerrar sesion").click()
    page.wait_for_load_state("networkidle")
    assert urlparse(page.url).path.endswith("/login") or urlparse(page.url).path == "/"
