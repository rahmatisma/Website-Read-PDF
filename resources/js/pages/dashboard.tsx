import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import axios from 'axios';
import { AlertCircle, Calendar, ChevronRight, ClipboardCheck, File, FileCheck, Mail, Shield } from 'lucide-react';
import { useEffect, useState } from 'react';
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
    role: 'admin' | 'engineer' | 'nms';
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface UploadTrend {
    date: string;
    spk: number;
    checklist: number;
}

interface DashboardProps {
    [key: string]: any;
    countUsersUnverified: number;
    countSPKTypes: number;
    countFormChecklist: number;
    countFormPmPopGlobal: number;
    uploadTrend: Array<{
        date: string;
        spk: number;
        checklist: number;
        pmPop: number;
    }>;
    recentDocuments: any[];
    unverifiedUsers: any[];
}

export default function Dashboard() {
    const initialProps = usePage<DashboardProps>().props;

    // ✅ State untuk semua data real-time
    const [countUsersUnverified, setCountUsersUnverified] = useState(initialProps.countUsersUnverified);
    const [countSPKTypes, setCountSPKTypes] = useState(initialProps.countSPKTypes);
    const [countFormChecklist, setCountFormChecklist] = useState(initialProps.countFormChecklist);
    const [countFormPmPop, setCountFormPmPop] = useState(initialProps.countFormPmPop);
    const [countFormPmPopGlobal, setCountFormPmPopGlobal] = useState(initialProps.countFormPmPopGlobal);
    const [uploadTrend, setUploadTrend] = useState(initialProps.uploadTrend);
    const [localDocuments, setLocalDocuments] = useState<Document[]>(initialProps.recentDocuments);
    const [unverifiedUsers, setUnverifiedUsers] = useState(initialProps.unverifiedUsers);
    const [isPolling, setIsPolling] = useState(false);

    // ✅ Real-time polling untuk SEMUA data dashboard
    useEffect(() => {
        console.log('🚀 Dashboard Real-time Started');
        setIsPolling(true);

        // Polling setiap 5 detik untuk update semua data
        const interval = setInterval(async () => {
            try {
                console.log('📡 Fetching dashboard stats...');

                const response = await axios.get('/api/dashboard/stats');
                const data = response.data;

                console.log('✅ Dashboard stats received:', data);

                // Update semua state
                setCountUsersUnverified(data.countUsersUnverified);
                setCountSPKTypes(data.countSPKTypes);
                setCountFormChecklist(data.countFormChecklist);
                setCountFormPmPopGlobal(data.countFormPmPopGlobal);
                setUploadTrend(data.uploadTrend);
                setLocalDocuments(data.recentDocuments);
                setUnverifiedUsers(data.unverifiedUsers);
            } catch (error) {
                console.error('❌ Dashboard polling error:', error);
            }
        }, 5000); // Poll setiap 5 detik

        return () => {
            console.log('🛑 Stopping dashboard polling');
            clearInterval(interval);
            setIsPolling(false);
        };
    }, []);

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
            case 'engineer':
                return 'default';
            case 'nms':
                return 'secondary';
            default:
                return 'outline';
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
                return <span className="rounded-full bg-green-500/20 px-2 py-1 text-xs text-green-400">✅ Selesai</span>;
            case 'processing':
                return <span className="animate-pulse rounded-full bg-yellow-500/20 px-2 py-1 text-xs text-yellow-400">🔄 Proses</span>;
            case 'failed':
                return <span className="rounded-full bg-red-500/20 px-2 py-1 text-xs text-red-400">❌ Gagal</span>;
            case 'uploaded':
                return <span className="rounded-full bg-blue-500/20 px-2 py-1 text-xs text-blue-400">📤 Upload</span>;
            default:
                return null;
        }
    };

    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* ✅ Real-time Indicator */}
                {isPolling && (
                    <div className="rounded-lg border border-blue-500/30 bg-blue-500/10 px-4 py-3">
                        <div className="flex items-center gap-3">
                            <div className="h-2 w-2 animate-pulse rounded-full bg-blue-400"></div>
                            <span className="text-sm text-blue-400">🔄 Real-time monitoring active - Dashboard updates every 5 seconds</span>
                        </div>
                    </div>
                )}

                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {/* ✅ CHANGED: Unverified Users (bukan Verified) */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Unverified Users</CardTitle>
                            <AlertCircle className="h-4 w-4 text-yellow-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{countUsersUnverified}</div>
                            <p className="text-xs text-muted-foreground">Users pending verification</p>
                        </CardContent>
                    </Card>

                    {/* Total Jenis SPK */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Jenis SPK</CardTitle>
                            <FileCheck className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{countSPKTypes}</div>
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
                            <div className="text-2xl font-bold">{countFormChecklist}</div>
                            <p className="text-xs text-muted-foreground">Total form templates</p>
                        </CardContent>
                    </Card>

                    {/* Total Form PM POP - BARU */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Form PM POP</CardTitle>
                            <ClipboardCheck className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{countFormPmPopGlobal}</div>
                            <p className="text-xs text-muted-foreground">Total PM POP forms</p>
                        </CardContent>
                    </Card>
                </div>
                

                {/* Upload Trend Chart */}
                <Card>
                    <CardHeader>
                        <CardTitle>Upload Trend</CardTitle>
                        <CardDescription>Daily document uploads for the last 7 days (auto-updates)</CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6">
                        <ResponsiveContainer width="100%" height={300}>
                            <AreaChart data={uploadTrend}>
                                <defs>
                                    <linearGradient id="colorSPK" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#a855f7" stopOpacity={0.5} />
                                        <stop offset="95%" stopColor="#a855f7" stopOpacity={0.1} />
                                    </linearGradient>
                                    <linearGradient id="colorChecklist" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#10b981" stopOpacity={0.5} />
                                        <stop offset="95%" stopColor="#10b981" stopOpacity={0.1} />
                                    </linearGradient>
                                    <linearGradient id="colorPmPop" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#f59e0b" stopOpacity={0.5} />
                                        <stop offset="95%" stopColor="#f59e0b" stopOpacity={0.1} />
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
                                    formatter={(value, name) => {
                                        const label = name === 'spk' ? 'SPK Documents' : 'Form Checklist';
                                        return [value ?? 0, label];
                                    }}
                                />
                                <Area
                                    type="monotone"
                                    dataKey="spk"
                                    stroke="#a855f7"
                                    strokeWidth={2}
                                    fillOpacity={1}
                                    fill="url(#colorSPK)"
                                    name="spk"
                                />
                                <Area
                                    type="monotone"
                                    dataKey="checklist"
                                    stroke="#10b981"
                                    strokeWidth={2}
                                    fillOpacity={1}
                                    fill="url(#colorChecklist)"
                                    name="checklist"
                                />
                                <Area
                                    type="monotone"
                                    dataKey="pmPop"
                                    stroke="#f59e0b"
                                    strokeWidth={2}
                                    fillOpacity={1}
                                    fill="url(#colorPmPop)"
                                    name="pmPop"
                                />
                            </AreaChart>
                        </ResponsiveContainer>

                        {/* Custom Legend */}
                        <div className="mt-4 flex items-center justify-center gap-6">
                            <div className="flex items-center gap-2">
                                <div className="h-3 w-3 rounded-full bg-purple-500"></div>
                                <span className="text-sm text-muted-foreground">SPK Documents</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <div className="h-3 w-3 rounded-full bg-emerald-500"></div>
                                <span className="text-sm text-muted-foreground">Form Checklist</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <div className="h-3 w-3 rounded-full bg-orange-500"></div>
                                <span className="text-sm text-muted-foreground">Form PM POP</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Recent Documents Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Documents</CardTitle>
                        <CardDescription>5 Latest uploaded documents (auto-deleted after 1 day, auto-updates)</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {localDocuments.length > 0 ? (
                            <>
                                <div className="rounded-lg border">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead className="w-[50px]">ID</TableHead>
                                                <TableHead>File Name</TableHead>
                                                <TableHead>Uploaded Date</TableHead>
                                                <TableHead>File Size</TableHead>
                                                <TableHead>Status</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {localDocuments.map((doc) => (
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
                                {/* View More Button */}
                                <div className="mt-4 flex justify-center">
                                    <button
                                        onClick={() => router.visit('/documents')}
                                        className="flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                    >
                                        <File className="h-4 w-4" />
                                        View More Documents
                                    </button>
                                </div>
                            </>
                        ) : (
                            <div className="py-8 text-center text-muted-foreground">
                                <File className="mx-auto h-12 w-12 opacity-20" />
                                <p className="mt-2">No recent documents</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Unverified Users Table */}
                {unverifiedUsers.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Unverified Users</CardTitle>
                            <CardDescription>Latest users pending admin verification (auto-updates)</CardDescription>
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
                                        {unverifiedUsers.map((user) => (
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
                                                    <Badge variant="outline" className="border-yellow-500 text-yellow-600">
                                                        Pending Approval
                                                    </Badge>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>
                            {/* View More Button */}
                            <div className="mt-4 flex justify-center">
                                <button
                                    onClick={() => router.visit('/users')}
                                    className="flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Shield className="h-4 w-4" />
                                    Manage All Users
                                    <ChevronRight className="h-4 w-4" />
                                </button>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
