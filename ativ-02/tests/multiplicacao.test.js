// Testes Multiplicação
const { multiplicacao } = require('../calculator');

describe('Testes Multiplicação', () => {
  test('deve multiplicar 4 * 3 resultando em 12', () => {
    expect(multiplicacao(4, 3)).toBe(12);
  });

  test('deve retornar 0 ao multiplicar qualquer número por zero', () => {
    expect(multiplicacao(9, 0)).toBe(0);
  });

  test('deve multiplicar número positivo por número negativo', () => {
    expect(multiplicacao(5, -3)).toBe(-15);
  });

  test('deve multiplicar dois números negativos resultando em positivo', () => {
    expect(multiplicacao(-4, -5)).toBe(20);
  });

  test('deve multiplicar números decimais corretamente', () => {
    expect(multiplicacao(2.5, 4)).toBe(10);
  });
});
