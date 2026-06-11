// Script para gerar os 15 PDFs de exame de espirometria sintéticos
const PDFDocument = require('pdfkit');
const fs = require('fs');
const path = require('path');

const outputDir = path.join(__dirname, '..', 'test-pdfs');
if (!fs.existsSync(outputDir)) fs.mkdirSync(outputDir, { recursive: true });

const exams = [
  // Paciente 1: Carlos Mendes (M, 45 anos, não fumante) — padrão obstrutivo
  {
    id: 'EX-001', patient: 'Carlos Mendes', age: 45, gender: 'M', smoker: 'Não',
    height: 175, weight: 82,
    fvc: 3.82, fvc_pred: 4.65, fvc_pct: 82,
    fev1: 2.41, fev1_pred: 3.71, fev1_pct: 65,
    fev1_fvc: 63, fev1_fvc_pred: 80,
    pef: 6.20, pef_pred: 8.10, pef_pct: 77,
    interpretation: 'Padrao obstrutivo moderado. Relacao VEF1/CVF abaixo do limite inferior de normalidade.'
  },
  {
    id: 'EX-002', patient: 'Carlos Mendes', age: 45, gender: 'M', smoker: 'Não',
    height: 175, weight: 82,
    fvc: 3.91, fvc_pred: 4.65, fvc_pct: 84,
    fev1: 2.55, fev1_pred: 3.71, fev1_pct: 69,
    fev1_fvc: 65, fev1_fvc_pred: 80,
    pef: 6.40, pef_pred: 8.10, pef_pct: 79,
    interpretation: 'Padrao obstrutivo leve a moderado. Melhora discreta em relacao ao exame anterior.'
  },
  {
    id: 'EX-003', patient: 'Carlos Mendes', age: 45, gender: 'M', smoker: 'Não',
    height: 175, weight: 82,
    fvc: 4.05, fvc_pred: 4.65, fvc_pct: 87,
    fev1: 2.78, fev1_pred: 3.71, fev1_pct: 75,
    fev1_fvc: 69, fev1_fvc_pred: 80,
    pef: 6.90, pef_pred: 8.10, pef_pct: 85,
    interpretation: 'Padrao obstrutivo leve. Melhora significativa. Considerar revisao de tratamento.'
  },
  {
    id: 'EX-004', patient: 'Carlos Mendes', age: 45, gender: 'M', smoker: 'Não',
    height: 175, weight: 82,
    fvc: 4.20, fvc_pred: 4.65, fvc_pct: 90,
    fev1: 3.10, fev1_pred: 3.71, fev1_pct: 84,
    fev1_fvc: 74, fev1_fvc_pred: 80,
    pef: 7.50, pef_pred: 8.10, pef_pct: 93,
    interpretation: 'Funcao pulmonar dentro dos limites normais. Remissao do quadro obstrutivo.'
  },
  {
    id: 'EX-005', patient: 'Carlos Mendes', age: 45, gender: 'M', smoker: 'Não',
    height: 175, weight: 82,
    fvc: 4.35, fvc_pred: 4.65, fvc_pct: 94,
    fev1: 3.32, fev1_pred: 3.71, fev1_pct: 89,
    fev1_fvc: 76, fev1_fvc_pred: 80,
    pef: 7.80, pef_pred: 8.10, pef_pct: 96,
    interpretation: 'Funcao pulmonar normal. Paciente com boa resposta terapeutica.'
  },
  // Paciente 2: Ana Paula Ferreira (F, 38 anos, fumante) — padrão restritivo
  {
    id: 'EX-006', patient: 'Ana Paula Ferreira', age: 38, gender: 'F', smoker: 'Sim',
    height: 162, weight: 68,
    fvc: 2.20, fvc_pred: 3.50, fvc_pct: 63,
    fev1: 1.87, fev1_pred: 2.94, fev1_pct: 64,
    fev1_fvc: 85, fev1_fvc_pred: 80,
    pef: 4.80, pef_pred: 6.20, pef_pct: 77,
    interpretation: 'Padrao restritivo moderado. CVF reduzida com relacao VEF1/CVF preservada.'
  },
  {
    id: 'EX-007', patient: 'Ana Paula Ferreira', age: 38, gender: 'F', smoker: 'Sim',
    height: 162, weight: 68,
    fvc: 2.35, fvc_pred: 3.50, fvc_pct: 67,
    fev1: 1.98, fev1_pred: 2.94, fev1_pct: 67,
    fev1_fvc: 84, fev1_fvc_pred: 80,
    pef: 5.10, pef_pred: 6.20, pef_pct: 82,
    interpretation: 'Padrao restritivo moderado. Pequena melhora na CVF. Manutencao do padrao restritivo.'
  },
  {
    id: 'EX-008', patient: 'Ana Paula Ferreira', age: 38, gender: 'F', smoker: 'Sim',
    height: 162, weight: 68,
    fvc: 2.10, fvc_pred: 3.50, fvc_pct: 60,
    fev1: 1.75, fev1_pred: 2.94, fev1_pct: 60,
    fev1_fvc: 83, fev1_fvc_pred: 80,
    pef: 4.50, pef_pred: 6.20, pef_pct: 73,
    interpretation: 'Piora do padrao restritivo. CVF abaixo de 65% do previsto. Investigar causas.'
  },
  {
    id: 'EX-009', patient: 'Ana Paula Ferreira', age: 38, gender: 'F', smoker: 'Sim',
    height: 162, weight: 68,
    fvc: 2.45, fvc_pred: 3.50, fvc_pct: 70,
    fev1: 2.08, fev1_pred: 2.94, fev1_pct: 71,
    fev1_fvc: 85, fev1_fvc_pred: 80,
    pef: 5.30, pef_pred: 6.20, pef_pct: 86,
    interpretation: 'Padrao restritivo leve a moderado. Melhora apos intervencao fisioterapica.'
  },
  {
    id: 'EX-010', patient: 'Ana Paula Ferreira', age: 38, gender: 'F', smoker: 'Sim',
    height: 162, weight: 68,
    fvc: 2.80, fvc_pred: 3.50, fvc_pct: 80,
    fev1: 2.41, fev1_pred: 2.94, fev1_pct: 82,
    fev1_fvc: 86, fev1_fvc_pred: 80,
    pef: 5.80, pef_pred: 6.20, pef_pct: 94,
    interpretation: 'Funcao pulmonar proxima da normalidade. CVF 80% do previsto. Manutencao da fisioterapia recomendada.'
  },
  // Paciente 3: Roberto Alves (M, 62 anos, fumante) — padrão misto
  {
    id: 'EX-011', patient: 'Roberto Alves', age: 62, gender: 'M', smoker: 'Sim',
    height: 170, weight: 75,
    fvc: 2.95, fvc_pred: 3.90, fvc_pct: 76,
    fev1: 1.65, fev1_pred: 2.85, fev1_pct: 58,
    fev1_fvc: 56, fev1_fvc_pred: 76,
    pef: 4.20, pef_pred: 6.80, pef_pct: 62,
    interpretation: 'Padrao misto (obstrutivo e restritivo) moderado a grave. VEF1 58% do previsto.'
  },
  {
    id: 'EX-012', patient: 'Roberto Alves', age: 62, gender: 'M', smoker: 'Sim',
    height: 170, weight: 75,
    fvc: 2.88, fvc_pred: 3.90, fvc_pct: 74,
    fev1: 1.58, fev1_pred: 2.85, fev1_pct: 55,
    fev1_fvc: 55, fev1_fvc_pred: 76,
    pef: 4.10, pef_pred: 6.80, pef_pct: 60,
    interpretation: 'Padrao misto grave. Piora progressiva. Adesao ao tratamento deve ser verificada.'
  },
  {
    id: 'EX-013', patient: 'Roberto Alves', age: 62, gender: 'M', smoker: 'Sim',
    height: 170, weight: 75,
    fvc: 3.05, fvc_pred: 3.90, fvc_pct: 78,
    fev1: 1.78, fev1_pred: 2.85, fev1_pct: 62,
    fev1_fvc: 58, fev1_fvc_pred: 76,
    pef: 4.50, pef_pred: 6.80, pef_pct: 66,
    interpretation: 'Padrao misto moderado. Melhora pos-broncodilatacao. Resposta broncodilatadora positiva.'
  },
  {
    id: 'EX-014', patient: 'Roberto Alves', age: 62, gender: 'M', smoker: 'Sim',
    height: 170, weight: 75,
    fvc: 3.15, fvc_pred: 3.90, fvc_pct: 81,
    fev1: 1.92, fev1_pred: 2.85, fev1_pct: 67,
    fev1_fvc: 61, fev1_fvc_pred: 76,
    pef: 4.80, pef_pred: 6.80, pef_pct: 71,
    interpretation: 'Padrao obstrutivo moderado. Componente restritivo em remissao. Continuar tratamento.'
  },
  {
    id: 'EX-015', patient: 'Roberto Alves', age: 62, gender: 'M', smoker: 'Sim',
    height: 170, weight: 75,
    fvc: 3.30, fvc_pred: 3.90, fvc_pct: 85,
    fev1: 2.15, fev1_pred: 2.85, fev1_pct: 75,
    fev1_fvc: 65, fev1_fvc_pred: 76,
    pef: 5.20, pef_pred: 6.80, pef_pct: 76,
    interpretation: 'Melhora significativa. Padrao obstrutivo leve. Acompanhamento trimestral recomendado.'
  }
];

