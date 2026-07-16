**

12. # EJECUCIÓN DE PRUEBAS

## 12.1. Herramientas y frameworks de ejecución

Para la ejecución de las pruebas del sistema ARTE Y METAL se utilizarán las siguientes herramientas:

| Tipo de prueba | Herramienta / Framework | Lenguaje | Justificación |
| --- | --- | --- | --- |
| Pruebas unitarias | pytest + requests | Python | Se validan funciones y endpoints de forma aislada usando Python, siguiendo las preferencias del docente y aprovechando la claridad de pytest. |
| Pruebas funcionales automatizadas | Playwright | Python | Framework moderno de automatización de navegadores con API estable, soporte activo y generación de traces/evidencias. |
| Pruebas de interfaz de usuario | Playwright | Python | Para validar flujos completos desde la perspectiva del usuario final. |
| Pruebas manuales | Navegador web | - | Aplicadas a flujos que requieren validación visual o interacciones complejas de negocio. |

> **Nota sobre Selenium:** No se utilizará Selenium para las pruebas automatizadas del proyecto. Aunque históricamente fue el estándar en automatización de navegadores, actualmente ha sido superado por herramientas más modernas como **Playwright**, las cuales ofrecen mejor estabilidad, ejecución más rápida, soporte nativo para múltiples navegadores, generación automática de capturas y traces, y una curva de aprendizaje más sencilla con Python.

### 12.1.1. Entorno de ejecución

- Las pruebas automatizadas con Playwright se ejecutarán desde una **laptop personal** con Python 3.10+ instalado.
- El **target** de las pruebas será el **servidor en la nube** donde está desplegada la aplicación (`https://arteymetal.online`), siguiendo lo establecido en las Partes 7 y 8 del presente plan.
- Se utilizará un entorno virtual de Python (`venv`) para aislar las dependencias de prueba.

### 12.1.2. Dependencias de ejemplo

```text
pytest>=7.0.0
pytest-playwright>=0.4.0
playwright>=1.40.0
requests>=2.31.0
python-dotenv>=1.0.0
```

### 12.1.3. Instalación rápida

```bash
python3 -m venv venv-tests
source venv-tests/bin/activate
pip install -r requirements-tests.txt
playwright install
```

## 12.2. Ejecución de pruebas unitarias

Las pruebas unitarias se ejecutarán con **Python** utilizando `pytest` y `requests`, validando funciones, endpoints y reglas de negocio de forma aislada. Esto permite mantener un lenguaje de pruebas unificado con las pruebas funcionales y seguir las preferencias del docente.

### 12.2.1. Ejemplo de prueba unitaria con Python

```python
# tests/unit/test_login_api.py
import requests


BASE_URL = "https://arteymetal.online"


def test_login_con_credenciales_validas():
    payload = {
        "login": "bvasquezkeysije@gmail.com",
        "password": "[contraseña válida]",
    }
    response = requests.post(f"{BASE_URL}/login", data=payload)
    assert response.status_code == 200 or response.is_redirect
    assert "dashboard" in response.text or response.status_code == 302


def test_login_con_credenciales_invalidas():
    payload = {
        "login": "bvasquezkeysije@gmail.com",
        "password": "contrasenaIncorrecta",
    }
    response = requests.post(f"{BASE_URL}/login", data=payload)
    assert response.status_code in (200, 422)
    assert "error" in response.text.lower() or "invalido" in response.text.lower()
```

### 12.2.2. Ejecución de pruebas unitarias

```bash
pytest tests/unit/ -v
```

| Aspecto | Descripción |
| --- | --- |
| Alcance | Endpoints críticos, validaciones de formularios, cálculos de montos y reglas de negocio. |
| Frecuencia | En cada cambio significativo del backend. |
| Evidencia | Reporte de consola de pytest. |

## 12.3. Ejecución de pruebas funcionales

### 12.3.1. Pruebas funcionales manuales

Se ejecutarán de forma manual los casos de prueba que requieren validación visual o criterios subjetivos de usabilidad.

**PRUEBA N°: Nombre (código)**

- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** [Detallar pasos]
- **c) Resultados:** [Satisfactorio / Fallido / Bloqueado]

### 12.3.2. Pruebas funcionales automatizadas con Playwright

Se automatizarán los flujos críticos del sistema usando Playwright con Python. A continuación se presenta un ejemplo base para el caso de inicio de sesión.

**Ejemplo de código con Playwright:**

```python
# tests/test_login.py
import re
from playwright.sync_api import Page, expect


def test_login_exitoso(page: Page):
    page.goto("https://arteymetal.online/login")
    page.fill("input[name='login']", "bvasquezkeysije@gmail.com")
    page.fill("input[name='password']", "[contraseña válida]")
    page.click("button[type='submit']")

    # Verifica redirección al dashboard
    expect(page).to_have_url(re.compile(".*dashboard"))
    expect(page.locator("text=Bienvenido")).to_be_visible()
```

**Ejecución:**

```bash
pytest tests/test_login.py --headed --slowmo 500
```

**PRUEBA N°: Nombre (código)**

- **a) Captura del sistema:** [Adjuntar screenshot o trace]
- **b) Código:** [Adjuntar script de Playwright]
- **c) Resultado:** [Satisfactorio / Fallido / Bloqueado]

## 12.4. Ejecución de pruebas de validación

Se verifican las reglas de validación de datos en formularios, campos obligatorios, formatos y límites.

**PRUEBA N°: Nombre (código)**

- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** [Adjuntar script de validación]
- **c) Resultados:** [Satisfactorio / Fallido / Bloqueado]

## 12.5. Ejecución de pruebas de integración

Se valida la interacción entre módulos: pedidos, producción, reparto, almacén, ventas y reportes.

**PRUEBA N°: Nombre (código)**

- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** [Adjuntar script de integración]
- **c) Resultados:** [Satisfactorio / Fallido / Bloqueado]

## 12.6. Ejecución de pruebas de regresión

Al finalizar cada sprint o corrección, se ejecutará un conjunto de pruebas de regresión para garantizar que los cambios no afecten funcionalidades previamente validadas.

```bash
pytest tests/ --browser=chromium --tracing=on
```

**PRUEBA N°: Nombre (código)**

- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** [Adjuntar script de regresión]
- **c) Resultados:** [Satisfctorio / Fallido / Bloqueado]

## 12.7. Gestión de evidencias

Todas las pruebas automatizadas generarán las siguientes evidencias:

| Evidencia | Descripción | Herramienta |
| --- | --- | --- |
| Screenshots | Capturas de pantalla en puntos clave del flujo. | Playwright `page.screenshot()` |
| Traces | Registro detallado de interacciones para reproducción. | Playwright `--tracing=on` |
| Videos | Grabación opcional de la ejecución completa. | Playwright `video: 'on'` |
| Reportes de consola | Resultados de la ejecución de pytest. | pytest |

**
