# PRUEBAS - ARTE Y METAL

Esta carpeta contiene el código fuente de las pruebas realizadas para el sistema **ARTE Y METAL**.

## Estructura

| Carpeta | Contenido |
| --- | --- |
| `unit/` | Pruebas unitarias y de API con `pytest` + `requests`. |
| `playwright/` | Pruebas funcionales automatizadas con Playwright. |
| `validation/` | Pruebas de validación de formularios y reglas de negocio. |
| `integration/` | Pruebas de integración entre módulos. |
| `regression/` | Pruebas de regresión. |
| `performance/` | Pruebas de carga, estrés y picos. |
| `manual/` | Matriz de pruebas manuales. |

## Instalación

```bash
python -m venv venv
source venv/bin/activate  # Windows: venv\Scripts\activate
pip install -r requirements.txt
playwright install
```

## Ejecución

```bash
# Pruebas unitarias
pytest unit/

# Pruebas automatizadas con Playwright
pytest playwright/

# Pruebas de validación
pytest validation/

# Pruebas de integración
pytest integration/

# Pruebas de regresión
pytest regression/

# Pruebas de rendimiento (ejecutar cada script por separado)
python performance/test_carga.py
python performance/test_estres.py
python performance/test_picos.py
```

## Configuración

Editar `config.py` con la URL del sistema y credenciales de prueba.

## Nota

Los scripts están preparados para ejecutarse contra el servidor en la nube. Antes de ejecutar, asegurarse de que el servidor esté disponible y de que las credenciales de prueba sean correctas.
