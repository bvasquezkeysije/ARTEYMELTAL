"""
Configuración común para las pruebas del sistema ARTE Y METAL.
"""
import os
from dotenv import load_dotenv

load_dotenv()

BASE_URL = os.getenv("ARTEYMETAL_URL", "https://arteymetal.online")
ADMIN_EMAIL = os.getenv("ADMIN_EMAIL", "admin@arteymetal.com")
ADMIN_PASSWORD = os.getenv("ADMIN_PASSWORD", "password")
VENDEDOR_EMAIL = os.getenv("VENDEDOR_EMAIL", "vendedor@arteymetal.com")
VENDEDOR_PASSWORD = os.getenv("VENDEDOR_PASSWORD", "password")
ALMACENERO_EMAIL = os.getenv("ALMACENERO_EMAIL", "almacen@arteymetal.com")
ALMACENERO_PASSWORD = os.getenv("ALMACENERO_PASSWORD", "password")

HEADERS = {
    "Accept": "application/json",
    "X-Requested-With": "XMLHttpRequest",
}
