// Testes Subtração
const { subtracao } = require('../calculator');

describe('Testes Subtração', () => {
  test('deve subtrair 10 - 4 resultando em 6', () => {
    expect(subtracao(10, 4)).toBe(6);
  });

  test('deve retornar número negativo quando o subtraendo for maior', () => {
    expect(subtracao(3, 8)).toBe(-5);
  });

  test('deve subtrair números decimais corretamente', () => {
    expect(subtracao(5.5, 2.5)).toBe(3);
  });

  test('deve subtrair 0 sem alterar o número', () => {
    expect(subtracao(10, 0)).toBe(10);
  });

  test('deve converter textos numéricos e subtrair corretamente', () => {
    expect(subtracao('20', '8')).toBe(12);
  });
});