function createExamPDF(exam) {
  return new Promise((resolve, reject) => {
    const filePath = path.join(outputDir, `${exam.id}.pdf`);
    const doc = new PDFDocument({ size: 'A4', margin: 50 });
    const stream = fs.createWriteStream(filePath);
    doc.pipe(stream);

    // Header
    doc.fontSize(16).font('Helvetica-Bold').text('LABORATORIO DE FUNCAO PULMONAR', { align: 'center' });
    doc.fontSize(12).font('Helvetica').text('Exame de Espirometria', { align: 'center' });
    doc.moveDown();
    doc.moveTo(50, doc.y).lineTo(545, doc.y).stroke();
    doc.moveDown(0.5);

    // Patient info
    doc.fontSize(11).font('Helvetica-Bold').text('DADOS DO PACIENTE');
    doc.font('Helvetica');
    doc.text(`ID do Exame: ${exam.id}`);
    doc.text(`Paciente: ${exam.patient}`);
    doc.text(`Idade: ${exam.age} anos    Sexo: ${exam.gender === 'M' ? 'Masculino' : 'Feminino'}    Tabagista: ${exam.smoker}`);
    doc.text(`Altura: ${exam.height} cm    Peso: ${exam.weight} kg`);
    doc.text(`Data do Exame: ${new Date().toLocaleDateString('pt-BR')}`);
    doc.moveDown();

    // Results table header
    doc.font('Helvetica-Bold').text('RESULTADOS ESPIROMETRICOS');
    doc.moveDown(0.5);

    const tableTop = doc.y;
    const colX = [50, 200, 280, 360, 440];
    const rowH = 22;

    // Table header
    doc.font('Helvetica-Bold').fontSize(10);
    doc.text('Parametro', colX[0], tableTop);
    doc.text('Obtido', colX[1], tableTop);
    doc.text('Previsto', colX[2], tableTop);
    doc.text('% Previsto', colX[3], tableTop);
    doc.text('LLN', colX[4], tableTop);
    doc.moveTo(50, tableTop + 15).lineTo(545, tableTop + 15).stroke();

    // Table rows
    doc.font('Helvetica').fontSize(10);
    const rows = [
      ['CVF (L)', exam.fvc.toFixed(2), exam.fvc_pred.toFixed(2), `${exam.fvc_pct}%`, (exam.fvc_pred * 0.8).toFixed(2)],
      ['VEF1 (L)', exam.fev1.toFixed(2), exam.fev1_pred.toFixed(2), `${exam.fev1_pct}%`, (exam.fev1_pred * 0.8).toFixed(2)],
      ['VEF1/CVF (%)', `${exam.fev1_fvc}%`, `${exam.fev1_fvc_pred}%`, '-', '70%'],
      ['PFE (L/s)', exam.pef.toFixed(2), exam.pef_pred.toFixed(2), `${exam.pef_pct}%`, (exam.pef_pred * 0.8).toFixed(2)],
    ];

    rows.forEach((row, i) => {
      const y = tableTop + rowH + i * rowH;
      row.forEach((cell, j) => {
        doc.text(cell, colX[j], y);
      });
    });

    doc.moveDown(rows.length + 2);

    // Interpretation
    doc.moveTo(50, doc.y).lineTo(545, doc.y).stroke();
    doc.moveDown(0.5);
    doc.font('Helvetica-Bold').fontSize(11).text('INTERPRETACAO CLINICA');
    doc.font('Helvetica').fontSize(10);
    doc.moveDown(0.3);
    doc.text(`CVF: ${exam.fvc_pct}% do previsto (${exam.fvc_pct >= 80 ? 'Normal' : exam.fvc_pct >= 60 ? 'Reducao moderada' : 'Reducao grave'})`);
    doc.text(`VEF1: ${exam.fev1_pct}% do previsto (${exam.fev1_pct >= 80 ? 'Normal' : exam.fev1_pct >= 60 ? 'Reducao moderada' : 'Reducao grave'})`);
    doc.text(`VEF1/CVF: ${exam.fev1_fvc}% (${exam.fev1_fvc >= 70 ? 'Normal' : 'Reducao - Padrao Obstrutivo'})`);
    doc.moveDown();
    doc.font('Helvetica-Bold').text('Conclusao: ');
    doc.font('Helvetica').text(exam.interpretation, { continued: false });
    doc.moveDown();
    doc.text('Responsavel Tecnico: Dr. Fisioterapeuta Responsavel    CRF: XXXXX');
    doc.text(`Data do Laudo: ${new Date().toLocaleDateString('pt-BR')}`);

    doc.end();
    stream.on('finish', () => { console.log(`Created: ${exam.id}.pdf`); resolve(filePath); });
    stream.on('error', reject);
  });
}

async function main() {
  for (const exam of exams) {
    await createExamPDF(exam);
  }
  console.log(`\nAll ${exams.length} PDFs created in: ${outputDir}`);
}

main().catch(console.error);
