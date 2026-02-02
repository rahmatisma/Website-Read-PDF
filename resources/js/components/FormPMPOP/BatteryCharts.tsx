import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { LineChart, Line, BarChart, Bar, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, ReferenceLine } from 'recharts';
import { Battery, TrendingDown, TrendingUp, AlertTriangle } from 'lucide-react';

interface BatteryMeasurement {
    cellNumber: number;
    [key: string]: number | null; // Dynamic keys untuk setiap bank
}

interface BankSummary {
    bank_name: string;
    bank_type: string;
    battery_brand: string;
    battery_capacity: string;
    production_date: string | null;
    avg_voltage: number;
    min_voltage: number;
    max_voltage: number;
    avg_soh: number;
    min_soh: number;
    max_soh: number;
    cells_below_12v: number;
    cells_below_80_soh: number;
    total_cells: number;
}

interface BatteryChartData {
    voltage_chart: BatteryMeasurement[];
    soh_chart: BatteryMeasurement[];
    bank_summary: BankSummary[];
    bank_names: string[];
    metadata: {
        total_banks: number;
        total_cells: number;
        max_cell_number: number;
    };
}

interface BatteryChartsProps {
    data: BatteryChartData;
}

// Color palette untuk setiap bank (max 8 banks)
const BANK_COLORS = [
    '#3b82f6', // blue-500
    '#10b981', // green-500
    '#f59e0b', // amber-500
    '#ef4444', // red-500
    '#8b5cf6', // violet-500
    '#ec4899', // pink-500
    '#14b8a6', // teal-500
    '#f97316', // orange-500
];

