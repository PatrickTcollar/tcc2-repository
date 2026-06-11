const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
  ShadingType, PageNumber, PageBreak, LevelFormat
} = require('docx');
const fs = require('fs');
const path = require('path');

const border = { style: BorderStyle.SINGLE, size: 1, color: 'CCCCCC' };
const borders = { top: border, bottom: border, left: border, right: border };
const headerBorder = { style: BorderStyle.SINGLE, size: 1, color: '1F4E79' };
const headerBorders = { top: headerBorder, bottom: headerBorder, left: headerBorder, right: headerBorder };

function cell(text, opts = {}) {
  return new TableCell({
    borders: opts.header ? headerBorders : borders,
    width: opts.width ? { size: opts.width, type: WidthType.DXA } : undefined,
    shading: opts.header
      ? { fill: '1F4E79', type: ShadingType.CLEAR }
      : opts.alt
        ? { fill: 'EBF3FB', type: ShadingType.CLEAR }
        : { fill: 'FFFFFF', type: ShadingType.CLEAR },
    margins: { top: 80, bottom: 80, left: 120, right: 120 },
    children: [new Paragraph({
      children: [new TextRun({
        text,
        font: 'Arial',
        size: opts.header ? 20 : 18,
        bold: opts.bold || opts.header || false,
        color: opts.header ? 'FFFFFF' : (opts.color || '000000'),
      })]
    })]
  });
}

function h1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    children: [new TextRun({ text, font: 'Arial', size: 28, bold: true, color: '1F4E79' })],
    spacing: { before: 400, after: 200 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: '1F4E79', space: 1 } }
  });
}

function h2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    children: [new TextRun({ text, font: 'Arial', size: 24, bold: true, color: '2E75B6' })],
    spacing: { before: 300, after: 120 }
  });
}

function h3(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_3,
    children: [new TextRun({ text, font: 'Arial', size: 22, bold: true, color: '333333' })],
    spacing: { before: 200, after: 80 }
  });
}

function p(text, opts = {}) {
  return new Paragraph({
    children: [new TextRun({ text, font: 'Arial', size: 20, bold: opts.bold || false, color: opts.color || '333333' })],
    spacing: { before: 60, after: 60 },
    alignment: opts.center ? AlignmentType.CENTER : AlignmentType.JUSTIFIED
  });
}

function bullet(text) {
  return new Paragraph({
    numbering: { reference: 'bullets', level: 0 },
    children: [new TextRun({ text, font: 'Arial', size: 20, color: '333333' })],
    spacing: { before: 40, after: 40 }
  });
}

function space() {
  return new Paragraph({ children: [new TextRun('')], spacing: { before: 80, after: 80 } });
}

function statusBadge(status) {
  const map = {
    'Passou': 'PASSOU',
    'Falhou': 'FALHOU',
    'N/A': 'N/A',
    'Pendente': 'PENDENTE'
  };
  return map[status] || status;
}

