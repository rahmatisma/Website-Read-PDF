import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Battery, TrendingDown, AlertTriangle, CheckCircle, ArrowRight } from 'lucide-react';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

// ─── Interfaces ──────────────────────────────────────────────
interface BatteryData {
    location: string;
    bank: string;
    batteryNo: number;
    voltage: number;
    soh: number;
    status: 'sehat' | 'warning' | 'kritis';
    trend: 'up' | 'down' | 'stable';
}

interface BatterySummary {
    sehat: number;
    warning: number;
    kritis: number;
}

// ─── Component ───────────────────────────────────────────────
export default function BatteryMonitoring() {
    const [summary, setSummary] = useState<BatterySummary>({ sehat: 0, warning: 0, kritis: 0 });
    const [criticalBatteries, setCriticalBatteries] = useState<BatteryData[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    // ── Fetch dari backend ─────────────────────────────────
    const fetchData = async () => {
        try {
            setLoading(true);
            setError(null);

            const response = await axios.get('/api/battery/dashboard-summary');
            const data = response.data;

            setSummary(data.summary);
            setCriticalBatteries(data.criticalBatteries);
        } catch (err) {
            console.error('Battery dashboard fetch error:', err);
            setError('Gagal mengambil data battery');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, []);

    // ── Badge helpers ─────────────────────────────────────
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'sehat':
                return (
                    <Badge className="bg-green-500/20 text-green-400 hover:bg-green-500/30">
                        <CheckCircle className="mr-1 h-3 w-3" />
                        Sehat
                    </Badge>
                );
            case 'warning':
                return (
                    <Badge className="bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30">
                        <AlertTriangle className="mr-1 h-3 w-3" />
                        Warning
                    </Badge>
                );
            case 'kritis':
                return (
                    <Badge className="bg-red-500/20 text-red-400 hover:bg-red-500/30">
                        <AlertTriangle className="mr-1 h-3 w-3" />
                        Kritis
                    </Badge>
                );
            default:
                return null;
        }
    };

    const getTrendIcon = (trend: string) => {
        switch (trend) {
            case 'down':
                return <TrendingDown className="h-4 w-4 text-red-400" />;
            case 'up':
                return <TrendingDown className="h-4 w-4 rotate-180 text-green-400" />;
            case 'stable':
                return <div className="h-4 w-4 text-gray-400">—</div>;
            default:
                return null;
        }
    };

    // ── Render ─────────────────────────────────────────────
    return (
        <Card className="col-span-full">
            <CardHeader>
                <div className="flex items-center justify-between">
                    <div>
                        <CardTitle className="flex items-center gap-2">
                            <Battery className="h-5 w-5 text-blue-500" />
                            Battery Health Monitoring
                        </CardTitle>
                        <CardDescription>Real-time battery condition across all locations</CardDescription>
                    </div>
                    <button
                        onClick={() => router.visit('/battery-dashboard')}
                        className="flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                    >
                        <Battery className="h-4 w-4" />
                        View Full Dashboard
                        <ArrowRight className="h-4 w-4" />
                    </button>
                </div>
            </CardHeader>

            <CardContent>
                {/* ── Error state ── */}
                {error && (
                    <div className="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        {error}
                        <button onClick={fetchData} className="ml-2 underline">Coba lagi</button>
                    </div>
                )}

                {/* ── Summary Cards ── */}
                <div className="mb-6 grid gap-4 md:grid-cols-3">
                    {/* Sehat */}
                    <Card className="border-green-500/30 bg-green-500/5">
                        <CardContent className="pt-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Battery Sehat</p>
                                    <p className="mt-2 text-3xl font-bold text-green-400">
                                        {loading ? '...' : summary.sehat}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">V ≥12V & SOH ≥80%</p>
                                </div>
                                <div className="rounded-full bg-green-500/20 p-3">
                                    <CheckCircle className="h-6 w-6 text-green-400" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Warning */}
                    <Card className="border-yellow-500/30 bg-yellow-500/5">
                        <CardContent className="pt-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Perlu Monitoring</p>
                                    <p className="mt-2 text-3xl font-bold text-yellow-400">
                                        {loading ? '...' : summary.warning}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">Salah satu &lt; threshold</p>
                                </div>
                                <div className="rounded-full bg-yellow-500/20 p-3">
                                    <AlertTriangle className="h-6 w-6 text-yellow-400" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Kritis */}
                    <Card className="border-red-500/30 bg-red-500/5">
                        <CardContent className="pt-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Perlu Diganti</p>
                                    <p className="mt-2 text-3xl font-bold text-red-400">
                                        {loading ? '...' : summary.kritis}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">V &lt;12V & SOH &lt;80%</p>
                                </div>
                                <div className="rounded-full bg-red-500/20 p-3">
                                    <AlertTriangle className="h-6 w-6 text-red-400" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* ── Critical Batteries Table ── */}
                <div>
                    <h3 className="mb-4 text-lg font-semibold">⚠️ 5 Battery Terburuk (Perlu Perhatian)</h3>

                    {loading ? (
                        // Skeleton loading
                        <div className="space-y-3">
                            {[...Array(5)].map((_, i) => (
                                <div key={i} className="h-12 animate-pulse rounded-lg bg-muted" />
                            ))}
                        </div>
                    ) : criticalBatteries.length === 0 ? (
                        // Kosong
                        <div className="rounded-lg border border-green-500/30 bg-green-500/5 px-4 py-8 text-center">
                            <CheckCircle className="mx-auto h-10 w-10 text-green-400" />
                            <p className="mt-2 text-sm text-green-400">Semua battery dalam kondisi baik</p>
                        </div>
                    ) : (
                        // Tabel data
                        <div className="rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Location</TableHead>
                                        <TableHead>Bank</TableHead>
                                        <TableHead>Battery #</TableHead>
                                        <TableHead>Voltage</TableHead>
                                        <TableHead>SOH</TableHead>
                                        <TableHead>Trend</TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {criticalBatteries.map((battery, index) => (
                                        <TableRow key={index}>
                                            <TableCell className="font-medium">{battery.location}</TableCell>
                                            <TableCell>
                                                <Badge variant="outline" className="font-mono">
                                                    {battery.bank}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Battery className="h-4 w-4 text-muted-foreground" />
                                                    <span className="font-medium">#{battery.batteryNo}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <span className={battery.voltage < 12 ? 'font-semibold text-red-400' : ''}>
                                                    {battery.voltage.toFixed(1)}V
                                                </span>
                                            </TableCell>
                                            <TableCell>
                                                <span className={battery.soh < 80 ? 'font-semibold text-yellow-400' : ''}>
                                                    {battery.soh}%
                                                </span>
                                            </TableCell>
                                            <TableCell>{getTrendIcon(battery.trend)}</TableCell>
                                            <TableCell>{getStatusBadge(battery.status)}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    )}
                </div>

                {/* ── Footer info ── */}
                <div className="mt-4 flex items-center gap-2 rounded-lg border border-blue-500/30 bg-blue-500/5 px-4 py-3">
                    <div className="h-2 w-2 animate-pulse rounded-full bg-blue-400"></div>
                    <span className="text-sm text-blue-400">
                        💡 Data battery diambil dari latest inspection di setiap lokasi
                    </span>
                </div>
            </CardContent>
        </Card>
    );
}