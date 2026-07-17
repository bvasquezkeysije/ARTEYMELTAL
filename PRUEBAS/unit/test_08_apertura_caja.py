"""
CP24-HU08: Apertura de caja.
"""
from conftest import abrir_caja


def test_apertura_caja(vendedor_session):
    assert abrir_caja(vendedor_session)