const doc = new Document({
  numbering: {
    config: [
      {
        reference: 'bullets',
        levels: [{
          level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } }
        }]
      }
    ]
  },
  styles: {
    default: { document: { run: { font: 'Arial', size: 20 } } },
    paragraphStyles: [
      {
        id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 28, bold: true, font: 'Arial', color: '1F4E79' },
        paragraph: { spacing: { before: 400, after: 200 }, outlineLevel: 0 }
      },
      {
        id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 24, bold: true, font: 'Arial', color: '2E75B6' },
        paragraph: { spacing: { before: 300, after: 120 }, outlineLevel: 1 }
      },
      {
        id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 22, bold: true, font: 'Arial', color: '333333' },
        paragraph: { spacing: { before: 200, after: 80 }, outlineLevel: 2 }
      }
    ]
  },
  sections: [
    // =========================================================
    // CAPA
    // =========================================================
    {
      properties: {
        page: {
          size: { width: 11906, height: 16838 },
          margin: { top: 1440, right: 1440, bottom: 1440, left: 1800 }
        }
      },
      headers: {
        default: new Header({
          children: [new Paragraph({
            children: [new TextRun({ text: 'PulmoEspir — Plano de Testes v1.0', font: 'Arial', size: 18, color: '666666' })],
            border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: '1F4E79', space: 1 } },
            spacing: { after: 100 }
          })]
        })
      },
      footers: {
        default: new Footer({
          children: [new Paragraph({
            children: [
              new TextRun({ text: 'Confidencial — Uso Interno', font: 'Arial', size: 16, color: '999999' }),
              new TextRun({ text: '\tPagina ', font: 'Arial', size: 16, color: '999999' }),
              new TextRun({ children: [PageNumber.CURRENT], font: 'Arial', size: 16, color: '999999' }),
              new TextRun({ text: ' de ', font: 'Arial', size: 16, color: '999999' }),
              new TextRun({ children: [PageNumber.TOTAL_PAGES], font: 'Arial', size: 16, color: '999999' }),
            ],
            tabStops: [{ type: 'right', position: 9026 }]
          })]
        })
      },
      children: [
        space(), space(), space(),
        new Paragraph({
          children: [new TextRun({ text: 'PulmoEspir', font: 'Arial', size: 64, bold: true, color: '1F4E79' })],
          alignment: AlignmentType.CENTER, spacing: { before: 600, after: 80 }
        }),
        new Paragraph({
          children: [new TextRun({ text: 'Sistema de Gerenciamento de Espirometria com IA', font: 'Arial', size: 28, color: '2E75B6' })],
          alignment: AlignmentType.CENTER, spacing: { before: 0, after: 400 }
        }),
        new Paragraph({
          children: [new TextRun({ text: '', font: 'Arial', size: 16 })],
          border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: '1F4E79', space: 1 } },
          spacing: { before: 0, after: 400 }
        }),
        new Paragraph({
          children: [new TextRun({ text: 'PLANO DE TESTES COMPLETO', font: 'Arial', size: 36, bold: true, color: '333333' })],
          alignment: AlignmentType.CENTER, spacing: { before: 0, after: 200 }
        }),
        new Paragraph({
          children: [new TextRun({ text: 'Versao 1.0 — Trabalho de Conclusao de Curso (TCC2)', font: 'Arial', size: 22, color: '666666' })],
          alignment: AlignmentType.CENTER, spacing: { before: 0, after: 600 }
        }),
        space(), space(),

        // Info table
        new Table({
          width: { size: 7200, type: WidthType.DXA },
          columnWidths: [2400, 4800],
          rows: [
            new TableRow({ children: [cell('Projeto', { header: true, width: 2400 }), cell('PulmoEspir — TCC2', { width: 4800, bold: true })] }),
            new TableRow({ children: [cell('Versao', { header: true, width: 2400 }), cell('1.0', { width: 4800 })] }),
            new TableRow({ children: [cell('Data', { header: true, width: 2400 }), cell('11 de Junho de 2026', { width: 4800 })] }),
            new TableRow({ children: [cell('Autor', { header: true, width: 2400 }), cell('Patrick Tarouco', { width: 4800 })] }),
            new TableRow({ children: [cell('Ambiente', { header: true, width: 2400 }), cell('Localhost (Laravel 11 + MySQL + API Gemini)', { width: 4800 })] }),
            new TableRow({ children: [cell('Total de Casos de Teste', { header: true, width: 2400 }), cell('7 casos — CT-01 a CT-07', { width: 4800, bold: true })] }),
          ]
        }),

        new Paragraph({ children: [new PageBreak()] }),

        // ===== 1. INTRODUCAO =====
        h1('1. Introducao e Objetivos'),
        p('Este documento descreve o plano de testes completo para o sistema PulmoEspir, desenvolvido como Trabalho de Conclusao de Curso (TCC2). O objetivo e validar todas as funcionalidades criticas do sistema, garantindo confiabilidade, seguranca e usabilidade na gestao de exames de espirometria com inteligencia artificial.'),
        space(),
        h2('1.1 Escopo dos Testes'),
        bullet('Autenticacao e autorizacao de usuarios'),
        bullet('Cadastro e gerenciamento de pacientes'),
        bullet('Upload e armazenamento de exames em PDF'),
        bullet('Geracao automatizada de laudos via API Gemini (IA)'),
        bullet('Interface de chat inteligente com contexto do exame'),
        bullet('Listagem, visualizacao e exclusao de registros'),
        bullet('Controle de acesso e isolamento de dados por usuario'),
        space(),
        h2('1.2 Ferramentas e Tecnologias'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [3000, 6026],
          rows: [
            new TableRow({ children: [cell('Componente', { header: true, width: 3000 }), cell('Tecnologia/Versao', { header: true, width: 6026 })] }),
            new TableRow({ children: [cell('Backend', { width: 3000 }), cell('Laravel 11, PHP 8.2', { width: 6026 })] }),
            new TableRow({ children: [cell('Frontend', { width: 3000 }), cell('Blade, TailwindCSS, Alpine.js', { width: 6026, alt: true })] }),
            new TableRow({ children: [cell('Banco de Dados', { width: 3000 }), cell('MySQL 8.0', { width: 6026 })] }),
            new TableRow({ children: [cell('IA / NLP', { width: 3000 }), cell('Google Gemini 2.5 Flash API', { width: 6026, alt: true })] }),
            new TableRow({ children: [cell('Extracao de PDF', { width: 3000 }), cell('smalot/pdfparser', { width: 6026 })] }),
            new TableRow({ children: [cell('Automacao de Testes', { width: 3000 }), cell('Playwright 1.60 (execucao manual assistida)', { width: 6026, alt: true })] }),
            new TableRow({ children: [cell('Servidor Local', { width: 3000 }), cell('php artisan serve (localhost:8000)', { width: 6026 })] }),
          ]
        }),

        new Paragraph({ children: [new PageBreak()] }),

        // ===== 2. DADOS DE TESTE =====
        h1('2. Dados de Teste'),
        h2('2.1 Pacientes Cadastrados'),
        p('Tres perfis de pacientes foram criados para cobrir diferentes cenarios clinicos:'),
        space(),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [1800, 2200, 900, 900, 1100, 2126],
          rows: [
            new TableRow({ children: [
              cell('ID', { header: true, width: 1800 }),
              cell('Nome', { header: true, width: 2200 }),
              cell('Sexo', { header: true, width: 900 }),
              cell('Idade', { header: true, width: 900 }),
              cell('Fumante', { header: true, width: 1100 }),
              cell('Perfil Clinico', { header: true, width: 2126 })
            ]}),
            new TableRow({ children: [
              cell('P-001', { width: 1800 }),
              cell('Carlos Mendes', { width: 2200 }),
              cell('M', { width: 900 }),
              cell('45', { width: 900 }),
              cell('Nao', { width: 1100 }),
              cell('Obstrutivo (evolucao positiva)', { width: 2126 })
            ]}),
            new TableRow({ children: [
              cell('P-002', { width: 1800, alt: true }),
              cell('Ana Paula Ferreira', { width: 2200, alt: true }),
              cell('F', { width: 900, alt: true }),
              cell('38', { width: 900, alt: true }),
              cell('Sim', { width: 1100, alt: true }),
              cell('Restritivo (tabagista)', { width: 2126, alt: true })
            ]}),
            new TableRow({ children: [
              cell('P-003', { width: 1800 }),
              cell('Roberto Alves', { width: 2200 }),
              cell('M', { width: 900 }),
              cell('62', { width: 900 }),
              cell('Sim', { width: 1100 }),
              cell('Misto (obstrutivo + restritivo)', { width: 2126 })
            ]}),
          ]
        }),
        space(),
        h2('2.2 Exames Sinteticos (PDFs)'),
        p('15 arquivos PDF foram gerados programaticamente com dados reais de espirometria. Cada PDF contem: ID do exame, dados do paciente, tabela de resultados (CVF, VEF1, VEF1/CVF, PFE) e interpretacao clinica textual.'),
        space(),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [1200, 2500, 1100, 1100, 900, 2226],
          rows: [
            new TableRow({ children: [
              cell('ID Exame', { header: true, width: 1200 }),
              cell('Paciente', { header: true, width: 2500 }),
              cell('CVF (%)', { header: true, width: 1100 }),
              cell('VEF1 (%)', { header: true, width: 1100 }),
              cell('VEF1/CVF', { header: true, width: 900 }),
              cell('Padrao', { header: true, width: 2226 })
            ]}),
            ...([
              ['EX-001','Carlos Mendes','82%','65%','63%','Obstrutivo Moderado'],
              ['EX-002','Carlos Mendes','84%','69%','65%','Obstrutivo Leve-Moderado'],
              ['EX-003','Carlos Mendes','87%','75%','69%','Obstrutivo Leve'],
              ['EX-004','Carlos Mendes','90%','84%','74%','Normal'],
              ['EX-005','Carlos Mendes','94%','89%','76%','Normal'],
              ['EX-006','Ana Paula Ferreira','63%','64%','85%','Restritivo Moderado'],
              ['EX-007','Ana Paula Ferreira','67%','67%','84%','Restritivo Moderado'],
              ['EX-008','Ana Paula Ferreira','60%','60%','83%','Restritivo Moderado-Grave'],
              ['EX-009','Ana Paula Ferreira','70%','71%','85%','Restritivo Leve-Moderado'],
              ['EX-010','Ana Paula Ferreira','80%','82%','86%','Proximo Normal'],
              ['EX-011','Roberto Alves','76%','58%','56%','Misto Moderado-Grave'],
              ['EX-012','Roberto Alves','74%','55%','55%','Misto Grave'],
              ['EX-013','Roberto Alves','78%','62%','58%','Misto Moderado'],
              ['EX-014','Roberto Alves','81%','67%','61%','Obstrutivo Moderado'],
              ['EX-015','Roberto Alves','85%','75%','65%','Obstrutivo Leve'],
            ]).map((r, i) => new TableRow({ children: r.map((v, j) => cell(v, { width: [1200,2500,1100,1100,900,2226][j], alt: i % 2 === 1 })) }))
          ]
        }),

        new Paragraph({ children: [new PageBreak()] }),

        // ===== 3. CASOS DE TESTE =====
        h1('3. Casos de Teste'),

        // CT-01
        h2('CT-01 — Autenticacao de Usuario'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-01', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Autenticacao de Usuario', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('Auth — Login e Registro', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Alta', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('Sistema acessivel em localhost:8000', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Acessar /register e criar conta com nome, e-mail e senha\n2. Verificar redirecionamento para /dashboard\n3. Fazer logout\n4. Acessar /login com credenciais corretas\n5. Acessar /login com credenciais incorretas', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('Registro cria conta e redireciona para dashboard. Login valido autentica. Login invalido exibe mensagem de erro sem acesso ao sistema.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),
        space(),

        // CT-02
        h2('CT-02 — Cadastro de Pacientes'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-02', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Cadastro de Pacientes', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('Pacientes — CRUD', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Alta', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('Usuario autenticado no sistema', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Acessar /pacientes/create\n2. Cadastrar Carlos Mendes (M, 45 anos, nao fumante)\n3. Cadastrar Ana Paula Ferreira (F, 38 anos, fumante)\n4. Cadastrar Roberto Alves (M, 62 anos, fumante)\n5. Verificar listagem em /pacientes\n6. Editar um paciente\n7. Tentar cadastrar paciente sem nome (validacao)', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('3 pacientes cadastrados e listados. Edicao atualiza dados. Formulario sem nome exibe erro de validacao.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),
        space(),

        // CT-03
        h2('CT-03 — Upload de Exames PDF'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-03', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Upload de Exames PDF', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('Exames — Upload e Armazenamento', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Alta', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('3 pacientes cadastrados. PDFs EX-001 a EX-015 disponiveis', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Para Carlos Mendes: fazer upload de EX-001 a EX-005\n2. Para Ana Paula Ferreira: fazer upload de EX-006 a EX-010\n3. Para Roberto Alves: fazer upload de EX-011 a EX-015\n4. Verificar listagem em /exames\n5. Tentar upload de arquivo .txt (validacao de tipo)\n6. Verificar que exames ficam associados ao paciente correto', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('15 exames armazenados, visiveis na listagem, associados corretamente. Upload de .txt rejeitado. Exames visiveis na pagina do paciente.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),
        space(),

        new Paragraph({ children: [new PageBreak()] }),

        // CT-04
        h2('CT-04 — Geracao Automatica de Laudos com IA'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-04', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Geracao Automatica de Laudos com IA', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('Laudos — API Gemini', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Alta', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('Exames EX-001, EX-006 e EX-011 carregados. Chave API Gemini configurada no .env', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Na pagina do paciente Carlos Mendes, clicar em "Gerar Laudo" para EX-001\n2. Aguardar resposta da API Gemini (timeout 60s)\n3. Verificar exibicao do laudo com secoes: Achados, Interpretacao Clinica e Recomendacoes\n4. Repetir para EX-006 (Ana Paula) e EX-011 (Roberto)\n5. Verificar persistencia do laudo no banco\n6. Tentar gerar segundo laudo para o mesmo exame', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('Laudo gerado em linguagem clinica, com 3 secoes obrigatorias. Salvo no banco. Sistema impede geracao de laudo duplicado ou exibe aviso adequado.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),
        space(),

        // CT-05
        h2('CT-05 — Chat IA com Contexto do Exame'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-05', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Chat IA com Contexto do Exame', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('Chat — API Gemini', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Alta', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('Exame EX-010 carregado (Ana Paula Ferreira). API Gemini operacional', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Acessar a pagina de chat do exame EX-010\n2. Enviar mensagem: "Qual o diagnostico deste exame?"\n3. Verificar resposta contextualizada da IA\n4. Enviar mensagem: "Quais exercicios fisioterapeuticos sao indicados?"\n5. Verificar coerencia da resposta com o diagnostico\n6. Enviar mensagem: "O paciente e tabagista, como isso afeta os resultados?"', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('IA responde com base nos dados do exame EX-010. Respostas coerentes com padrao restritivo em melhora. Mencionada a condicao de tabagismo.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),
        space(),

        new Paragraph({ children: [new PageBreak()] }),

        // CT-06
        h2('CT-06 — Controle de Acesso e Isolamento de Dados'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-06', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Controle de Acesso e Isolamento de Dados', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('Seguranca — Autorizacao', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Alta', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('Usuario A com pacientes cadastrados. Criar usuario B separado', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Registrar usuario B com e-mail diferente\n2. Verificar que usuario B nao ve pacientes do usuario A\n3. Tentar acessar /pacientes/{id} de um paciente do usuario A estando logado como B\n4. Tentar excluir exame de outro usuario via URL direta\n5. Acessar /dashboard sem autenticacao (esperado: redirect para /login)', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('Usuario B ve lista vazia. Acesso a registro de outro usuario retorna HTTP 403. Areas protegidas redirecionam nao-autenticados para login.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),
        space(),

        // CT-07
        h2('CT-07 — Exclusao e Integridade Referencial'),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2200, 6826],
          rows: [
            new TableRow({ children: [cell('ID', { header: true, width: 2200 }), cell('CT-07', { header: true, width: 6826 })] }),
            new TableRow({ children: [cell('Nome', { width: 2200 }), cell('Exclusao e Integridade Referencial', { width: 6826, bold: true })] }),
            new TableRow({ children: [cell('Modulo', { width: 2200, alt: true }), cell('CRUD — Exclusoes Encadeadas', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Prioridade', { width: 2200 }), cell('Media', { width: 6826 })] }),
            new TableRow({ children: [cell('Pre-condicoes', { width: 2200, alt: true }), cell('Pelo menos um exame com laudo gerado', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Passos de Teste', { width: 2200 }), cell('1. Excluir laudo de um exame\n2. Verificar que o exame permanece\n3. Excluir exame que possui laudo\n4. Verificar que laudo associado tambem e removido (cascade)\n5. Excluir paciente\n6. Verificar que exames e laudos do paciente sao removidos', { width: 6826 })] }),
            new TableRow({ children: [cell('Resultado Esperado', { width: 2200, alt: true }), cell('Exclusoes em cascata funcionam corretamente. Nenhum registro orfao no banco. Arquivo PDF removido do storage apos exclusao do exame.', { width: 6826, alt: true })] }),
            new TableRow({ children: [cell('Resultado Obtido', { width: 2200 }), cell('', { width: 6826 })] }),
            new TableRow({ children: [cell('Status', { width: 2200, alt: true }), cell('[ ] Passou   [ ] Falhou   [ ] Bloqueado', { width: 6826, alt: true })] }),
          ]
        }),

        new Paragraph({ children: [new PageBreak()] }),

        // ===== 4. SUMARIO DE RESULTADOS =====
        h1('4. Sumario dos Resultados'),
        p('Tabela de registro dos resultados apos execucao dos testes:'),
        space(),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [1100, 3500, 1200, 1200, 2026],
          rows: [
            new TableRow({ children: [
              cell('ID', { header: true, width: 1100 }),
              cell('Nome do Caso', { header: true, width: 3500 }),
              cell('Prioridade', { header: true, width: 1200 }),
              cell('Status', { header: true, width: 1200 }),
              cell('Observacoes', { header: true, width: 2026 })
            ]}),
            ...([
              ['CT-01','Autenticacao de Usuario','Alta',''],
              ['CT-02','Cadastro de Pacientes','Alta',''],
              ['CT-03','Upload de Exames PDF','Alta',''],
              ['CT-04','Geracao de Laudos com IA','Alta',''],
              ['CT-05','Chat IA com Contexto','Alta',''],
              ['CT-06','Controle de Acesso','Alta',''],
              ['CT-07','Exclusao e Integridade','Media',''],
            ]).map((r, i) => new TableRow({ children: [
              cell(r[0], { width: 1100, alt: i % 2 === 1 }),
              cell(r[1], { width: 3500, alt: i % 2 === 1 }),
              cell(r[2], { width: 1200, alt: i % 2 === 1 }),
              cell(r[3], { width: 1200, alt: i % 2 === 1 }),
              cell(r[4], { width: 2026, alt: i % 2 === 1 }),
            ]}))
          ]
        }),
        space(),
        new Table({
          width: { size: 9026, type: WidthType.DXA },
          columnWidths: [2256, 2256, 2257, 2257],
          rows: [
            new TableRow({ children: [
              cell('Total de Testes', { header: true, width: 2256 }),
              cell('Aprovados', { header: true, width: 2256 }),
              cell('Reprovados', { header: true, width: 2257 }),
              cell('Bloqueados', { header: true, width: 2257 })
            ]}),
            new TableRow({ children: [
              cell('7', { width: 2256, bold: true }),
              cell('', { width: 2256 }),
              cell('', { width: 2257 }),
              cell('', { width: 2257 }),
            ]})
          ]
        }),

        space(), space(),

        // ===== 5. CRITERIOS DE ACEITE =====
        h1('5. Criterios de Aceite'),
        p('O sistema sera considerado aprovado para entrega do TCC2 se atender aos seguintes criterios:'),
        space(),
        bullet('Todos os 7 casos de teste de prioridade Alta obtiverem status "Passou"'),
        bullet('Nenhuma vulnerabilidade critica de segurança identificada nos testes de controle de acesso (CT-06)'),
        bullet('Geracao de laudo via IA (CT-04) bem-sucedida em pelo menos 90% das tentativas'),
        bullet('Chat IA (CT-05) responde com conteudo coerente ao exame em 100% das tentativas validas'),
        bullet('Nenhum dado orfao no banco apos operacoes de exclusao (CT-07)'),
        space(),
        h1('6. Evidencias e Anexos'),
        p('As capturas de tela de cada execucao devem ser anexadas nesta secao ou registradas no sistema de versionamento. Cada evidencia deve ser nomeada conforme o padrao:'),
        space(),
        p('Padrao: [ID_CASO]-[SUBETAPA]-[RESULTADO].png', { bold: true }),
        p('Exemplo: CT-04-laudo-gerado-PASSOU.png'),
        space(),
        bullet('Screenshots de cada operacao de upload (CT-03)'),
        bullet('Texto completo dos laudos gerados pela IA (CT-04)'),
        bullet('Log completo do chat IA com EX-010 (CT-05)'),
        bullet('Capturas HTTP 403 para tentativas nao autorizadas (CT-06)'),
        space(),
        new Paragraph({
          children: [new TextRun({ text: 'Fim do Documento', font: 'Arial', size: 18, color: '999999', italics: true })],
          alignment: AlignmentType.CENTER,
          spacing: { before: 600, after: 200 }
        }),
      ]
    }
  ]
});

const outPath = path.join(__dirname, '..', 'Plano_de_Testes_PulmoEspir.docx');

Packer.toBuffer(doc).then(buffer => {
  fs.writeFileSync(outPath, buffer);
  console.log('Documento criado: ' + outPath);
}).catch(err => {
  console.error('Erro:', err.message);
});
