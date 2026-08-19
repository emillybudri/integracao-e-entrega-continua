// Todas as funções de cálculo da calculadora

// Soma
function soma(a, b) {
  return Number(a) + Number(b);
}

// Subtração
function subtracao(a, b) {
  return Number(a) - Number(b);
}

// Multiplicação
function multiplicacao(a, b) {
  return Number(a) * Number(b);
}

// Divisão
function divisao(a, b) {
  const numB = Number(b);

  // Se b for zero, lança erro
  if (numB === 0) {
    throw new Error("Divisão por zero não é permitida");
  }

  return Number(a) / numB;
}

// Exporta todas as funções
module.exports = {
  soma,
  subtracao,
  multiplicacao,
  divisao
};
