import React, { useState, useEffect, useRef } from 'react';
import { Send, FileText, Bot, User, Loader2, Save } from 'lucide-react';
// Acessamos jsPDF a partir do objeto global 'window.jspdf' carregado via CDN no Blade.
// N\u00e3o h\u00e1 necessidade de um 'import' direto aqui que causaria erro de resolu\u00e7\u00e3o com Vite.
// import jsPDF from 'jspdf'; // Esta linha deve estar comentada ou removida.

// Main App Component
const App = () => {
    // State para armazenar as mensagens do chat
    const [messages, setMessages] = useState([]);
    // State para o input atual da mensagem
    const [input, setInput] = useState('');
    // State para indicar se a IA est\u00e1 pensando (carregando)
    const [loading, setLoading] = useState(false);
    // State para armazenar o ID do exame
    const [examId, setExamId] = useState(null);
    // Ref para rolar automaticamente para o final do chat
    const chatEndRef = useRef(null);

    // Efeito para buscar o ID e nome do exame do objeto window (passado pelo Blade)
    useEffect(() => {
        if (window.examId) {
            setExamId(window.examId);
            // Mensagem inicial do assistente de IA
            setMessages([
                { role: 'bot', text: `Ol\u00e1! Eu sou seu assistente de IA. Estou aqui para conversar sobre o exame ${window.examOriginalFilename ? window.examOriginalFilename : 'selecionado'}. Como posso ajudar?` }
            ]);
        } else {
            setMessages([
                { role: 'bot', text: 'Erro: ID do exame n\u00e3o encontrado. Por favor, acesse esta p\u00e1gina atrav\u00e9s da lista de exames.' }
            ]);
        }
    }, []);

    // Efeito para rolar para a \u00faltima mensagem sempre que novas mensagens s\u00e3o adicionadas
    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    // Fun\u00e7\u00e3o ass\u00edncrona para enviar uma mensagem para a IA
    const sendMessage = async () => {
        if (!input.trim() || loading || !examId) return; // N\u00e3o envia se o input estiver vazio, carregando ou sem examId

        const userMessage = { role: 'user', text: input };
        setMessages((prevMessages) => [...prevMessages, userMessage]); // Adiciona a mensagem do usu\u00e1rio
        setInput(''); // Limpa o input
        setLoading(true); // Ativa o estado de carregamento

        try {
            // Faz a requisi\u00e7\u00e3o POST para o endpoint da API Laravel
            const response = await fetch(`/api/exames/${examId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Obt\u00e9m o token CSRF de uma meta tag no HTML para seguran\u00e7a
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: input }) // Envia a mensagem do usu\u00e1rio no corpo da requisi\u00e7\u00e3o
            });

            // Lida com respostas que n\u00e3o s\u00e3o OK (erros HTTP)
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Erro ao comunicar com a IA.');
            }

            const data = await response.json(); // Pega a resposta JSON
            setMessages((prevMessages) => [...prevMessages, { role: 'bot', text: data.reply }]); // Adiciona a resposta da IA
        } catch (error) {
            console.error('Erro ao enviar mensagem:', error);
            setMessages((prevMessages) => [...prevMessages, { role: 'bot', text: `Desculpe, houve um erro ao processar sua solicita\u00e7\u00e3o: ${error.message}` }]);
        } finally {
            setLoading(false); // Desativa o estado de carregamento
        }
    };

    // Lida com o evento de tecla pressionada para enviar mensagem ao pressionar Enter
    const handleKeyPress = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { // Envia ao pressionar Enter sem Shift
            e.preventDefault();
            sendMessage();
        }
    };

    // Fun\u00e7\u00e3o para gerar e baixar o PDF da conversa do chat
    const generatePdf = () => {
        // Acessa jsPDF a partir do objeto global 'window.jspdf' carregado via CDN
        // Isso evita problemas de resolu\u00e7\u00e3o de m\u00f3dulo com Vite para libs carregadas globalmente.
        const doc = new window.jspdf.jsPDF();
        let yPos = 10; // Posi\u00e7\u00e3o Y inicial para o conte\u00fado
        const margin = 10;
        const maxWidth = doc.internal.pageSize.width - 2 * margin;

        doc.setFontSize(16);
        doc.text(`Hist\u00f3rico do Chat - Exame ID: ${examId}`, margin, yPos);
        yPos += 10;
        doc.setFontSize(12);

        // Itera sobre as mensagens e adiciona ao PDF
        messages.forEach(msg => {
            const prefix = msg.role === 'user' ? 'Voc\u00ea: ' : 'IA: ';
            // Divide o texto em linhas para quebrar corretamente dentro da largura da p\u00e1gina
            const textLines = doc.splitTextToSize(prefix + msg.text, maxWidth);
            doc.text(textLines, margin, yPos);
            yPos += (textLines.length * 7) + 5; // Ajusta a posi\u00e7\u00e3o Y para a pr\u00f3xima mensagem

            // Adiciona nova p\u00e1gina se o conte\u00fado exceder a p\u00e1gina atual
            if (yPos > doc.internal.pageSize.height - 20) {
                doc.addPage();
                yPos = 10;
            }
        });

        doc.save(`chat_exame_${examId}.pdf`); // Salva o documento PDF
    };

    return (
        <div className="flex flex-col h-screen bg-gray-100 rounded-lg shadow-lg overflow-hidden">
            <header className="bg-indigo-600 text-white p-4 flex items-center justify-between shadow-md">
                <h1 className="text-xl font-semibold flex items-center">
                    <FileText className="mr-2" /> Chat sobre o Exame {examId ? `#${examId}` : ''}
                </h1>
                <button onClick={generatePdf} className="flex items-center bg-indigo-700 hover:bg-indigo-800 text-white py-2 px-4 rounded-md transition-colors duration-200 shadow-sm">
                    <Save className="mr-2" size={18} /> Salvar PDF
                </button>
            </header>

            <main className="flex-1 p-4 overflow-y-auto bg-gray-50 custom-scrollbar">
                <div className="flex flex-col space-y-4">
                    {messages.map((msg, index) => (
                        <div
                            key={index}
                            className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}
                        >
                            <div
                                className={`flex items-start max-w-lg p-3 rounded-lg shadow-md ${
                                    msg.role === 'user'
                                        ? 'bg-blue-500 text-white rounded-br-none'
                                        : 'bg-gray-200 text-gray-800 rounded-tl-none'
                                }`}
                            >
                                <div className="flex-shrink-0 mr-2">
                                    {msg.role === 'user' ? (
                                        <User className="w-6 h-6" />
                                    ) : (
                                        <Bot className="w-6 h-6" />
                                    )}
                                </div>
                                <p className="break-words">{msg.text}</p>
                            </div>
                        </div>
                    ))}
                    {loading && (
                        <div className="flex justify-start">
                            <div className="flex items-start max-w-lg p-3 rounded-lg shadow-md bg-gray-200 text-gray-800 rounded-tl-none">
                                <Bot className="w-6 h-6 flex-shrink-0 mr-2" />
                                <Loader2 className="animate-spin w-6 h-6 text-gray-600" />
                                <span className="ml-2">Digitando...</span>
                            </div>
                        </div>
                    )}
                    <div ref={chatEndRef} /> {/* \u00c1rea para o scroll autom\u00e1tico */}
                </div>
            </main>

            <footer className="p-4 bg-white border-t border-gray-200 flex items-center shadow-md">
                <input
                    type="text"
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    onKeyPress={handleKeyPress}
                    placeholder="Digite sua mensagem..."
                    className="flex-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 shadow-sm"
                    disabled={!examId || loading}
                />
                <button
                    onClick={sendMessage}
                    className="ml-3 bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-lg shadow-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    disabled={!input.trim() || loading || !examId}
                >
                    <Send className="w-6 h-6" />
                </button>
            </footer>
        </div>
    );
};

export default App;
