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
    // Load messages from localStorage or use initial message
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

    // Save messages to localStorage whenever they change
    useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
        }
    }, [messages]);

    // Auto scroll to bottom when messages change
    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, isLoading]);

    const handleNewChat = () => {
        const confirmed = window.confirm('Apakah Anda yakin ingin memulai chat baru? Semua riwayat chat akan dihapus.');
        if (confirmed) {
            setMessages([getInitialMessage()]);
            setInputMessage('');
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
                <Card className="m-2 flex h-full flex-col">
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <Bot className="h-6 w-6" />
                                <div>
                                    <CardTitle>Company Chatbot</CardTitle>
                                    <CardDescription>Tanyakan apa saja tentang perusahaan, produk, dan layanan kami</CardDescription>
                                </div>
                            </div>
                            <Button variant="outline" size="sm" onClick={handleNewChat} className="flex items-center gap-2 bg-white text-black cursor-pointer">
                                <MessageSquarePlus className="h-4 w-4" />
                                New Chat
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent className="flex flex-1 flex-col gap-4 overflow-hidden">
                        {/* Messages Area */}
                        <ScrollArea className="flex-1 pr-4">
                            <div className="space-y-4">
                                {messages.map((message) => (
                                    <div key={message.id} className={`flex items-start gap-3 ${message.sender === 'user' ? 'flex-row-reverse' : ''}`}>
                                        <div
                                            className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-full ${
                                                message.sender === 'user' ? 'bg-primary text-primary-foreground' : 'bg-muted'
                                            }`}
                                        >
                                            {message.sender === 'user' ? <User className="h-4 w-4" /> : <Bot className="h-4 w-4" />}
                                        </div>
                                        <div className={`flex flex-col gap-1 ${message.sender === 'user' ? 'items-end' : 'items-start'}`}>
                                            <div
                                                className={`rounded-lg px-4 py-2 ${message.sender === 'user' ? 'bg-blue-700 text-white' : 'bg-muted'}`}
                                            >
                                                <p className="text-sm">{message.text}</p>
                                            </div>
                                            <span className="text-xs text-muted-foreground">{message.timestamp}</span>
                                        </div>
                                    </div>
                                ))}
                                {isLoading && (
                                    <div className="flex items-start gap-3">
                                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-muted">
                                            <Bot className="h-4 w-4" />
                                        </div>
                                        <div className="rounded-lg bg-muted px-4 py-2">
                                            <div className="flex gap-1">
                                                <div className="h-2 w-2 animate-bounce rounded-full bg-muted-foreground [animation-delay:-0.3s]"></div>
                                                <div className="h-2 w-2 animate-bounce rounded-full bg-muted-foreground [animation-delay:-0.15s]"></div>
                                                <div className="h-2 w-2 animate-bounce rounded-full bg-muted-foreground"></div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                                {/* Invisible element for scrolling */}
                                <div ref={messagesEndRef} />
                            </div>
                        </ScrollArea>

                        {/* Input Area */}
                        <div className="flex items-center gap-2">
                            <Input
                                placeholder="Ketik pesan Anda di sini..."
                                value={inputMessage}
                                onChange={(e) => setInputMessage(e.target.value)}
                                onKeyPress={handleKeyPress}
                                disabled={isLoading}
                                className="flex-1"
                            />
                            <Button onClick={handleSendMessage} disabled={!inputMessage.trim() || isLoading} size="icon" className='cursor-pointer'>
                                <Send className="h-4 w-4" />
                            </Button>
                        </div>

                        {/* Info Text */}
                        <p className="text-xs text-muted-foreground">
                            💡 Tips: Tanyakan tentang produk, layanan, kontak, atau informasi perusahaan kami
                        </p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
