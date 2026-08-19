// Testes Divisão
const { divisao } = require('../calculator');

describe('Testes Divisão', () => {
  test('deve dividir 20 por 4 resultando em 5', () => {
    expect(divisao(20, 4)).toBe(5);
  });

  test('deve dividir resultando em número decimal', () => {
    expect(divisao(7, 2)).toBe(3.5);
  });

  test('deve lançar erro ao tentar dividir por zero', () => {
    expect(() => divisao(10, 0)).toThrow('Divisão por zero não é permitida');
  });

  test('deve retornar 0 quando o dividendo for 0 (0 / N)', () => {
    expect(divisao(0, 5)).toBe(0);
  });

  test('deve dividir dois números negativos resultando em número positivo', () => {
    expect(divisao(-20, -4)).toBe(5);
  });
});
