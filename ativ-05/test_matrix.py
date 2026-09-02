def test_soma():
    assert 1 + 1 == 2

def test_multiplicacao():
    assert 3 * 3 == 9

def test_versao_python():
    import sys
    assert sys.version_info >= (3, 10)
