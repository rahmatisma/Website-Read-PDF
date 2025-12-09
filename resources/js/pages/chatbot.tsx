import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import { Bot, MessageSquarePlus, Send, User } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Chatbot',
        href: '/test',
    },
];

interface Message {
    id: number;
    text: string;
    sender: 'user' | 'bot';
    timestamp: string;
}

const STORAGE_KEY = 'chatbot_messages';

const getInitialMessage = (): Message => ({
    id: 1,
    text: 'Halo! Saya adalah chatbot perusahaan. Saya dapat membantu Anda dengan pertanyaan seputar produk, layanan, dan informasi perusahaan kami. Ada yang bisa saya bantu?',
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
    const messagesEndRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
        }
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, isLoading]);

    const handleNewChat = () => {
        const confirmed = window.confirm('Apakah Anda yakin ingin memulai chat baru? Semua riwayat chat akan dihapus.');
        if (confirmed) {
            setMessages([getInitialMessage()]);
            setInputMessage('');
            setTimeout(() => {
                (document.querySelector('.chat-input') as HTMLInputElement)?.focus();
            }, 50);
        }
    };

    const handleSendMessage = async () => {
        if (!inputMessage.trim() || isLoading) return;

        const userMessage: Message = {
            id: messages.length + 1,
            text: inputMessage,
            sender: 'user',
            timestamp: new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
            }),
        };

        setMessages((prev) => [...prev, userMessage]);
        setInputMessage('');

        setTimeout(() => {
            (document.querySelector('.chat-input') as HTMLInputElement)?.focus();
        }, 50);
        setIsLoading(true);

        try {
            const response = await axios.post('/chatbot/message', {
                message: inputMessage,
            });

            const botMessage: Message = {
                id: messages.length + 2,
                text: response.data.message,
                sender: 'bot',
                timestamp: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }),
            };

            setMessages((prev) => [...prev, botMessage]);
        } catch (error: any) {
            const errorMessage: Message = {
                id: messages.length + 2,
                text: error.response?.data?.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.',
                sender: 'bot',
                timestamp: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }),
            };
            setMessages((prev) => [...prev, errorMessage]);
        } finally {
            setIsLoading(false);
        }
    };

    const handleKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Chatbot" />
            <div className="flex h-[calc(100vh-8rem)] flex-1 flex-col">
                <Card className="m-0 flex h-full flex-col overflow-hidden md:m-2">
                    <CardHeader className="shrink-0 p-3 md:p-6">
                        <div className="flex items-center justify-between gap-2">
                            <div className="flex items-center gap-2">
                                <Bot className="h-5 w-5 md:h-6 md:w-6" />
                                <div>
                                    <CardTitle className="text-base md:text-lg">Company Chatbot</CardTitle>
                                    <CardDescription className="hidden text-xs md:block md:text-sm">
                                        Tanyakan apa saja tentang perusahaan, produk, dan layanan kami
                                    </CardDescription>
                                </div>
                            </div>
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
                    </CardHeader>

                    {/* Messages Area */}
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
                                                <p className="text-xs md:text-sm">{message.text}</p>
                                            </div>
                                            <span className="text-[10px] text-muted-foreground md:text-xs">{message.timestamp}</span>
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
                                <div ref={messagesEndRef} />
                            </div>
                        </ScrollArea>
                    </CardContent>
                </Card>

                {/* Floating Input Area - Responsive untuk mobile dan desktop */}
                <div className="fixed inset-x-0 bottom-0 z-50 md:bottom-4 md:left-72 md:right-8">
                    <div className="mx-auto rounded-t-lg border-t bg-background p-3 shadow-xl md:rounded-lg md:border md:bg-background/95 md:p-4 md:backdrop-blur md:supports-[backdrop-filter]:bg-background/80">
                        <div className="flex items-center gap-2">
                            <Input
                                placeholder="Ketik pesan..."
                                autoFocus
                                value={inputMessage}
                                onChange={(e) => setInputMessage(e.target.value)}
                                onKeyPress={handleKeyPress}
                                className="chat-input flex-1 text-sm md:text-base"
                            />
                            <Button 
                                onClick={handleSendMessage} 
                                disabled={!inputMessage.trim() || isLoading} 
                                size="icon" 
                                className="h-9 w-9 cursor-pointer md:h-10 md:w-10"
                            >
                                <Send className="h-3.5 w-3.5 md:h-4 md:w-4" />
                            </Button>
                        </div>

                        {/* Info Text - Hidden on mobile */}
                        <p className="mt-2 hidden text-xs text-muted-foreground md:block">
                            💡 Tips: Tanyakan tentang produk, layanan, kontak, atau informasi perusahaan kami
                        </p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}