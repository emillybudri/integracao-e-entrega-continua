// Calculadora

const readline = require('readline');
const { soma, subtracao, multiplicacao, divisao } = require('./calculator');

// Ler a entrada e exibir a saída
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

// Menu
function exibirMenu() {
  console.log('CALCULADORA');
  console.log('1. Soma (+)');
  console.log('2. Subtração (-)');
  console.log('3. Multiplicação (*)');
  console.log('4. Divisão (/)');
  console.log('5. Sair');

  rl.question('Escolha uma opção (1-5): ', (opcao) => {

    // Opção 5: encerra a aplicação
    if (opcao.trim() === '5') {
      console.log('\nSaindo da Calculadora. Até logo!\n');
      rl.close();
      return;
    }

    // Se for uma opção diferente de 1, 2, 3 ou 4, exibe erro e reexibe o menu
    if (!['1', '2', '3', '4'].includes(opcao.trim())) {
      console.log('\nOpção inválida! Tente novamente.\n');
      return exibirMenu();
    }

    // Se a opção for válida (1 a 4), chama a função para pedir os números
    solicitarNumeros(opcao.trim());
  });
}

// Pede os números e executa o cálculo
function solicitarNumeros(opcao) {

  rl.question('\nDigite o primeiro número: ', (input1) => {

    rl.question('Digite o segundo número: ', (input2) => {
      const num1 = Number(input1);
      const num2 = Number(input2);

      // Valida se são válidos
      if (isNaN(num1) || isNaN(num2)) {
        console.log('\nErro: Por favor, insira apenas números válidos.\n');
        return exibirMenu();
      }

      let resultado;
      let operacaoTexto = '';

      try {
        // Executa a função correspondente
        switch (opcao) {
          case '1':
            resultado = soma(num1, num2);
            operacaoTexto = `${num1} + ${num2}`;
            break;

          case '2':
            resultado = subtracao(num1, num2);
            operacaoTexto = `${num1} - ${num2}`;
            break;

          case '3':
            resultado = multiplicacao(num1, num2);
            operacaoTexto = `${num1} * ${num2}`;
            break;

          case '4':
            resultado = divisao(num1, num2);
            operacaoTexto = `${num1} / ${num2}`;
            break;
        }

        // Exibe o resultado
        console.log(`\n>>> RESULTADO: ${operacaoTexto} = ${resultado}\n`);
      } catch (error) {
        // Exibe mensagem caso ocorra algum erro
        console.log(`\n>>> ERRO: ${error.message}\n`);
      }

      // Volta ao menu
      exibirMenu();
    });
  });
}

// Inicia a calculadora
exibirMenu();
