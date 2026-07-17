"""
Configuración común para las pruebas del sistema ARTE Y METAL.

Crear un archivo .env en la carpeta PRUEBAS/ con los valores reales.
Ver PRUEBAS/.env.example como referencia.
"""
import os
from dotenv import load_dotenv

load_dotenv()

BASE_URL = os.getenv("ARTEYMETAL_URL", "https://arteymetal.online")

ADMIN_EMAIL = os.getenv("ADMIN_EMAIL", "")
ADMIN_PASSWORD = os.getenv("ADMIN_PASSWORD", "")

VENDEDOR_EMAIL = os.getenv("VENDEDOR_EMAIL", "")
VENDEDOR_PASSWORD = os.getenv("VENDEDOR_PASSWORD", "")

ALMACENERO_EMAIL = os.getenv("ALMACENERO_EMAIL", "")
ALMACENERO_PASSWORD = os.getenv("ALMACENERO_PASSWORD", "")

HEADERS = {
    "Accept": "application/json",
    "X-Requested-With": "XMLHttpRequest",
}
