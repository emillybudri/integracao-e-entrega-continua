// Simulação do desafio: Chave de API exposta (descomentar para falhar analise de seguranca)
// const API_KEY_EXPOSTA = "sk_live_1234567890abcdef1234567890";

// Simulação do desafio: Erro de lint (descomentar para falhar linter)
// const variavelSemUso = "erro_de_lint"

const variavelSemUso = "erro_de_lint";

function soma(a, b) {
  return a + b;
}

function subtrai(a, b) {
  return a - b;
}

function multiplica(a, b) {
  return a * b;
}

function divide(a, b) {
  if (b === 0) {
    throw new Error("Divisão por zero não é permitida.");
  }
  return a / b;
}

function validaEmail(email) {
  if (!email || typeof email !== "string") {
    return false;
  }
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

function formataMoeda(valor) {
  if (typeof valor !== "number") {
    throw new Error("O valor deve ser um número.");
  }
  return `R$ ${valor.toFixed(2).replace(".", ",")}`;
}

// Simulação do desafio: Cobertura abaixo do limite (descomentar sem criar teste)
// function funcaoSemTesteNaoCoberta(parametro) {
//   if (parametro === "A") return 1;
//   if (parametro === "B") return 2;
//   if (parametro === "C") return 3;
//   return 0;
// }

module.exports = {
  soma,
  subtrai,
  multiplica,
  divide,
  validaEmail,
  formataMoeda
};
