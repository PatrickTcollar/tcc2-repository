<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Laudos — Fisioterapia Respiratória</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --green-dark:  #1a6b3c;
            --green-mid:   #2d9b5a;
            --green-light: #4cbb7f;
            --green-pale:  #e8f7ef;
            --green-bg:    #f0faf5;
            --gray-text:   #4b5563;
        }
        body { font-family: 'Inter', sans-serif; background: #fff; color: #1f2937; min-height: 100vh; }

        nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: rgba(255,255,255,0.96); backdrop-filter: blur(8px); border-bottom: 1px solid #e5e7eb; padding: 0 2rem; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.05rem; color: var(--green-dark); text-decoration: none; }
        .nav-logo .icon { width: 36px; height: 36px; background: var(--green-mid); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; flex-shrink: 0; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        .btn-outline { padding: 8px 18px; border-radius: 8px; font-size: .875rem; font-weight: 500; border: 1.5px solid var(--green-mid); color: var(--green-dark); text-decoration: none; transition: all .2s; }
        .btn-outline:hover { background: var(--green-pale); }
        .btn-solid { padding: 8px 18px; border-radius: 8px; font-size: .875rem; font-weight: 600; background: var(--green-mid); color: white; text-decoration: none; transition: all .2s; }
        .btn-solid:hover { background: var(--green-dark); }

        .hero { padding-top: 64px; min-height: 100vh; background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-mid) 55%, #1e8449 100%); display: flex; align-items: center; justify-content: center; text-align: center; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E"); }
        .hero-content { position: relative; z-index: 1; max-width: 760px; padding: 2rem; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); color: white; padding: 6px 16px; border-radius: 999px; font-size: .8rem; font-weight: 500; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.25); }
        .hero h1 { font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 800; color: white; line-height: 1.15; margin-bottom: 1.25rem; }
        .hero h1 span { color: #a7f3c8; }
        .hero p { font-size: 1.1rem; color: rgba(255,255,255,0.85); line-height: 1.75; margin-bottom: 2.5rem; max-width: 560px; margin-inline: auto; }
        .hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-hero-primary { padding: 14px 32px; border-radius: 10px; font-size: 1rem; font-weight: 700; background: white; color: var(--green-dark); text-decoration: none; transition: all .2s; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.2); }
        .btn-hero-secondary { padding: 14px 32px; border-radius: 10px; font-size: 1rem; font-weight: 600; background: rgba(255,255,255,0.12); color: white; text-decoration: none; border: 1.5px solid rgba(255,255,255,0.4); transition: all .2s; }
        .btn-hero-secondary:hover { background: rgba(255,255,255,0.22); }

        .wave { display: block; width: 100%; background: linear-gradient(135deg, var(--green-dark), #1e8449); line-height: 0; }
        .wave svg { display: block; width: 100%; }

        .section { padding: 80px 2rem; }
        .section-center { text-align: center; max-width: 620px; margin: 0 auto 56px; }
        .tag { display: inline-block; background: var(--green-pale); color: var(--green-dark); font-size: .75rem; font-weight: 700; padding: 4px 14px; border-radius: 999px; margin-bottom: 12px; letter-spacing: .06em; text-transform: uppercase; }
        .section-center h2 { font-size: 2rem; font-weight: 800; margin-bottom: 12px; }
        .section-center p { color: var(--gray-text); line-height: 1.7; }

        .features-grid { max-width: 1080px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px; }
        .feature-card { background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 30px 26px; transition: all .2s; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(45,155,90,0.12); border-color: var(--green-light); }
        .feature-icon { width: 50px; height: 50px; border-radius: 12px; background: var(--green-pale); color: var(--green-mid); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; margin-bottom: 18px; }
        .feature-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: .875rem; color: var(--gray-text); line-height: 1.65; }

        .steps-bg { background: var(--green-bg); }
        .steps-grid { max-width: 880px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 32px; }
        .step { text-align: center; }
        .step-num { width: 54px; height: 54px; border-radius: 50%; background: var(--green-mid); color: white; font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .step h4 { font-size: .975rem; font-weight: 700; margin-bottom: 8px; }
        .step p { font-size: .85rem; color: var(--gray-text); line-height: 1.65; }

        .stats-band { background: linear-gradient(135deg, var(--green-dark), var(--green-mid)); padding: 60px 2rem; }
        .stats-grid { max-width: 880px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 24px; text-align: center; }
        .stat-item { color: white; }
        .stat-item .icon-big { font-size: 2rem; color: #a7f3c8; margin-bottom: 8px; }
        .stat-item .label { font-size: .875rem; opacity: .85; }

        .cta-bottom { padding: 80px 2rem; text-align: center; background: white; }
        .cta-bottom h2 { font-size: 2rem; font-weight: 800; margin-bottom: 12px; }
        .cta-bottom p { color: var(--gray-text); margin-bottom: 32px; font-size: 1rem; }
        .btn-cta { display: inline-flex; align-items: center; gap: 10px; padding: 15px 38px; border-radius: 12px; background: var(--green-mid); color: white; font-size: 1.05rem; font-weight: 700; text-decoration: none; transition: all .2s; box-shadow: 0 4px 20px rgba(45,155,90,0.3); }
        .btn-cta:hover { background: var(--green-dark); transform: translateY(-2px); }

        footer { background: #111827; color: #9ca3af; text-align: center; padding: 22px 2rem; font-size: .85rem; }
        footer span { color: var(--green-light); font-weight: 500; }

        @media (max-width: 600px) { .nav-links .btn-outline { display: none; } }
    </style>
</head>
<body>

<nav>
    <a href="/" class="nav-logo">
        <span class="icon"><i class="fas fa-lungs"></i></span>
        Fisioterapia Respiratória
    </a>
    <div class="nav-links">
        @auth
            <a href="{{ url('/dashboard') }}" class="btn-solid"><i class="fas fa-tachometer-alt" style="margin-right:6px"></i>Sistema</a>
        @else
            <a href="{{ route('login') }}" class="btn-outline">Entrar</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-solid">Cadastrar-se</a>
            @endif
        @endauth
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <div class="hero-badge"><i class="fas fa-stethoscope"></i> Tecnologia a serviço da reabilitação respiratória</div>
        <h1>Laudos de Espirometria<br><span>com Inteligência Artificial</span></h1>
        <p>Plataforma digital que auxilia fisioterapeutas a analisar exames, gerar laudos automatizados e acompanhar a evolução clínica de pacientes com precisão e agilidade.</p>
        <div class="hero-cta">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-hero-primary"><i class="fas fa-tachometer-alt" style="margin-right:8px"></i>Acessar Sistema</a>
            @else
                <a href="{{ route('register') }}" class="btn-hero-primary"><i class="fas fa-user-plus" style="margin-right:8px"></i>Começar Agora</a>
                <a href="{{ route('login') }}" class="btn-hero-secondary">Já tenho conta</a>
            @endauth
        </div>
    </div>
</section>

<div class="wave">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,40 C360,0 1080,80 1440,20 L1440,60 L0,60 Z" fill="#ffffff"/>
    </svg>
</div>

<section class="section">
    <div class="section-center">
        <span class="tag">Funcionalidades</span>
        <h2>Tudo que você precisa em um só lugar</h2>
        <p>Desenvolvido para fisioterapeutas que precisam de agilidade, precisão e organização no dia a dia clínico.</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-robot"></i></div>
            <h3>Laudos com IA</h3>
            <p>Geração automática de laudos de espirometria com análise clínica estruturada em segundos, usando linguagem profissional.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-comments"></i></div>
            <h3>Chat com o Exame</h3>
            <p>Converse com a IA sobre o exame do paciente, tire dúvidas clínicas e solicite esclarecimentos diretamente na plataforma.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-users"></i></div>
            <h3>Gestão de Pacientes</h3>
            <p>Cadastro completo com dados clínicos relevantes como histórico de tabagismo, gênero e data de nascimento.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
            <h3>Evolução Clínica</h3>
            <p>Acompanhe a progressão pulmonar ao longo do tempo, comparando resultados e identificando melhoras.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-hospital"></i></div>
            <h3>Gestão de Clínica</h3>
            <p>Vincule profissionais à clínica, organize a equipe e mantenha os dados institucionais atualizados.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-file-pdf"></i></div>
            <h3>Exportação em PDF</h3>
            <p>Baixe os laudos em PDF formatado, pronto para impressão e arquivamento no prontuário do paciente.</p>
        </div>
    </div>
</section>

<section class="section steps-bg">
    <div class="section-center">
        <span class="tag">Como Funciona</span>
        <h2>Simples, rápido e eficiente</h2>
        <p>Em poucos passos você tem um laudo profissional gerado pela inteligência artificial.</p>
    </div>
    <div class="steps-grid">
        <div class="step">
            <div class="step-num">1</div>
            <h4>Cadastre o Paciente</h4>
            <p>Registre nome, data de nascimento, gênero e histórico de tabagismo.</p>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <h4>Envie o Exame</h4>
            <p>Faça upload do PDF da espirometria diretamente na plataforma.</p>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <h4>Gere o Laudo</h4>
            <p>A IA analisa o exame e gera um laudo clínico estruturado em segundos.</p>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <h4>Exporte ou Converse</h4>
            <p>Baixe o laudo em PDF ou use o chat para aprofundar a análise clínica.</p>
        </div>
    </div>
</section>

<div class="stats-band">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="icon-big"><i class="fas fa-bolt"></i></div>
            <div class="label">Laudos gerados em segundos</div>
        </div>
        <div class="stat-item">
            <div class="icon-big"><i class="fas fa-shield-alt"></i></div>
            <div class="label">Dados seguros e organizados</div>
        </div>
        <div class="stat-item">
            <div class="icon-big"><i class="fas fa-brain"></i></div>
            <div class="label">Powered by Gemini 2.5 Flash</div>
        </div>
        <div class="stat-item">
            <div class="icon-big"><i class="fas fa-file-medical"></i></div>
            <div class="label">Laudos prontos para impressão</div>
        </div>
    </div>
</div>

<section class="cta-bottom">
    <h2>Pronto para começar?</h2>
    <p>Crie sua conta e transforme a forma como você gera laudos respiratórios.</p>
    @auth
        <a href="{{ url('/dashboard') }}" class="btn-cta"><i class="fas fa-tachometer-alt"></i> Acessar o Sistema</a>
    @else
        <a href="{{ route('register') }}" class="btn-cta"><i class="fas fa-user-plus"></i> Criar conta gratuita</a>
    @endauth
</section>

<footer>
    <p>Desenvolvido como Trabalho de Conclusão de Curso &mdash; <span>Fisioterapia Respiratória</span></p>
</footer>

</body>
</html>
