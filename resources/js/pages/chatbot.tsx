import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Bot, MessageSquarePlus, Send, User, Database, Zap } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

interface Message {
    id: number;
    text: string;
    sender: 'user' | 'bot';
    timestamp: string;
    relevantDataCount?: number;
}

const STORAGE_KEY = 'chatbot_messages';

const getInitialMessage = (): Message => ({
    id: 1,
    text: 'Halo! Saya adalah chatbot SPK Management System. Saya dapat membantu Anda dengan pertanyaan seputar data SPK, jaringan, pelanggan, dan informasi teknis lainnya. Ada yang bisa saya bantu?',
    sender: 'bot',
    timestamp: new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    }),
});

export default function Chatbot() {
    const [messages, setMessages] = useState<Message[]>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    return JSON.parse(saved);
                } catch (e) {
                    console.error('Error parsing saved messages:', e);
                }
            }
        }
        return [getInitialMessage()];
    });

    const [inputMessage, setInputMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [streamingText, setStreamingText] = useState('');
    const [isStreaming, setIsStreaming] = useState(false);
    
    // ✅ TAMBAHAN: Toggle antara RAG mode dan Stream mode
    const [useRAG, setUseRAG] = useState(true);
    
    const messagesEndRef = useRef<HTMLDivElement>(null);
    const abortControllerRef = useRef<AbortController | null>(null);

    useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
        }
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, streamingText]);

    useEffect(() => {
        return () => {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }
        };
    }, []);

    const handleNewChat = () => {
        const confirmed = window.confirm('Apakah Anda yakin ingin memulai chat baru? Semua riwayat chat akan dihapus.');
        if (confirmed) {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }
            setIsStreaming(false);
            setStreamingText('');
            setMessages([getInitialMessage()]);
            setInputMessage('');
            setTimeout(() => {
                (document.querySelector('.chat-input') as HTMLInputElement)?.focus();
            }, 50);
        }
    };

    // ✅ FUNGSI RAG (Baru)
    const handleSendMessageRAG = async (messageToSend: string) => {
        setIsLoading(true);

        try {
            const response = await fetch('/chatbot/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    query: messageToSend,
                    search_type: 'both',
                    top_k: 3,
                }),
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            setIsLoading(false);

            if (data.success && data.data) {
                const botMessage: Message = {
                    id: Date.now(),
                    text: data.data.answer,
                    sender: 'bot',
                    timestamp: new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                    }),
                    relevantDataCount: data.data.relevant_data_count || 0,
                };
                
                setMessages((prev) => [...prev, botMessage]);
            } else {
                throw new Error(data.error || 'Terjadi kesalahan');
            }

        } catch (error: any) {
            setIsLoading(false);
            
            const errorText = error.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.';
            
            const botMessage: Message = {
                id: Date.now(),
                text: errorText,
                sender: 'bot',
                timestamp: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }),
            };
            
            setMessages((prev) => [...prev, botMessage]);
        }
    };

    // ✅ FUNGSI STREAMING (Existing - Tetap Dipertahankan)
    const handleSendMessageStream = async (messageToSend: string) => {
        setIsLoading(true);
        setStreamingText('');
        setIsStreaming(true);

        abortControllerRef.current = new AbortController();

        try {
            const response = await fetch('/chatbot/stream', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    message: messageToSend,
                }),
                signal: abortControllerRef.current.signal,
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            setIsLoading(false);

            const reader = response.body?.getReader();
            const decoder = new TextDecoder();
            let fullText = '';

            if (reader) {
                while (true) {
                    const { done, value } = await reader.read();
                    
                    if (done) break;

                    const chunk = decoder.decode(value, { stream: true });
                    const lines = chunk.split('\n');
                    
                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            try {
                                const jsonStr = line.substring(6);
                                const data = JSON.parse(jsonStr);
                                
                                if (data.error) {
                                    throw new Error(data.message || 'Terjadi kesalahan');
                                }
                                
                                if (data.token) {
                                    fullText += data.token;
                                    setStreamingText(fullText);
                                }
                                
                                if (data.done) {
                                    break;
                                }
                            } catch (e) {
                                // Skip invalid JSON
                            }
                        }
                    }
                }
            }

            setIsStreaming(false);
            
            const botMessage: Message = {
                id: Date.now(),
                text: fullText || 'Maaf, tidak ada respons.',
                sender: 'bot',
                timestamp: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }),
            };
            
            setMessages((prev) => [...prev, botMessage]);
            setStreamingText('');

        } catch (error: any) {
            setIsLoading(false);
            setIsStreaming(false);
            
            if (error.name === 'AbortError') {
                return;
            }
            
            const errorText = 'Maaf, terjadi kesalahan. Silakan coba lagi.';
            
            const botMessage: Message = {
                id: Date.now(),
                text: errorText,
                sender: 'bot',
                timestamp: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }),
            };
            
            setMessages((prev) => [...prev, botMessage]);
            setStreamingText('');
        }
    };

    // ✅ MAIN HANDLER - Pilih antara RAG atau Streaming
    const handleSendMessage = async () => {
        if (!inputMessage.trim() || isLoading || isStreaming) return;

        const userMessage: Message = {
            id: Date.now(),
            text: inputMessage,
            sender: 'user',
            timestamp: new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
            }),
        };

        const messageToSend = inputMessage;
        setMessages((prev) => [...prev, userMessage]);
        setInputMessage('');

        setTimeout(() => {
            (document.querySelector('.chat-input') as HTMLInputElement)?.focus();
        }, 50);

        // Pilih mode berdasarkan toggle
        if (useRAG) {
            await handleSendMessageRAG(messageToSend);
        } else {
            await handleSendMessageStream(messageToSend);
        }
    };

    const handleKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    };

    return (
        <AppLayout enableSticky={true}>
            <Head title="Chatbot" />
            <div className="flex h-full flex-1 flex-col">
                <div className="sticky top-0 z-50 transition-all duration-300">
                    <div className="border-b bg-background/95 p-3 shadow-md backdrop-blur-md supports-[backdrop-filter]:bg-background/90 md:mx-2 md:rounded-lg md:border md:p-6">
                        <div className="flex items-center justify-between gap-2">
                            <div className="flex items-center gap-2">
                                <Bot className="h-5 w-5 md:h-6 md:w-6" />
                                <div>
                                    <div className="flex items-center gap-2">
                                        <CardTitle className="text-base md:text-lg">SPK Chatbot</CardTitle>
                                        {/* ✅ BADGE MODE */}
                                        <Badge variant={useRAG ? "default" : "secondary"} className="text-[10px]">
                                            {useRAG ? (
                                                <span className="flex items-center gap-1">
                                                    <Database className="h-2.5 w-2.5" />
                                                    RAG
                                                </span>
                                            ) : (
                                                <span className="flex items-center gap-1">
                                                    <Zap className="h-2.5 w-2.5" />
                                                    Stream
                                                </span>
                                            )}
                                        </Badge>
                                    </div>
                                    <CardDescription className="hidden text-xs md:block md:text-sm">
                                        {useRAG 
                                            ? 'Mode RAG: Jawaban berdasarkan database SPK'
                                            : 'Mode Stream: Jawaban streaming real-time'
                                        }
                                    </CardDescription>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                {/* ✅ TOGGLE MODE */}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => setUseRAG(!useRAG)}
                                    className="flex cursor-pointer items-center gap-1 md:gap-2"
                                >
                                    {useRAG ? <Zap className="h-3 w-3 md:h-4 md:w-4" /> : <Database className="h-3 w-3 md:h-4 md:w-4" />}
                                    <span className="hidden md:inline">{useRAG ? 'Stream' : 'RAG'}</span>
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={handleNewChat}
                                    className="flex cursor-pointer items-center gap-1 bg-white text-black md:gap-2"
                                >
                                    <MessageSquarePlus className="h-3 w-3 md:h-4 md:w-4" />
                                    <span className="hidden md:inline">New Chat</span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <Card className="m-0 flex flex-1 flex-col overflow-hidden transition-all duration-300 md:m-2">
                    <CardContent className="flex-1 overflow-hidden p-0">
                        <ScrollArea className="h-full px-3 py-2 pb-32 md:px-6 md:py-4 md:pb-40">
                            <div className="space-y-3 md:space-y-4">
                                {messages.map((message) => (
                                    <div
                                        key={message.id}
                                        className={`flex items-start gap-2 md:gap-3 ${message.sender === 'user' ? 'flex-row-reverse' : ''}`}
                                    >
                                        <div
                                            className={`flex h-7 w-7 shrink-0 items-center justify-center rounded-full md:h-8 md:w-8 ${
                                                message.sender === 'user' ? 'bg-primary text-primary-foreground' : 'bg-muted'
                                            }`}
                                        >
                                            {message.sender === 'user' ? <User className="h-3 w-3 md:h-4 md:w-4" /> : <Bot className="h-3 w-3 md:h-4 md:w-4" />}
                                        </div>
                                        <div className={`flex max-w-[75%] flex-col gap-1 md:max-w-[80%] ${message.sender === 'user' ? 'items-end' : 'items-start'}`}>
                                            <div
                                                className={`rounded-lg px-3 py-2 md:px-4 ${message.sender === 'user' ? 'bg-blue-700 text-white' : 'bg-muted'}`}
                                            >
                                                <p className="whitespace-pre-line text-xs md:text-sm">{message.text}</p>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-[10px] text-muted-foreground md:text-xs">{message.timestamp}</span>
                                                {/* ✅ Tampilkan jumlah data relevan (RAG mode) */}
                                                {message.sender === 'bot' && message.relevantDataCount !== undefined && message.relevantDataCount > 0 && (
                                                    <span className="flex items-center gap-1 text-[10px] text-muted-foreground md:text-xs">
                                                        <Database className="h-2.5 w-2.5 md:h-3 md:w-3" />
                                                        {message.relevantDataCount} data
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                                
                                {isLoading && (
                                    <div className="flex items-start gap-2 md:gap-3">
                                        <div className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-muted md:h-8 md:w-8">
                                            <Bot className="h-3 w-3 md:h-4 md:w-4" />
                                        </div>
                                        <div className="rounded-lg bg-muted px-3 py-2 md:px-4">
                                            <div className="flex gap-1">
                                                <div className="h-1.5 w-1.5 animate-bounce rounded-full bg-muted-foreground [animation-delay:-0.3s] md:h-2 md:w-2"></div>
                                                <div className="h-1.5 w-1.5 animate-bounce rounded-full bg-muted-foreground [animation-delay:-0.15s] md:h-2 md:w-2"></div>
                                                <div className="h-1.5 w-1.5 animate-bounce rounded-full bg-muted-foreground md:h-2 md:w-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {isStreaming && streamingText && (
                                    <div className="flex items-start gap-2 md:gap-3">
                                        <div className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-muted md:h-8 md:w-8">
                                            <Bot className="h-3 w-3 md:h-4 md:w-4" />
                                        </div>
                                        <div className="flex max-w-[75%] flex-col gap-1 md:max-w-[80%]">
                                            <div className="rounded-lg bg-muted px-3 py-2 md:px-4">
                                                <p className="whitespace-pre-line text-xs md:text-sm">
                                                    {streamingText}
                                                    <span className="animate-pulse">▊</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                <div ref={messagesEndRef} />
                            </div>
                        </ScrollArea>
                    </CardContent>
                </Card>

                <div className="sticky bottom-0 z-50 px-24 transition-all duration-300 md:bottom-4">
                    <div className="rounded-t-lg border-t bg-background p-3 shadow-xl md:mx-2 md:rounded-lg md:border md:bg-background/95 md:p-4 md:backdrop-blur md:supports-[backdrop-filter]:bg-background/80">
                        <div className="flex items-center gap-2">
                            <Input
                                placeholder={useRAG ? "Tanya tentang SPK, nojar, pelanggan..." : "Ketik pesan..."}
                                autoFocus
                                value={inputMessage}
                                onChange={(e) => setInputMessage(e.target.value)}
                                onKeyPress={handleKeyPress}
                                className="chat-input flex-1 text-sm md:text-base"
                                disabled={isLoading || isStreaming}
                            />
                            <Button 
                                onClick={handleSendMessage} 
                                disabled={!inputMessage.trim() || isLoading || isStreaming} 
                                size="icon" 
                                className="h-9 w-9 cursor-pointer md:h-10 md:w-10"
                            >
                                <Send className="h-3.5 w-3.5 md:h-4 md:w-4" />
                            </Button>
                        </div>

                        <p className="mt-2 hidden text-xs text-muted-foreground md:block">
                            {useRAG 
                                ? '💡 Tips: "Cek nojar 12345", "Siapa vendor SPK-001?", "Lokasi pelanggan PT Telkom?"'
                                : '💡 Tips: Tanyakan tentang produk, layanan, kontak, atau informasi perusahaan kami'
                            }
                        </p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}