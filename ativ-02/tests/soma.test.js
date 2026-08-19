// Testes Soma
const { soma } = require('../calculator');

describe('Testes Soma', () => {
  test('deve somar 2 + 3 resultando em 5', () => {
    expect(soma(2, 3)).toBe(5);
  });

  test('deve somar números negativos corretamente', () => {
    expect(soma(-4, -6)).toBe(-10);
  });

  test('deve somar números decimais corretamente', () => {
    expect(soma(1.2, 2.8)).toBe(4);
  });

  test('deve somar quando o elemento for 0 (elemento neutro)', () => {
    expect(soma(7, 0)).toBe(7);
  });

  test('deve converter textos numéricos e somar corretamente', () => {
    expect(soma('10', '5')).toBe(15);
  });
});
