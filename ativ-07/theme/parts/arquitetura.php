<figure class="figure">
<svg class="diagram" viewBox="0 0 520 400" role="img" aria-labelledby="diagram-title diagram-desc" xmlns="http://www.w3.org/2000/svg">
    <title id="diagram-title">Arquitetura do ambiente</title>
    <desc id="diagram-desc">O navegador acessa o container WordPress pela porta 8081. O WordPress consulta o container MariaDB pela porta 3306, dentro da rede wordpress_net. Cada container usa um volume persistente.</desc>

    <defs>
        <marker id="seta" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
            <path d="M1 1.5 8.5 5 1 8.5z" fill="#000"/>
        </marker>
    </defs>

    <!-- Rede -->
    <rect x="156" y="20" width="348" height="360" rx="18" fill="none" stroke="#6B6B6B" stroke-width="1.25" stroke-dasharray="5 6"/>
    <text x="176" y="46" class="d-label">rede wordpress_net</text>

    <!-- Navegador -->
    <rect x="12" y="84" width="104" height="92" rx="12" fill="#fff" stroke="#D5D5D5" stroke-width="1.5"/>
    <path d="M12 96a12 12 0 0 1 12-12h80a12 12 0 0 1 12 12v10H12z" fill="#F4F4F4"/>
    <circle cx="26" cy="95" r="3" fill="#D5D5D5"/>
    <circle cx="37" cy="95" r="3" fill="#D5D5D5"/>
    <circle cx="48" cy="95" r="3" fill="#D5D5D5"/>
    <text x="64" y="138" class="d-title" text-anchor="middle">Navegador</text>
    <text x="64" y="156" class="d-small" text-anchor="middle">localhost:8081</text>

    <!-- Seta HTTP -->
    <line x1="118" y1="130" x2="176" y2="130" stroke="#000" stroke-width="1.5" marker-end="url(#seta)"/>
    <text x="147" y="120" class="d-small" text-anchor="middle">HTTP</text>

    <!-- WordPress -->
    <rect x="178" y="70" width="304" height="120" rx="14" fill="#fff" stroke="#D5D5D5" stroke-width="1.5"/>
    <path d="M178 84a14 14 0 0 1 14-14h276a14 14 0 0 1 14 14v34H178z" fill="#FDF2F8"/>
    <circle cx="202" cy="94" r="5" fill="#DB2777"/>
    <text x="216" y="99" class="d-title">WordPress</text>
    <text x="466" y="99" class="d-small" text-anchor="end">porta 8081 → 80</text>
    <text x="198" y="140" class="d-small">Serviço web · Apache e PHP</text>
    <rect x="198" y="154" width="150" height="24" rx="12" fill="#F4F4F4"/>
    <text x="273" y="170" class="d-chip" text-anchor="middle">volume wordpress_data</text>

    <!-- Seta SQL -->
    <line x1="330" y1="192" x2="330" y2="248" stroke="#000" stroke-width="1.5" marker-end="url(#seta)"/>
    <rect x="344" y="205" width="116" height="26" rx="13" fill="#fff" stroke="#D5D5D5"/>
    <text x="402" y="222" class="d-chip" text-anchor="middle">consulta SQL :3306</text>

    <!-- MariaDB -->
    <rect x="178" y="250" width="304" height="120" rx="14" fill="#fff" stroke="#D5D5D5" stroke-width="1.5"/>
    <path d="M178 264a14 14 0 0 1 14-14h276a14 14 0 0 1 14 14v34H178z" fill="#F4F4F4"/>
    <circle cx="202" cy="274" r="5" fill="#DB2777"/>
    <text x="216" y="279" class="d-title">MariaDB 10.11</text>
    <text x="466" y="279" class="d-small" text-anchor="end">porta 3306 (interna)</text>
    <text x="198" y="320" class="d-small">Serviço database · healthcheck ativo</text>
    <rect x="198" y="334" width="118" height="24" rx="12" fill="#FDF2F8"/>
    <text x="257" y="350" class="d-chip" text-anchor="middle">volume db_data</text>
</svg>
</figure>
