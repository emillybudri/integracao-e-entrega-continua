const fs = require("fs");
const path = require("path");

const SECRET_PATTERNS = [
  { name: "Chave AWS (AKIA)", regex: /AKIA[0-9A-Z]{16}/g },
  { name: "Chave Privada (sk_live)", regex: /sk_live_[0-9a-zA-Z]{24,}/g },
  { name: "Chave de API em const", regex: /const\s+API_KEY(_\w+)?\s*=\s*["'][^"']{10,}["']/g },
  { name: "Token Privado", regex: /SECRET_KEY\s*=\s*["'][^"']{8,}["']/g }
];

const DIRECTORIES_TO_SCAN = [
  path.join(__dirname, "../src"),
  path.join(__dirname, "../tests")
];

function scanFile(filePath) {
  const content = fs.readFileSync(filePath, "utf-8");
  const foundSecrets = [];

  const lines = content.split("\n");
  lines.forEach((line, index) => {
    const trimmed = line.trim();
    if (
      trimmed.startsWith("//") ||
      trimmed.startsWith("*") ||
      trimmed.startsWith("/*") ||
      line.includes("SECRET_PATTERNS") ||
      line.includes("AKIA[0-9A-Z]")
    ) {
      return;
    }

    SECRET_PATTERNS.forEach(pattern => {
      pattern.regex.lastIndex = 0;
      if (pattern.regex.test(line)) {
        foundSecrets.push({
          line: index + 1,
          pattern: pattern.name,
          snippet: line.trim()
        });
      }
    });
  });

  return foundSecrets;
}

function runSecurityScan() {
  console.log("Executando analise estatica de seguranca...");
  let totalIssues = 0;

  DIRECTORIES_TO_SCAN.forEach(dir => {
    if (!fs.existsSync(dir)) return;

    const files = fs.readdirSync(dir);
    files.forEach(file => {
      if (file.endsWith(".js")) {
        const fullPath = path.join(dir, file);
        const issues = scanFile(fullPath);

        if (issues.length > 0) {
          console.error(`\n[ERRO DE SEGURANCA] Segredo exposto em: ${path.relative(process.cwd(), fullPath)}`);
          issues.forEach(issue => {
            console.error(`  - Linha ${issue.line}: ${issue.pattern}`);
            console.error(`    Conteudo: "${issue.snippet}"`);
            totalIssues++;
          });
        }
      }
    });
  });

  if (totalIssues > 0) {
    console.error(`\nFalha de seguranca: ${totalIssues} segredo(s) encontrado(s).`);
    process.exit(1);
  } else {
    console.log("Nenhum segredo encontrado.");
  }
}

runSecurityScan();
