import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import { Calendar, CheckCircle2, File, FileText, HardDrive, Mail, Shield, Upload, FileCheck, ClipboardCheck, Clock } from 'lucide-react';
import { useState } from 'react';
import { Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

interface Document {
    id: number;
    fileName: string;
    uploadedDate: string;
    fileSize: string;
    status?: 'uploaded' | 'processing' | 'completed' | 'failed';
}

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface DashboardProps {
    countDOC: number;
    countIMG: number;
    countAll: number;
    countUsersVerified?: number;
    countSPKTypes?: number;
    countFormChecklist?: number;
    uploadTrend?: { date: string; uploads: number }[];
    recentDocuments?: Document[];
    [key: string]: unknown;
}

export default function Dashboard() {
    const {
        countUsersVerified = 0,
        countSPKTypes = 0,
        countFormChecklist = 0,
        uploadTrend = [],
        recentDocuments = [],
    } = usePage<DashboardProps>().props;

    // Dummy data untuk upload trend (7 hari terakhir)
    const defaultUploadTrend = [
        { date: '17 Dec', uploads: 12 },
        { date: '18 Dec', uploads: 19 },
        { date: '19 Dec', uploads: 15 },
        { date: '20 Dec', uploads: 25 },
        { date: '21 Dec', uploads: 22 },
        { date: '22 Dec', uploads: 30 },
        { date: '23 Dec', uploads: 28 },
    ];

    // Dummy data untuk recent documents
    const defaultRecentDocuments: Document[] = [
        {
            id: 1,
            fileName: 'SPK_Masket_2025.pdf',
            uploadedDate: '2025-12-23 10:30',
            fileSize: '2.4 MB',
            status: 'processing',
        },
        {
            id: 2,
            fileName: 'Proposal_Jaringan_Lintasarta.pdf',
            uploadedDate: '2025-12-23 09:15',
            fileSize: '1.8 MB',
            status: 'completed',
        },
        {
            id: 3,
            fileName: 'Berita_Acara_Lintasarta.pdf',
            uploadedDate: '2025-12-22 16:45',
            fileSize: '3.2 MB',
            status: 'processing',
        },
        {
            id: 4,
            fileName: 'SPK_Smartfren_Q4.pdf',
            uploadedDate: '2025-12-22 14:20',
            fileSize: '1.5 MB',
            status: 'failed',
        },
        {
            id: 5,
            fileName: 'Dokumentasi_Site_Survey.pdf',
            uploadedDate: '2025-12-22 11:00',
            fileSize: '4.1 MB',
            status: 'processing',
        },
    ];

    const chartData = uploadTrend.length > 0 ? uploadTrend : defaultUploadTrend;
    const documents = recentDocuments.length > 0 ? recentDocuments : defaultRecentDocuments;

    // Data Dummy Users
    const dummyUsers: User[] = [
        {
            id: 1,
            name: 'Administrator',
            email: 'admin@email.com',
            role: 'admin',
            avatar: undefined,
            email_verified_at: '2024-01-15T10:30:00.000000Z',
            created_at: '2024-01-15T10:30:00.000000Z',
            updated_at: '2024-12-22T08:15:00.000000Z',
        },
        {
            id: 2,
            name: 'John Doe',
            email: 'john@email.com',
            role: 'user',
            avatar: undefined,
            email_verified_at: '2024-03-20T14:20:00.000000Z',
            created_at: '2024-03-20T14:20:00.000000Z',
            updated_at: '2024-12-20T09:30:00.000000Z',
        },
        {
            id: 3,
            name: 'Jane Smith',
            email: 'jane@email.com',
            role: 'user',
            avatar: undefined,
            email_verified_at: '2024-05-10T09:15:00.000000Z',
            created_at: '2024-05-10T09:15:00.000000Z',
            updated_at: '2024-12-21T11:45:00.000000Z',
        },
        {
            id: 4,
            name: 'Bob Manager',
            email: 'bob@email.com',
            role: 'manager',
            avatar: undefined,
            email_verified_at: '2024-02-28T16:45:00.000000Z',
            created_at: '2024-02-28T16:45:00.000000Z',
            updated_at: '2024-12-19T13:20:00.000000Z',
        },
        {
            id: 5,
            name: 'Alice Cooper',
            email: 'alice@email.com',
            role: 'user',
            avatar: undefined,
            email_verified_at: null,
            created_at: '2024-12-01T08:00:00.000000Z',
            updated_at: '2024-12-01T08:00:00.000000Z',
        },
    ];

    const [users, setUsers] = useState<User[]>(dummyUsers);

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const getRoleBadgeVariant = (role: string) => {
        switch (role) {
            case 'admin':
                return 'destructive';
            case 'manager':
                return 'default';
            default:
                return 'secondary';
        }
    };

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(date);
    };

    const getStatusBadge = (status?: string) => {
        if (!status) return null;

        switch (status) {
            case 'completed':
                return <span className="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400">✅ Selesai</span>;
            case 'processing':
                return <span className="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400 animate-pulse">🔄 Proses</span>;
            case 'failed':
                return <span className="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400">❌ Gagal</span>;
            case 'uploaded':
                return <span className="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">📤 Upload</span>;
            default:
                return null;
        }
    }

    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {/* Verified Users */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Verified</CardTitle>
                            <CheckCircle2 className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{countUsersVerified}</div>
                            <p className="text-xs text-muted-foreground">Users has been verified</p>
                        </CardContent>
                    </Card>

                    {/* Total Jenis SPK */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Jenis SPK</CardTitle>
                            <FileCheck className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{countSPKTypes || 8}</div>
                            <p className="text-xs text-muted-foreground">Different SPK types</p>
                        </CardContent>
                    </Card>

                    {/* Total Form Checklist */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Form Checklist</CardTitle>
                            <ClipboardCheck className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{countFormChecklist || 12}</div>
                            <p className="text-xs text-muted-foreground">Total form templates</p>
                        </CardContent>
                    </Card>
                </div>

                {/* Upload Trend Chart */}
                <Card>
                    <CardHeader>
                        <CardTitle>Upload Trend</CardTitle>
                        <CardDescription>Daily document uploads for the last 7 days</CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6">
                        <ResponsiveContainer width="100%" height={300}>
                            <AreaChart data={chartData}>
                                <defs>
                                    <linearGradient id="colorUploads" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#ffffff" stopOpacity={0.3} />
                                        <stop offset="95%" stopColor="#ffffff" stopOpacity={0} />
                                    </linearGradient>
                                </defs>
                                <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                                <XAxis dataKey="date" className="text-xs" tick={{ fill: '#ffffff' }} height={60} dy={10} />
                                <YAxis className="text-xs" tick={{ fill: '#ffffff' }} width={60} dx={-10} />
                                <Tooltip
                                    contentStyle={{
                                        backgroundColor: 'hsl(var(--popover))',
                                        border: '1px solid hsl(var(--border))',
                                        borderRadius: '8px',
                                    }}
                                    labelStyle={{ color: 'hsl(var(--popover-foreground))' }}
                                />
                                <Area type="monotone" dataKey="uploads" stroke="#ffffff" fillOpacity={1} fill="url(#colorUploads)" />
                            </AreaChart>
                        </ResponsiveContainer>
                    </CardContent>
                </Card>

                {/* Recent Documents Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Documents</CardTitle>
                        <CardDescription>Latest uploaded PDF documents</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-[50px]">ID</TableHead>
                                        <TableHead>File Name</TableHead>
                                        <TableHead>Uploaded Date</TableHead>
                                        <TableHead>File Size</TableHead>
                                        <TableHead>Document Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {documents
                                    .map((doc) => (
                                        <TableRow key={doc.id}>
                                            <TableCell className="font-medium">{doc.id}</TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <File className="h-4 w-4 text-muted-foreground" />
                                                    <span className="font-medium">{doc.fileName}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                    <Calendar className="h-4 w-4" />
                                                    {formatDate(doc.uploadedDate)}
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-sm text-muted-foreground">{doc.fileSize}</TableCell>
                                            <TableCell>{getStatusBadge(doc.status)}</TableCell>
                                        </TableRow>
                                        
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>

                {/* Unverified User Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Unverified User</CardTitle>
                        <CardDescription>Pengguna yang belum terverifikasi</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-[50px]">ID</TableHead>
                                        <TableHead>User</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Role</TableHead>
                                        <TableHead className="w-60">Joined</TableHead>
                                        <TableHead className="w-45">Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {users
                                        .filter((user) => user.email_verified_at === null)
                                        .map((user) => (
                                            <TableRow key={user.id}>
                                                <TableCell className="font-medium">{user.id}</TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-3">
                                                        <Avatar className="h-10 w-10">
                                                            <AvatarImage src={user.avatar} alt={user.name} />
                                                            <AvatarFallback className="bg-primary/10 text-primary">
                                                                {getInitials(user.name)}
                                                            </AvatarFallback>
                                                        </Avatar>
                                                        <div>
                                                            <div className="font-medium">{user.name}</div>
                                                        </div>
                                                    </div>
                                                </TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-2">
                                                        <Mail className="h-4 w-4 text-muted-foreground" />
                                                        <span className="text-sm">{user.email}</span>
                                                    </div>
                                                </TableCell>
                                                <TableCell>
                                                    <Badge variant={getRoleBadgeVariant(user.role)} className="capitalize">
                                                        <Shield className="mr-1 h-3 w-3" />
                                                        {user.role}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                        <Calendar className="h-4 w-4" />
                                                        {formatDate(user.created_at)}
                                                    </div>
                                                </TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-2">
                                                        <Badge variant="outline" className="border-yellow-500 text-yellow-600">
                                                            Unverified
                                                        </Badge>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
