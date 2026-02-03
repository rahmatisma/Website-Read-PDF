import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Battery, AlertTriangle, CheckCircle } from 'lucide-react';
import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Line } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

// Register ChartJS components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

// ─── Interfaces ──────────────────────────────────────────────
interface LocationItem {
    id: number;
    name: string;
}

interface BankItem {
    bank_name: string;
    cells: number[];
}

interface TrendPoint {
    date: string;
    voltage: number;
    soh: number;
}

interface BatterySummary {
    sehat: number;
    warning: number;
    kritis: number;
}

// ─── Component ───────────────────────────────────────────────
export default function BatteryDashboard() {
    // ── Filter state ───────────────────────────────────────
    const [locations, setLocations]           = useState<LocationItem[]>([]);
    const [banks, setBanks]                   = useState<BankItem[]>([]);
    const [selectedLocation, setSelectedLocation] = useState<number | null>(null);
    const [selectedBank, setSelectedBank]         = useState<string>('');
    const [selectedBattery, setSelectedBattery]   = useState<number | null>(null);

    // ── Data state ─────────────────────────────────────────
    const [summary, setSummary]               = useState<BatterySummary>({ sehat: 0, warning: 0, kritis: 0 });
    const [trendData, setTrendData]           = useState<TrendPoint[]>([]);

    // ── Loading flags ──────────────────────────────────────
    const [loadingLocations, setLoadingLocations] = useState(true);
    const [loadingSummary, setLoadingSummary]     = useState(true);
    const [loadingBanks, setLoadingBanks]         = useState(false);
    const [loadingTrend, setLoadingTrend]         = useState(false);

    // ── Error ──────────────────────────────────────────────
    const [errorMsg, setErrorMsg] = useState<string | null>(null);

    // ── Derived: available cells dari bank yang dipilih ───
    const availableCells: number[] =
        banks.find((b) => b.bank_name === selectedBank)?.cells ?? [];

    // ════════════════════════════════════════════════════════
    // FETCH: locations (sekali, saat mount)
    // ════════════════════════════════════════════════════════
    useEffect(() => {
        const fetch = async () => {
            try {
                setLoadingLocations(true);
                const res = await axios.get('/api/battery/locations');
                setLocations(res.data.locations);
            } catch {
                setErrorMsg('Gagal mengambil data lokasi');
            } finally {
                setLoadingLocations(false);
            }
        };
        fetch();
    }, []);

    // ════════════════════════════════════════════════════════
    // FETCH: summary (sekali, saat mount)
    // ════════════════════════════════════════════════════════
    useEffect(() => {
        const fetch = async () => {
            try {
                setLoadingSummary(true);
                const res = await axios.get('/api/battery/dashboard-summary');
                setSummary(res.data.summary);
            } catch {
                setErrorMsg('Gagal mengambil summary');
            } finally {
                setLoadingSummary(false);
            }
        };
        fetch();
    }, []);

    // ════════════════════════════════════════════════════════
    // FETCH: banks & cells — dipanggil setiap kali location berubah
    // ════════════════════════════════════════════════════════
    const fetchBanksAndCells = useCallback(async () => {
        if (!selectedLocation) {
            setBanks([]);
            return;
        }
        try {
            setLoadingBanks(true);
            const res = await axios.get(`/api/battery/locations/${selectedLocation}/banks-and-cells`);
            setBanks(res.data.banks);
        } catch {
            setBanks([]);
            setErrorMsg('Gagal mengambil data bank');
        } finally {
            setLoadingBanks(false);
        }
    }, [selectedLocation]);

    useEffect(() => {
        fetchBanksAndCells();
    }, [fetchBanksAndCells]);

    // ════════════════════════════════════════════════════════
    // FETCH: trend — dipanggil saat location + bank + battery lengkap
    // ════════════════════════════════════════════════════════
    useEffect(() => {
        if (!selectedLocation || !selectedBank || !selectedBattery) {
            setTrendData([]);
            return;
        }

        const fetch = async () => {
            try {
                setLoadingTrend(true);
                const res = await axios.get('/api/battery/trend', {
                    params: {
                        location_id: selectedLocation,
                        bank:        selectedBank,
                        battery_no:  selectedBattery,
                    },
                });
                setTrendData(res.data.trend);
            } catch {
                setTrendData([]);
                setErrorMsg('Gagal mengambil trend data');
            } finally {
                setLoadingTrend(false);
            }
        };
        fetch();
    }, [selectedLocation, selectedBank, selectedBattery]);

    // ════════════════════════════════════════════════════════
    // HANDLER: reset downstream filter saat upstream berubah
    // ════════════════════════════════════════════════════════
    const handleLocationChange = (val: number | null) => {
        setSelectedLocation(val);
        setSelectedBank('');
        setSelectedBattery(null);
        setTrendData([]);
    };

    const handleBankChange = (val: string) => {
        setSelectedBank(val);
        setSelectedBattery(null);
        setTrendData([]);
    };

    const handleBatteryChange = (val: number | null) => {
        setSelectedBattery(val);
    };

    // ════════════════════════════════════════════════════════
    // STATUS HELPERS
    // ════════════════════════════════════════════════════════
    const getBatteryStatus = (voltage: number, soh: number) => {
        if (voltage < 12 && soh < 80) return 'kritis';
        if (voltage < 12 || soh < 80)  return 'warning';
        return 'sehat';
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'sehat':
                return (
                    <Badge className="bg-green-500/20 text-green-400 hover:bg-green-500/30">
                        <CheckCircle className="mr-1 h-3 w-3" /> SEHAT
                    </Badge>
                );
            case 'warning':
                return (
                    <Badge className="bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30">
                        <AlertTriangle className="mr-1 h-3 w-3" /> PERLU MONITORING
                    </Badge>
                );
            case 'kritis':
                return (
                    <Badge className="bg-red-500/20 text-red-400 hover:bg-red-500/30">
                        <AlertTriangle className="mr-1 h-3 w-3" /> PERLU DIGANTI
                    </Badge>
                );
            default:
                return null;
        }
    };

    // ════════════════════════════════════════════════════════
    // THRESHOLD CONSTANTS
    // ════════════════════════════════════════════════════════
    const VOLTAGE_THRESHOLD = 12;
    const SOH_THRESHOLD = 80;

    // ════════════════════════════════════════════════════════
    // CHART CONFIG WITH THRESHOLDS
    // ════════════════════════════════════════════════════════
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index' as const, intersect: false },
        plugins: {
            legend: {
                display: true,
                position: 'top' as const,
                labels: { 
                    color: '#ffffff', 
                    font: { size: 14, weight: 'bold' as const }, 
                    padding: 15, 
                    usePointStyle: true,
                    // Filter out threshold lines from legend
                    filter: function(legendItem: any) {
                        return !legendItem.text.includes('Threshold');
                    }
                },
            },
            tooltip: { 
                backgroundColor: 'rgba(0,0,0,0.9)', 
                titleFont: { size: 14, weight: 'bold' as const }, 
                bodyFont: { size: 13 }, 
                padding: 12,
                borderColor: 'rgba(255,255,255,0.3)',
                borderWidth: 1,
                callbacks: {
                    label: function(context: any) {
                        // Skip threshold lines in tooltip
                        if (context.dataset.label?.includes('Threshold')) {
                            return null;
                        }

                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        const value = context.parsed.y;
                        
                        if (context.dataset.label === 'Voltage (V)') {
                            const diff = value - VOLTAGE_THRESHOLD;
                            const sign = diff >= 0 ? '+' : '';
                            const status = diff >= 0 ? '✅' : '⚠️';
                            label += value.toFixed(2) + 'V ' + status + ' (' + sign + diff.toFixed(2) + 'V dari min)';
                        } else if (context.dataset.label === 'SOH (%)') {
                            const diff = value - SOH_THRESHOLD;
                            const sign = diff >= 0 ? '+' : '';
                            const status = diff >= 0 ? '✅' : '⚠️';
                            label += value.toFixed(1) + '% ' + status + ' (' + sign + diff.toFixed(1) + '% dari min)';
                        } else {
                            label += value;
                        }
                        
                        return label;
                    },
                    // Remove null entries
                    afterLabel: function() {
                        return '';
                    }
                }
            },
        },
        scales: {
            yVoltage: {
                type: 'linear' as const,
                position: 'left' as const,
                min: 11, 
                max: 14,
                title: { 
                    display: true, 
                    text: 'Voltage (V)', 
                    color: '#4299e1', 
                    font: { size: 14, weight: 'bold' as const } 
                },
                ticks: { 
                    color: '#ffffff', 
                    callback: (value: any) => value.toFixed(1) + 'V',
                },
                grid: { 
                    color: 'rgba(255,255,255,0.05)',
                },
            },
            ySOH: {
                type: 'linear' as const,
                position: 'right' as const,
                min: 0, 
                max: 120,
                title: { 
                    display: true, 
                    text: 'SOH (%)', 
                    color: '#48bb78', 
                    font: { size: 14, weight: 'bold' as const } 
                },
                ticks: { 
                    color: '#ffffff', 
                    callback: (value: any) => value + '%',
                },
                grid: { 
                    drawOnChartArea: false 
                },
            },
            x: {
                ticks: { 
                    color: '#ffffff',
                    font: { size: 11 },
                    maxRotation: 45,
                    minRotation: 0
                },
                grid: { 
                    color: 'rgba(255,255,255,0.05)' 
                },
            },
        },
    };

    // ════════════════════════════════════════════════════════
    // CHART DATA WITH THRESHOLD LINES
    // ════════════════════════════════════════════════════════
    const getChartData = () => {
        if (trendData.length === 0) return null;

        // Create threshold arrays (same length as data)
        const voltageThresholdData = new Array(trendData.length).fill(VOLTAGE_THRESHOLD);
        const sohThresholdData = new Array(trendData.length).fill(SOH_THRESHOLD);

        return {
            labels: trendData.map((d) => d.date),
            datasets: [
                // ── Voltage Line (Main) ──
                {
                    label: 'Voltage (V)',
                    data: trendData.map((d) => d.voltage),
                    borderColor: '#4299e1',
                    backgroundColor: 'rgba(66,153,225,0.1)',
                    borderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#4299e1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#4299e1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                    yAxisID: 'yVoltage',
                    tension: 0.3,
                    fill: true,
                    order: 2,
                },
                // ── Voltage Threshold Line ──
                {
                    label: 'Voltage Threshold',
                    data: voltageThresholdData,
                    borderColor: '#ef4444',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [10, 5],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    yAxisID: 'yVoltage',
                    tension: 0,
                    fill: false,
                    order: 1,
                },
                // ── SOH Line (Main) ──
                {
                    label: 'SOH (%)',
                    data: trendData.map((d) => d.soh),
                    borderColor: '#48bb78',
                    backgroundColor: 'rgba(72,187,120,0.1)',
                    borderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#48bb78',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#48bb78',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                    yAxisID: 'ySOH',
                    tension: 0.3,
                    fill: true,
                    order: 2,
                },
                // ── SOH Threshold Line ──
                {
                    label: 'SOH Threshold',
                    data: sohThresholdData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [10, 5],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    yAxisID: 'ySOH',
                    tension: 0,
                    fill: false,
                    order: 1,
                },
            ],
        };
    };

    // ════════════════════════════════════════════════════════
    // RENDER HELPERS
    // ════════════════════════════════════════════════════════
    const getChartTitle = () => {
        if (!selectedLocation || !selectedBank || !selectedBattery) return 'Trend Voltage & SOH per Battery';
        const locName = locations.find((l) => l.id === selectedLocation)?.name ?? '';
        return `${locName} — Bank ${selectedBank} — Battery #${selectedBattery}`;
    };

    // ════════════════════════════════════════════════════════
    // RENDER
    // ════════════════════════════════════════════════════════
    return (
        <AppLayout>
            <Head title="Battery Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">

                {/* ── Header ── */}
                <div>
                    <h1 className="text-3xl font-bold">🔋 Battery Monitoring Dashboard</h1>
                    <p className="text-muted-foreground">PT. Aplikanusa Lintasarta — Preventive Maintenance System</p>
                </div>

                {/* ── Global error ── */}
                {errorMsg && (
                    <div className="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        {errorMsg}
                        <button onClick={() => setErrorMsg(null)} className="ml-3 underline">Tutup</button>
                    </div>
                )}

                {/* ── Summary Cards ── */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="border-green-500/30 bg-green-500/5">
                        <CardContent className="pt-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Battery Sehat</p>
                                    <p className="mt-2 text-3xl font-bold text-green-400">
                                        {loadingSummary ? '...' : summary.sehat}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">V ≥12V & SOH ≥80%</p>
                                </div>
                                <CheckCircle className="h-12 w-12 text-green-400/20" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border-yellow-500/30 bg-yellow-500/5">
                        <CardContent className="pt-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Perlu Monitoring</p>
                                    <p className="mt-2 text-3xl font-bold text-yellow-400">
                                        {loadingSummary ? '...' : summary.warning}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">Salah satu &lt; threshold</p>
                                </div>
                                <AlertTriangle className="h-12 w-12 text-yellow-400/20" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border-red-500/30 bg-red-500/5">
                        <CardContent className="pt-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Perlu Diganti</p>
                                    <p className="mt-2 text-3xl font-bold text-red-400">
                                        {loadingSummary ? '...' : summary.kritis}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">V &lt;12V & SOH &lt;80%</p>
                                </div>
                                <AlertTriangle className="h-12 w-12 text-red-400/20" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* ── Filter Card ── */}
                <Card>
                    <CardHeader>
                        <CardTitle>Pilih Battery untuk Analisis</CardTitle>
                        <CardDescription>Filter berdasarkan lokasi, bank, dan nomor battery yang tersedia di database</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 md:grid-cols-3">

                            {/* Lokasi */}
                            <div>
                                <label className="mb-2 block text-sm font-medium">Lokasi</label>
                                <select
                                    value={selectedLocation ?? ''}
                                    onChange={(e) => handleLocationChange(e.target.value ? Number(e.target.value) : null)}
                                    disabled={loadingLocations}
                                    className="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">
                                        {loadingLocations ? 'Loading...' : locations.length === 0 ? 'Tidak ada lokasi' : '-- Pilih Lokasi --'}
                                    </option>
                                    {locations.map((loc) => (
                                        <option key={loc.id} value={loc.id}>{loc.name}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Bank */}
                            <div>
                                <label className="mb-2 block text-sm font-medium">Bank</label>
                                <select
                                    value={selectedBank}
                                    onChange={(e) => handleBankChange(e.target.value)}
                                    disabled={!selectedLocation || loadingBanks}
                                    className="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">
                                        {loadingBanks ? 'Loading...' : '-- Pilih Bank --'}
                                    </option>
                                    {banks.map((b) => (
                                        <option key={b.bank_name} value={b.bank_name}>{b.bank_name}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Battery # */}
                            <div>
                                <label className="mb-2 block text-sm font-medium">Battery</label>
                                <select
                                    value={selectedBattery ?? ''}
                                    onChange={(e) => handleBatteryChange(e.target.value ? Number(e.target.value) : null)}
                                    disabled={!selectedBank}
                                    className="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">-- Pilih Battery --</option>
                                    {availableCells.map((no) => (
                                        <option key={no} value={no}>Battery #{no}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* ── Chart ── */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div>
                                <CardTitle>{getChartTitle()}</CardTitle>
                                <CardDescription>
                                    Grafik perubahan voltage dan SOH dari waktu ke waktu dengan garis threshold
                                    <span className="ml-2 text-red-400">━━━</span> Voltage Min: {VOLTAGE_THRESHOLD}V
                                    <span className="ml-3 text-orange-400">━━━</span> SOH Min: {SOH_THRESHOLD}%
                                </CardDescription>
                            </div>
                            {trendData.length > 0 && (() => {
                                const last = trendData[trendData.length - 1];
                                return getStatusBadge(getBatteryStatus(last.voltage, last.soh));
                            })()}
                        </div>
                    </CardHeader>
                    <CardContent>
                        {loadingTrend ? (
                            <div className="flex h-[400px] items-center justify-center">
                                <div className="h-8 w-8 animate-spin rounded-full border-4 border-muted border-t-blue-500" />
                            </div>
                        ) : trendData.length > 0 ? (
                            <div style={{ height: '400px' }}>
                                <Line
                                    data={getChartData()!}
                                    options={chartOptions}
                                />
                            </div>
                        ) : (
                            <div className="flex h-[400px] items-center justify-center">
                                <div className="text-center">
                                    <Battery className="mx-auto h-16 w-16 text-muted-foreground/20" />
                                    <p className="mt-4 text-lg font-medium text-muted-foreground">
                                        Silakan pilih Lokasi, Bank, dan Battery
                                    </p>
                                    <p className="mt-2 text-sm text-muted-foreground">
                                        untuk melihat grafik trend voltage dan SOH
                                    </p>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* ── Info Box ── */}
                <Card className="border-blue-500/30 bg-blue-500/5">
                    <CardContent className="pt-6">
                        <div className="flex items-start gap-3">
                            <Battery className="h-5 w-5 text-blue-400" />
                            <div className="flex-1 text-sm text-blue-400">
                                <p className="mb-2 font-semibold">ℹ️ Informasi Penting:</p>
                                <ul className="list-disc list-inside space-y-1">
                                    <li><strong>Threshold Voltage:</strong> Minimum {VOLTAGE_THRESHOLD}V per battery (garis merah putus-putus)</li>
                                    <li><strong>Threshold SOH:</strong> Minimum {SOH_THRESHOLD}% (State of Health - garis kuning putus-putus)</li>
                                    <li><strong>Status Kritis:</strong> Voltage &lt;{VOLTAGE_THRESHOLD}V DAN SOH &lt;{SOH_THRESHOLD}% → Battery harus diganti segera</li>
                                    <li><strong>Status Warning:</strong> Salah satu dari Voltage atau SOH di bawah threshold → Perlu monitoring</li>
                                    <li><strong>Tooltip:</strong> Hover pada data point untuk melihat selisih dari nilai minimum</li>
                                </ul>
                            </div>
                        </div>
                    </CardContent>
                </Card>

            </div>
        </AppLayout>
    );
}