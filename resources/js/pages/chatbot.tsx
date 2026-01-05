import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { SidebarTrigger } from '@/components/ui/sidebar';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Bot, Database, MessageSquarePlus, Send, User, Zap } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

interface Message {
    id: number;
    text: string;
    sender: 'user' | 'bot';
    timestamp: string;
    relevantDataCount?: number;
    extractedEntities?: {
        nojar?: string;
        pelanggan?: string;
        spk?: string;
        pop?: string;
    };
}

const STORAGE_KEY = 'chatbot_messages';

// ✅ Message ID counter untuk generate unique ID
let messageIdCounter = 0;

const getInitialMessage = (): Message => ({
    id: ++messageIdCounter, // ✅ Unique ID
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
                    const parsed = JSON.parse(saved);
                    // ✅ Re-assign unique IDs untuk semua messages dari localStorage
                    if (parsed.length > 0) {
                        const messagesWithNewIds = parsed.map((msg: Message) => ({
                            ...msg,
                            id: ++messageIdCounter, // ✅ Generate new unique ID
                        }));
                        return messagesWithNewIds;
                    }
                } catch (e) {
                    console.error('Error parsing saved messages:', e);
                    // ✅ Clear corrupted localStorage
                    localStorage.removeItem(STORAGE_KEY);
                }
            }
        }
        return [getInitialMessage()];
    });

    const [currentContext, setCurrentContext] = useState<{
        last_nojar?: string;
        last_pelanggan?: string;
        last_spk?: string;
        last_pop?: string;
    }>({});

    const [inputMessage, setInputMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [streamingText, setStreamingText] = useState('');
    const [isStreaming, setIsStreaming] = useState(false);
    const [newChatDialogOpen, setNewChatDialogOpen] = useState(false);

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
        setNewChatDialogOpen(true);
    };

    const confirmNewChat = () => {
        if (abortControllerRef.current) {
            abortControllerRef.current.abort();
        }
        setIsStreaming(false);
        setStreamingText('');

        // Reset counter saat new chat
        messageIdCounter = 0;
        setMessages([getInitialMessage()]);
        setInputMessage('');
        setCurrentContext({});
        setNewChatDialogOpen(false);

        setTimeout(() => {
            (document.querySelector('.chat-input') as HTMLInputElement)?.focus();
        }, 50);
    };

    // ✅ FUNGSI RAG MODE DENGAN STREAMING REAL-TIME
    const handleSendMessageRAG = async (messageToSend: string) => {
        setIsLoading(true);
        setStreamingText('');
        setIsStreaming(true);

        abortControllerRef.current = new AbortController();

        try {
            // ✅ Build conversation history (10 messages terakhir)
            const conversationHistory = messages.slice(-10).map((msg) => ({
                role: msg.sender === 'user' ? 'user' : 'assistant',
                content: msg.text,
                timestamp: msg.timestamp,
            }));

            console.log('📤 Sending RAG streaming request:', {
                query: messageToSend,
                has_history: conversationHistory.length > 0,
                has_context: Object.keys(currentContext).length > 0,
                context: currentContext,
            });

            // ✅ Get CSRF token - coba beberapa cara
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Fallback: cari di meta tag lain
            if (!csrfToken) {
                csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content');
            }

            // Fallback: cari di input hidden
            if (!csrfToken) {
                csrfToken = (document.querySelector('input[name="_token"]') as HTMLInputElement)?.value;
            }

            console.log('🔍 CSRF Token found:', !!csrfToken);

            if (!csrfToken) {
                console.error('❌ CSRF token tidak ditemukan di DOM');
                console.log(
                    'Available meta tags:',
                    Array.from(document.querySelectorAll('meta')).map((m) => m.getAttribute('name')),
                );
                throw new Error('CSRF token tidak ditemukan. Pastikan meta tag CSRF ada di layout.');
            }

            console.log('🔑 CSRF Token:', csrfToken.substring(0, 20) + '...');

            // ✅ Call Laravel streaming endpoint dengan RAG context
            const response = await fetch('/chatbot/chat-stream', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'text/event-stream',
                },
                body: JSON.stringify({
                    query: messageToSend,
                    conversation_history: conversationHistory, // ✅ RAG: conversation memory
                    current_context: currentContext, // ✅ RAG: extracted entities context
                }),
                signal: abortControllerRef.current.signal,
            });

            if (!response.ok) {
                console.error('❌ Response not OK:', {
                    status: response.status,
                    statusText: response.statusText,
                    url: response.url,
                });

                // Coba baca error message dari response
                const errorText = await response.text();
                console.error('Error body:', errorText);

                throw new Error(`Request failed: ${response.status} ${response.statusText}`);
            }

            setIsLoading(false);

            // ✅ Read streaming response token by token
            const reader = response.body?.getReader();
            const decoder = new TextDecoder();
            let fullText = '';

            if (reader) {
                while (true) {
                    const { done, value } = await reader.read();

                    if (done) {
                        console.log('✅ Streaming completed, total length:', fullText.length);
                        break;
                    }

                    const chunk = decoder.decode(value, { stream: true });
                    const lines = chunk.split('\n');

                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            try {
                                const jsonStr = line.substring(6);
                                const data = JSON.parse(jsonStr);

                                if (data.error) {
                                    throw new Error(data.error || 'Terjadi kesalahan');
                                }

                                // 🔥 Terima token dari Ollama via Flask via Laravel
                                if (data.token) {
                                    fullText += data.token;
                                    setStreamingText(fullText); // 🔥 Update UI real-time per token
                                }

                                // ✅ Check jika streaming selesai
                                if (data.done) {
                                    console.log('🏁 Stream done signal received');
                                    break;
                                }
                            } catch (e) {
                                // Skip invalid JSON
                                console.warn('Invalid JSON in stream:', line, e);
                            }
                        }
                    }
                }
            }

            setIsStreaming(false);

            // ✅ Save complete message dengan unique ID
            const botMessage: Message = {
                id: ++messageIdCounter, // ✅ FIXED: Unique ID
                text: fullText || 'Maaf, tidak ada respons.',
                sender: 'bot',
                timestamp: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }),
                // Note: Untuk mode streaming, kita tidak bisa tahu relevantDataCount
                // karena response langsung stream tanpa metadata
            };

            setMessages((prev) => [...prev, botMessage]);
            setStreamingText('');

            console.log('✅ Message saved to history');
        } catch (error: any) {
            setIsLoading(false);
            setIsStreaming(false);

            if (error.name === 'AbortError') {
                console.log('⚠️ Stream aborted by user');
                return;
            }

            console.error('❌ RAG Streaming error:', error);

            const errorText = error.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.';

            const botMessage: Message = {
                id: ++messageIdCounter, // ✅ FIXED: Unique ID
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

    // ✅ MAIN HANDLER - Langsung pakai RAG Streaming
    const handleSendMessage = async () => {
        if (!inputMessage.trim() || isLoading || isStreaming) return;

        const userMessage: Message = {
            id: ++messageIdCounter, // ✅ FIXED: Unique ID
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

        // 🔥 Langsung pakai RAG Streaming
        await handleSendMessageRAG(messageToSend);
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
                                <SidebarTrigger className="md:hidden" />
                                <Bot className="h-5 w-5 md:h-6 md:w-6" />
                                <div>
                                    <div className="flex items-center gap-2">
                                        <CardTitle className="text-base md:text-lg">SPK Chatbot</CardTitle>
                                        <Badge variant="default" className="text-[10px]">
                                            <span className="flex items-center gap-1">
                                                <Zap className="h-2.5 w-2.5" />
                                                RAG Stream
                                            </span>
                                        </Badge>
                                    </div>
                                    <CardDescription className="hidden text-xs md:block md:text-sm">
                                        Mode RAG Streaming: Jawaban real-time dari database SPK
                                    </CardDescription>

                                    {/* ✅ CONTEXT INDICATOR */}
                                    {Object.keys(currentContext).length > 0 && (
                                        <div className="mt-1 flex flex-wrap gap-1">
                                            {currentContext.last_nojar && (
                                                <Badge variant="outline" className="text-[9px] md:text-[10px]">
                                                    📍 Nojar: {currentContext.last_nojar}
                                                </Badge>
                                            )}
                                            {currentContext.last_pelanggan && (
                                                <Badge variant="outline" className="text-[9px] md:text-[10px]">
                                                    👤 {currentContext.last_pelanggan.substring(0, 20)}...
                                                </Badge>
                                            )}
                                            {currentContext.last_spk && (
                                                <Badge variant="outline" className="text-[9px] md:text-[10px]">
                                                    📄 SPK: {currentContext.last_spk}
                                                </Badge>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
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
                                            {message.sender === 'user' ? (
                                                <User className="h-3 w-3 md:h-4 md:w-4" />
                                            ) : (
                                                <Bot className="h-3 w-3 md:h-4 md:w-4" />
                                            )}
                                        </div>
                                        <div
                                            className={`flex max-w-[75%] flex-col gap-1 md:max-w-[80%] ${message.sender === 'user' ? 'items-end' : 'items-start'}`}
                                        >
                                            <div
                                                className={`rounded-lg px-3 py-2 md:px-4 ${message.sender === 'user' ? 'bg-blue-700 text-white' : 'bg-muted'}`}
                                            >
                                                <p className="text-xs whitespace-pre-line md:text-sm">{message.text}</p>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-[10px] text-muted-foreground md:text-xs">{message.timestamp}</span>
                                                {message.sender === 'bot' &&
                                                    message.relevantDataCount !== undefined &&
                                                    message.relevantDataCount > 0 && (
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
                                                <p className="text-xs whitespace-pre-line md:text-sm">
                                                    {streamingText}
                                                    <span className="animate-pulse">▊</span>
                                                </p>
                                            </div>
                                            <span className="flex items-center gap-1 text-[10px] text-muted-foreground md:text-xs">
                                                <Zap className="h-2.5 w-2.5 md:h-3 md:w-3" />
                                                Streaming...
                                            </span>
                                        </div>
                                    </div>
                                )}

                                <div ref={messagesEndRef} />
                            </div>
                        </ScrollArea>
                    </CardContent>
                </Card>

                <div className="sticky bottom-0 z-50 transition-all duration-300 md:bottom-4 md:px-2">
                    <div className="rounded-t-lg border-t bg-background p-3 shadow-xl md:mx-2 md:rounded-lg md:border md:bg-background/95 md:p-4 md:backdrop-blur md:supports-[backdrop-filter]:bg-background/80">
                        <div className="flex items-center gap-2">
                            <Input
                                placeholder="Tanya tentang SPK, nojar, pelanggan..."
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
                                className="h-9 w-9 shrink-0 cursor-pointer md:h-10 md:w-10"
                            >
                                <Send className="h-3.5 w-3.5 md:h-4 md:w-4" />
                            </Button>
                        </div>

                        <p className="mt-2 hidden text-xs text-muted-foreground md:block">
                            💡 Tips: "Cek nojar 12345", "Siapa vendor SPK-001?", "Lokasi pelanggan PT Telkom?"
                        </p>
                    </div>
                </div>
            </div>
            {/* Alert Dialog untuk New Chat */}
            <AlertDialog open={newChatDialogOpen} onOpenChange={setNewChatDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Mulai chat baru?</AlertDialogTitle>
                        <AlertDialogDescription>Semua riwayat chat akan dihapus. Tindakan ini tidak dapat dibatalkan.</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Batal</AlertDialogCancel>
                        <AlertDialogAction onClick={confirmNewChat} className="cursor-pointer bg-blue-600 text-white hover:bg-blue-700">
                            Mulai Chat Baru
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </AppLayout>
    );
}