export const BatteryCharts: React.FC<BatteryChartsProps> = ({ data }) => {
    const { voltage_chart, soh_chart, bank_summary, bank_names, metadata } = data;

    // Custom tooltip untuk Voltage Chart
    const VoltageTooltip = ({ active, payload, label }: any) => {
        if (active && payload && payload.length) {
            return (
                <div className="rounded-lg border bg-background p-3 shadow-lg">
                    <p className="mb-2 font-semibold">Cell {label}</p>
                    {payload.map((entry: any, index: number) => (
                        <p key={index} className="text-sm" style={{ color: entry.color }}>
                            {entry.name}: {entry.value ? `${entry.value.toFixed(2)}V` : 'N/A'}
                        </p>
                    ))}
                    <p className="mt-2 border-t pt-2 text-xs text-muted-foreground">
                        Standard: ≥ 12.0V
                    </p>
                </div>
            );
        }
        return null;
    };

    // Custom tooltip untuk SOH Chart
    const SohTooltip = ({ active, payload, label }: any) => {
        if (active && payload && payload.length) {
            return (
                <div className="rounded-lg border bg-background p-3 shadow-lg">
                    <p className="mb-2 font-semibold">Cell {label}</p>
                    {payload.map((entry: any, index: number) => (
                        <p key={index} className="text-sm" style={{ color: entry.color }}>
                            {entry.name}: {entry.value ? `${entry.value}%` : 'N/A'}
                        </p>
                    ))}
                    <p className="mt-2 border-t pt-2 text-xs text-muted-foreground">
                        Standard: ≥ 80%
                    </p>
                </div>
            );
        }
        return null;
    };

    // Get color untuk SOH bar based on value
    const getSohColor = (value: number | null): string => {
        if (!value) return '#6b7280'; // gray-500
        if (value >= 80) return '#10b981'; // green-500
        if (value >= 60) return '#f59e0b'; // amber-500
        return '#ef4444'; // red-500
    };

    return (
        <div className="space-y-6">
            {/* SUMMARY CARDS */}
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                {bank_summary.map((bank, index) => (
                    <Card key={index}>
                        <CardHeader className="pb-3">
                            <CardTitle className="flex items-center gap-2 text-base">
                                <Battery className="h-4 w-4" style={{ color: BANK_COLORS[index] }} />
                                {bank.bank_name}
                            </CardTitle>
                            <CardDescription className="text-xs">
                                {bank.battery_brand} • {bank.battery_capacity}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-2">
                            <div className="flex items-center justify-between text-sm">
                                <span className="text-muted-foreground">Avg Voltage:</span>
                                <Badge variant={bank.avg_voltage >= 12 ? 'default' : 'destructive'}>
                                    {bank.avg_voltage.toFixed(2)}V
                                </Badge>
                            </div>
                            <div className="flex items-center justify-between text-sm">
                                <span className="text-muted-foreground">Avg SOH:</span>
                                <Badge variant={bank.avg_soh >= 80 ? 'default' : 'destructive'}>
                                    {bank.avg_soh.toFixed(1)}%
                                </Badge>
                            </div>
                            {bank.cells_below_80_soh > 0 && (
                                <div className="flex items-center gap-1 text-xs text-amber-500">
                                    <AlertTriangle className="h-3 w-3" />
                                    {bank.cells_below_80_soh} cells below 80% SOH
                                </div>
                            )}
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* VOLTAGE LINE CHART */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <TrendingUp className="h-5 w-5 text-blue-500" />
                        Battery Voltage per Cell
                    </CardTitle>
                    <CardDescription>
                        Voltage measurements across all cells (Standard: ≥ 12.0V)
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <ResponsiveContainer width="100%" height={400}>
                        <LineChart data={voltage_chart}>
                            <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                            <XAxis 
                                dataKey="cellNumber" 
                                label={{ value: 'Cell Number', position: 'insideBottom', offset: -5 }}
                            />
                            <YAxis 
                                label={{ value: 'Voltage (V)', angle: -90, position: 'insideLeft' }}
                                domain={[11, 14]}
                            />
                            <Tooltip content={<VoltageTooltip />} />
                            <Legend />
                            
                            {/* Reference line untuk minimum standard */}
                            <ReferenceLine 
                                y={12} 
                                stroke="#ef4444" 
                                strokeDasharray="3 3" 
                                label={{ value: 'Min Standard (12V)', position: 'right' }}
                            />
                            
                            {/* Dynamic lines untuk setiap bank */}
                            {bank_names.map((bankName, index) => (
                                <Line
                                    key={bankName}
                                    type="monotone"
                                    dataKey={bankName}
                                    stroke={BANK_COLORS[index]}
                                    strokeWidth={2}
                                    dot={{ r: 3 }}
                                    activeDot={{ r: 5 }}
                                    connectNulls
                                />
                            ))}
                        </LineChart>
                    </ResponsiveContainer>
                </CardContent>
            </Card>

            {/* SOH BAR CHART */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Battery className="h-5 w-5 text-green-500" />
                        State of Health (SOH) Distribution
                    </CardTitle>
                    <CardDescription>
                        Battery health percentage per cell (Standard: ≥ 80%)
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <ResponsiveContainer width="100%" height={400}>
                        <BarChart data={soh_chart}>
                            <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                            <XAxis 
                                dataKey="cellNumber" 
                                label={{ value: 'Cell Number', position: 'insideBottom', offset: -5 }}
                            />
                            <YAxis 
                                label={{ value: 'SOH (%)', angle: -90, position: 'insideLeft' }}
                                domain={[0, 100]}
                            />
                            <Tooltip content={<SohTooltip />} />
                            <Legend />
                            
                            {/* Reference line untuk minimum standard SOH */}
                            <ReferenceLine 
                                y={80} 
                                stroke="#f59e0b" 
                                strokeDasharray="3 3" 
                                label={{ value: 'Min Standard (80%)', position: 'right' }}
                            />
                            
                            {/* Dynamic bars untuk setiap bank */}
                            {bank_names.map((bankName, index) => (
                                <Bar
                                    key={bankName}
                                    dataKey={bankName}
                                    fill={BANK_COLORS[index]}
                                    radius={[4, 4, 0, 0]}
                                />
                            ))}
                        </BarChart>
                    </ResponsiveContainer>
                </CardContent>
            </Card>

            {/* BANK COMPARISON RADAR CHART */}
            {bank_summary.length > 1 && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <TrendingDown className="h-5 w-5 text-purple-500" />
                            Battery Bank Comparison
                        </CardTitle>
                        <CardDescription>
                            Compare average metrics across all battery banks
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ResponsiveContainer width="100%" height={400}>
                            <RadarChart data={bank_summary}>
                                <PolarGrid />
                                <PolarAngleAxis dataKey="bank_name" />
                                <PolarRadiusAxis angle={90} domain={[0, 100]} />
                                <Radar
                                    name="Avg SOH (%)"
                                    dataKey="avg_soh"
                                    stroke="#10b981"
                                    fill="#10b981"
                                    fillOpacity={0.6}
                                />
                                <Radar
                                    name="Avg Voltage (V × 10)"
                                    dataKey={(data) => data.avg_voltage * 10}
                                    stroke="#3b82f6"
                                    fill="#3b82f6"
                                    fillOpacity={0.6}
                                />
                                <Legend />
                                <Tooltip />
                            </RadarChart>
                        </ResponsiveContainer>
                        <p className="mt-2 text-center text-xs text-muted-foreground">
                            * Voltage values are multiplied by 10 for visualization
                        </p>
                    </CardContent>
                </Card>
            )}

            {/* METADATA INFO */}
            <Card className="border-blue-500/20 bg-blue-500/5">
                <CardContent className="pt-6">
                    <div className="grid gap-4 md:grid-cols-3">
                        <div>
                            <p className="text-sm text-muted-foreground">Total Battery Banks</p>
                            <p className="text-2xl font-bold">{metadata.total_banks}</p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">Total Cells Measured</p>
                            <p className="text-2xl font-bold">{metadata.total_cells}</p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">Cells per Bank</p>
                            <p className="text-2xl font-bold">{metadata.max_cell_number}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
};