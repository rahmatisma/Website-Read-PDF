import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Activity,
    Battery,
    Building2,
    CheckCircle2,
    Lightbulb,
    MapPin,
    Package,
    Server,
    Thermometer,
    Users,
    Wind,
    Zap,
} from 'lucide-react';
import React from 'react';
import { DataTable } from './DataTable';
import { BatteryCharts } from './BatteryCharts';

interface DetailItemProps {
    label: string;
    value: string | null | undefined;
}

export const DetailItem: React.FC<DetailItemProps> = ({ label, value }) => {
    if (!value) return null;
    return (
        <div className="flex flex-col gap-1">
            <span className="text-sm text-muted-foreground">{label}</span>
            <span className="text-sm font-medium">{value}</span>
        </div>
    );
};

interface DynamicDataRendererProps {
    parsedData: any;
    documentType: string;
    batteryChartData?: any;
}

export const DynamicDataRenderer: React.FC<DynamicDataRendererProps> = ({ parsedData, documentType, batteryChartData }) => {
    // Icon mapping
    const getIcon = (type: string) => {
        if (type.includes('inverter')) return <Zap className="h-5 w-5 text-blue-500" />;
        if (type.includes('battery')) return <Battery className="h-5 w-5 text-green-500" />;
        if (type.includes('ac')) return <Wind className="h-5 w-5 text-cyan-500" />;
        if (type.includes('rectifier')) return <Activity className="h-5 w-5 text-purple-500" />;
        if (type.includes('shelter') || type.includes('ruang')) return <Building2 className="h-5 w-5 text-orange-500" />;
        if (type.includes('petir') || type.includes('grounding')) return <Lightbulb className="h-5 w-5 text-yellow-500" />;
        if (type.includes('temperature')) return <Thermometer className="h-5 w-5 text-red-500" />;
        return <CheckCircle2 className="h-5 w-5 text-blue-500" />;
    };

    /**
     *  SMART COLUMN DETECTION
     * Detect which "standard" key is used in the data
     */
    const detectStandardKey = (data: any[]): string => {
        if (!Array.isArray(data) || data.length === 0) return 'standard';
        
        const firstItem = data[0];
        
        // Check which key exists
        if (firstItem.hasOwnProperty('operational_standard')) return 'operational_standard';
        if (firstItem.hasOwnProperty('standard')) return 'standard';
        
        // Check nested checklist
        if (firstItem.checklist && firstItem.checklist.length > 0) {
            const nestedItem = firstItem.checklist[0];
            if (nestedItem.hasOwnProperty('operational_standard')) return 'operational_standard';
            if (nestedItem.hasOwnProperty('standard')) return 'standard';
        }
        
        return 'standard'; // default
    };

    /**
     *  GET TABLE COLUMNS based on data structure
     */
    const getColumnsForData = (data: any[]): any[] => {
        if (!Array.isArray(data) || data.length === 0) {
            return [
                { key: 'no', label: 'No', width: '80px' },
                { key: 'description', label: 'Description' },
                { key: 'result', label: 'Result', width: '150px' },
                { key: 'standard', label: 'Standard', width: '200px' },
                { key: 'status', label: 'Status', width: '100px' },
            ];
        }

        // Detect standard key
        const standardKey = detectStandardKey(data);

        // Base columns
        const columns = [
            { key: 'no', label: 'No', width: '80px' },
            { key: 'description', label: 'Description' },
            { key: 'result', label: 'Result', width: '150px' },
            { key: standardKey, label: 'Standard', width: '200px' },  // ← DYNAMIC!
            { key: 'status', label: 'Status', width: '100px' },
        ];

        return columns;
    };

    /**
     *  SPECIAL HANDLER FOR ROOM_TEMPERATURE
     * Convert room_temperature object to array format that DataTable can handle
     */
    const renderRoomTemperature = (roomTempData: any) => {
        if (!roomTempData || !roomTempData.checklist) return null;

        // Transform to array format with nested checklist
        const transformedData = [{
            no: roomTempData.no || '3',
            description: roomTempData.description || 'Room Temperature',
            result: '-',
            standard: '-',
            status: '-',
            checklist: roomTempData.checklist.map((item: any) => ({
                ...item,
                result: item.result || '-',
            }))
        }];

        const columns = [
            { key: 'no', label: 'No', width: '80px' },
            { key: 'description', label: 'Description' },
            { key: 'result', label: 'Result', width: '150px' },
            { key: 'standard', label: 'Standard', width: '200px' },
            { key: 'status', label: 'Status', width: '100px' },
        ];

        return (
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Thermometer className="h-5 w-5 text-red-500" />
                        Room Temperature
                    </CardTitle>
                    <CardDescription>Pengukuran suhu ruangan</CardDescription>
                </CardHeader>
                <CardContent>
                    <DataTable columns={columns} data={transformedData} nested={true} />
                </CardContent>
            </Card>
        );
    };

    // Render different sections based on data type
    const renderSection = (key: string, data: any, index: number) => {
        // Skip metadata, header, pelaksana (rendered separately)
        if (['header', 'informasi_umum', 'pelaksana', 'notes', 'dokumentasi', 'battery_banks', 'inventory'].includes(key)) {
            return null;
        }

        //  SPECIAL CASE: room_temperature
        if (key === 'room_temperature') {
            return renderRoomTemperature(data);
        }

        // Handle ARRAY data (most common)
        if (Array.isArray(data) && data.length > 0) {
            const title = key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
            
            // Check if has nested checklist or capacity_options
            const hasNested = data.some((item) => item.checklist || item.capacity_options);

            // Get appropriate columns
            const columns = getColumnsForData(data);

            return (
                <Card key={key}>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            {getIcon(key)}
                            {title}
                        </CardTitle>
                        <CardDescription>
                            {key.includes('check') && 'Pemeriksaan perangkat'}
                            {key.includes('test') && 'Hasil pengujian'}
                            {key.includes('measurement') && 'Hasil pengukuran'}
                            {key.includes('infrastructure') && 'Infrastruktur ruangan'}
                            {key.includes('temperature') && 'Pengukuran suhu'}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <DataTable columns={columns} data={data} nested={hasNested} />
                    </CardContent>
                </Card>
            );
        }

        // Handle OBJECT with nested arrays (e.g., performance_measurement)
        if (typeof data === 'object' && data !== null && !Array.isArray(data)) {
            const title = key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());

            // Check if it's a simple object (like psi_pressure, input_current)
            const isSimpleObject = !Object.values(data).some((val) => Array.isArray(val));

            if (isSimpleObject) {
                // Render as simple info card (untuk single measurement items)
                return (
                    <Card key={key}>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                {getIcon(key)}
                                {title}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {Object.entries(data).map(([fieldKey, fieldValue]) => (
                                <DetailItem
                                    key={fieldKey}
                                    label={fieldKey.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())}
                                    value={typeof fieldValue === 'object' ? JSON.stringify(fieldValue) : String(fieldValue)}
                                />
                            ))}
                        </CardContent>
                    </Card>
                );
            }

            // Render nested sections
            return (
                <Card key={key}>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            {getIcon(key)}
                            {title}
                        </CardTitle>
                        <CardDescription>Detail pengukuran per kategori</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {Object.entries(data).map(([sectionKey, sectionValue]) => {
                            if (Array.isArray(sectionValue) && sectionValue.length > 0) {
                                const sectionTitle = sectionKey.replace(/_/g, ' ').toUpperCase();
                                const columns = getColumnsForData(sectionValue);
                                return <DataTable key={sectionKey} columns={columns} data={sectionValue} title={sectionTitle} />;
                            }
                            return null;
                        })}
                    </CardContent>
                </Card>
            );
        }

        return null;
    };

    // Special handling for battery_banks
    const renderBatteryBanks = () => {
        if (!parsedData.battery_banks || parsedData.battery_banks.length === 0) return null;

        //  KONDISI 1: Jika ada chartData dari API, render grafik
        if (batteryChartData) {
            return (
                <>
                    {/* Grafik Interaktif */}
                    <BatteryCharts data={batteryChartData} />
                    
                    {/* Detail Table (optional - bisa di-collapse) */}
                    <details className="group">
                        <summary className="cursor-pointer text-sm text-muted-foreground hover:text-foreground">
                            Lihat Detail Table Battery Banks
                        </summary>
                        <div className="mt-4">
                            {renderBatteryBanksTable()}
                        </div>
                    </details>
                </>
            );
        }

        //  KONDISI 2: Fallback ke table view jika belum ada chartData
        return renderBatteryBanksTable();
    };

    // Helper function untuk render table view (existing code)
    const renderBatteryBanksTable = () => {
        return (
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Battery className="h-5 w-5 text-green-500" />
                        Battery Banks
                    </CardTitle>
                    <CardDescription>{parsedData.battery_banks.length} battery banks detected</CardDescription>
                </CardHeader>
                <CardContent className="space-y-6">
                    {parsedData.battery_banks.map((bank: any, bankIndex: number) => (
                        <div key={bankIndex} className="rounded-lg border border-green-500/20 bg-green-500/5 p-4">
                            <div className="mb-4 flex items-center gap-2">
                                <Badge variant="outline" className="border-green-500 font-semibold">
                                    Bank {bank.bank_number} - {bank.bank_type}
                                </Badge>
                            </div>

                            <div className="mb-4 grid gap-3 md:grid-cols-3">
                                <DetailItem label="Battery Type" value={bank.battery_type} />
                                <DetailItem label="Battery Brand" value={bank.battery_brand} />
                                <DetailItem label="Capacity" value={bank.end_device_batt} />
                            </div>

                            {bank.voltage_soh_table && bank.voltage_soh_table.length > 0 && (
                                <div>
                                    <h4 className="mb-2 font-semibold">Voltage & SOH Measurements</h4>
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-sm">
                                            <thead>
                                                <tr className="border-b">
                                                    <th className="pb-2 text-left">Cell No</th>
                                                    <th className="pb-2 text-left">Voltage (V)</th>
                                                    <th className="pb-2 text-left">SOH (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {bank.voltage_soh_table.map((cell: any, cellIndex: number) => (
                                                    <tr key={cellIndex} className="border-b">
                                                        <td className="py-2">{cell.no}</td>
                                                        <td className="py-2">{cell.voltage || '-'}</td>
                                                        <td className="py-2">
                                                            {cell.soh ? (
                                                                <Badge
                                                                    variant="outline"
                                                                    className={
                                                                        parseInt(cell.soh) >= 80
                                                                            ? 'border-green-500 text-green-400'
                                                                            : 'border-yellow-500 text-yellow-400'
                                                                    }
                                                                >
                                                                    {cell.soh}%
                                                                </Badge>
                                                            ) : (
                                                                '-'
                                                            )}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </CardContent>
            </Card>
        );
    };

    // Special handling for inventory
    const renderInventory = () => {
        if (!parsedData.inventory) return null;

        return (
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Package className="h-5 w-5 text-indigo-500" />
                        Equipment Inventory
                    </CardTitle>
                    <CardDescription>Daftar peralatan dan status</CardDescription>
                </CardHeader>
                <CardContent className="space-y-6">
                    {parsedData.inventory.device_sentral && parsedData.inventory.device_sentral.length > 0 && (
                        <div>
                            <h4 className="mb-3 font-semibold text-indigo-400">I. DEVICE SENTRAL</h4>
                            <DataTable
                                columns={[
                                    { key: 'equipment', label: 'Equipment' },
                                    { key: 'qty', label: 'Qty', width: '80px' },
                                    { key: 'status', label: 'Status', width: '100px' },
                                    { key: 'bonding_ground', label: 'Bonding Ground', width: '150px' },
                                    { key: 'keterangan', label: 'Keterangan' },
                                ]}
                                data={parsedData.inventory.device_sentral}
                            />
                        </div>
                    )}

                    {parsedData.inventory.supporting_facilities && parsedData.inventory.supporting_facilities.length > 0 && (
                        <div>
                            <h4 className="mb-3 font-semibold text-indigo-400">II. SUPPORTING FACILITIES (SARPEN)</h4>
                            <DataTable
                                columns={[
                                    { key: 'equipment', label: 'Equipment' },
                                    { key: 'qty', label: 'Qty', width: '80px' },
                                    { key: 'status', label: 'Status', width: '100px' },
                                    { key: 'keterangan', label: 'Keterangan' },
                                ]}
                                data={parsedData.inventory.supporting_facilities}
                            />
                        </div>
                    )}
                </CardContent>
            </Card>
        );
    };

    return (
        <>
            {/* Header & Informasi Umum */}
            <div className="grid gap-4 md:grid-cols-2">
                {parsedData.header && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                {getIcon(documentType)}
                                Document Header
                            </CardTitle>
                            <CardDescription>Informasi dokumen</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <DetailItem label="No. Dokumen" value={parsedData.header.no_dok} />
                            <DetailItem label="Judul" value={parsedData.header.judul} />
                            <DetailItem label="Versi" value={parsedData.header.versi} />
                            <DetailItem label="Halaman" value={parsedData.header.halaman} />
                            <DetailItem label="Label" value={parsedData.header.label} />
                        </CardContent>
                    </Card>
                )}

                {parsedData.informasi_umum && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MapPin className="h-5 w-5 text-blue-500" />
                                Informasi Umum
                            </CardTitle>
                            <CardDescription>Detail lokasi dan perangkat</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {Object.entries(parsedData.informasi_umum).map(([key, value]) => (
                                <DetailItem
                                    key={key}
                                    label={key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())}
                                    value={typeof value === 'object' ? JSON.stringify(value) : String(value)}
                                />
                            ))}
                        </CardContent>
                    </Card>
                )}
            </div>

            {/* Dynamic Sections */}
            {Object.entries(parsedData)
                .filter(([key]) => !['header', 'informasi_umum', 'pelaksana', 'notes', 'battery_banks', 'inventory'].includes(key))
                .map(([key, data], index) => renderSection(key, data, index))}

            {/* Special Sections */}
            {renderBatteryBanks()}
            {renderInventory()}

            {/* Pelaksana */}
            {parsedData.pelaksana && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Users className="h-5 w-5 text-blue-500" />
                            Pelaksana
                        </CardTitle>
                        <CardDescription>Tim pelaksana dan verifikator</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {parsedData.pelaksana.executor && parsedData.pelaksana.executor.length > 0 && (
                            <div>
                                <h4 className="mb-2 text-sm font-semibold">Executor:</h4>
                                <div className="space-y-2">
                                    {parsedData.pelaksana.executor.map((exec: any, index: number) => (
                                        <div key={index} className="rounded-lg border border-blue-500/20 bg-blue-500/5 p-3">
                                            <div className="grid gap-2 md:grid-cols-2">
                                                <DetailItem label={`Executor ${exec.no}`} value={exec.Nama} />
                                                <DetailItem label="Status" value={exec['Mitra / internal']} />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                        <DetailItem label="Verifikator" value={parsedData.pelaksana.verifikator} />
                        <DetailItem label="Head of Sub Department" value={parsedData.pelaksana.head_of_sub_department} />
                    </CardContent>
                </Card>
            )}

            {/* Notes */}
            {parsedData.notes && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <CheckCircle2 className="h-5 w-5 text-blue-500" />
                            Notes / Additional Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="whitespace-pre-wrap text-sm">{parsedData.notes}</p>
                    </CardContent>
                </Card>
            )}
        </>
    );
};