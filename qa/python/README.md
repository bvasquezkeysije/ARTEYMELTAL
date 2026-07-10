# QA Python - starter

Base inicial para automatizacion de pruebas con Python sobre `Sistema-ArteyMetal`.

## Stack

- `pytest`
- `playwright`
- `python-dotenv`
- `pytest-html`

## Caso inicial

El primer flujo automatizado es `login`, porque cubre:

- `RF-01`
- `RFC-01`
- `HU01`

## Estructura

```text
qa/python/
  .env.example
  requirements.txt
  pytest.ini
  conftest.py
  tests/
    e2e/
      test_login.py
```

## Ejecucion esperada

```bash
cd qa/python
pip install -r requirements.txt
playwright install
pytest
```

## Variables de entorno

Configurar en `.env`:

- `BASE_URL`
- `LOGIN_USER`
- `LOGIN_EMAIL`
- `LOGIN_PASSWORD`
- `INACTIVE_LOGIN`
- `INACTIVE_PASSWORD`

## Notas

- Este starter no modifica la app.
- La idea es crecer luego a `clientes`, `pedidos` y `ventas`.
