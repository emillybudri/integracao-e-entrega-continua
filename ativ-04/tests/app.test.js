const {
  soma,
  subtrai,
  multiplica,
  divide,
  validaEmail,
  formataMoeda
} = require("../src/app");

describe("Testes Unitarios", () => {
  describe("Operacoes Matematicas", () => {
    test("Deve somar dois numeros corretamente", () => {
      expect(soma(2, 3)).toBe(5);
    });

    test("Deve subtrair dois numeros corretamente", () => {
      expect(subtrai(10, 4)).toBe(6);
    });

    test("Deve multiplicar dois numeros corretamente", () => {
      expect(multiplica(3, 4)).toBe(12);
    });

    test("Deve dividir dois numeros corretamente", () => {
      expect(divide(10, 2)).toBe(5);
    });

    test("Deve lancar erro ao dividir por zero", () => {
      expect(() => divide(10, 0)).toThrow("Divisão por zero não é permitida.");
    });
  });

  describe("Validacoes", () => {
    test("Deve validar emails", () => {
      expect(validaEmail("usuario@teste.com")).toBe(true);
      expect(validaEmail("email-invalido")).toBe(false);
      expect(validaEmail("")).toBe(false);
    });

    test("Deve formatar moeda", () => {
      expect(formataMoeda(100.5)).toBe("R$ 100,50");
      expect(() => formataMoeda("100")).toThrow("O valor deve ser um número.");
    });
  });
});
