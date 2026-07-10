import os

import pytest
from dotenv import load_dotenv
from playwright.sync_api import sync_playwright


load_dotenv()


@pytest.fixture(scope="session")
def settings():
    return {
        "base_url": os.getenv("BASE_URL", "http://127.0.0.1:8000").rstrip("/"),
        "login_user": os.getenv("LOGIN_USER", ""),
        "login_email": os.getenv("LOGIN_EMAIL", ""),
        "login_password": os.getenv("LOGIN_PASSWORD", ""),
        "inactive_login": os.getenv("INACTIVE_LOGIN", ""),
        "inactive_password": os.getenv("INACTIVE_PASSWORD", ""),
        "browser_executable": os.getenv(
            "BROWSER_EXECUTABLE",
            r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        ),
    }


@pytest.fixture(scope="session")
def browser(settings):
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(
            headless=True,
            executable_path=settings["browser_executable"],
        )
        yield browser
        browser.close()


@pytest.fixture
def page(browser):
    context = browser.new_context()
    page = context.new_page()
    yield page
    context.close()
