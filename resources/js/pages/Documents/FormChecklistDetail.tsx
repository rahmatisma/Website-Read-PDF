// FormChecklistDetail.tsx - Complete component for displaying Form Checklist details
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import FormChecklistPerangkatDetail from './FormChecklistPerangkatDetail';
import axios from 'axios';
import {
    AlertCircle,
    ArrowLeft,
    Calendar,
    Camera,
    CheckCircle2,
    ClipboardCheck,
    FileText,
    HardDrive,
    MapPin,
    Network,
    Package,
    Thermometer,
    Zap,
} from 'lucide-react';
import { useEffect, useState } from 'react';

interface Upload {
    id_upload: number;
    file_name: string;
    file_path: string;
    file_size: number;
    document_type: string;
    status: string;
    created_at: string;
    updated_at: string;
}

interface DokumentasiItem {
    jenis: string;
    patch_foto: string;
}

interface ChecklistParameter {
    existing?: string;
    standard: string;
    perbaikan?: string;
    hasil_akhir?: string;
    line_checklist?: string;
    nms_engineer?: string;
    on_site_teknisi?: string;
}

interface ExtractedData {
    data: {
        parsed: {
            data: {
                data_remote?: {
                    kota?: string;
                    alamat?: string;
                    no_spk?: string;
                    tanggal?: string;
                    propinsi?: string;
                    pelaksanaan?: {
                        jam_pulang?: string;
                        keterangan?: string;
                        jam_perintah?: string;
                        jam_berangkat?: string;
                        jam_persiapan?: string;
                        jam_mulai_kerja?: string;
                        jam_selesai_kerja?: string;
                        jam_tiba_di_kantor?: string;
                        jam_tiba_di_lokasi?: string;
                    };
                    nomor_telepon?: string;
                    contact_person?: string;
                    nama_pelanggan?: string;
                    nomor_jaringan?: string;
                };
                data_perangkat?: {
                    note?: string;
                    cabut?: Array<{ sn?: string; no_reg?: string; nama_barang?: string }>;
                    existing?: Array<{ sn?: string; no_reg?: string; nama_barang?: string }>;
                    tidak_terpakai?: Array<{ sn?: string; no_reg?: string; nama_barang?: string }>;
                    pengganti_atau_pasang_baru?: Array<{ sn?: string; no_reg?: string; nama_barang?: string }>;
                };
                line_checklist?: {
                    line_fo?: { parameter_kualitas?: ChecklistParameter[] };
                    site_area?: { parameter_kualitas?: ChecklistParameter[] };
                    hrb_r_lintas?: { parameter_kualitas?: ChecklistParameter[] };
                    tes_konektivitas?: { parameter_kualitas?: ChecklistParameter[] };
                };
                global_checklist?: {
                    electrical?: {
                        grounding_bar_terkoneksi_ke?: string;
                        output_tegangan_mengacu_modem?: {
                            n_g?: { it?: string; pln?: string; ups?: string };
                            p_g?: { it?: string; pln?: string; ups?: string };
                            p_n?: { it?: string; pln?: string; ups?: string };
                        };
                    };
                    data_lokasi?: {
                        ruang?: string;
                        latitude?: string;
                        longitude?: string;
                        posisi_modem_di_lt?: string;
                    };
                    environment?: {
                        ac_pendingin_ruangan?: string;
                        suhu_ruangan_perangkat?: string;
                    };
                };
                indoor_area_checklist?: any;
                outdoor_area_checklist?: any; // ✅ TAMBAHKAN INI
            };
            metadata?: {
                parser_used: string;
                ocr_data_available: boolean;
                detection_confidence: string;
            };
            jenis_spk?: string;
            document_type: string;
        };
        dokumentasi?: DokumentasiItem[];
    };
}

interface FormChecklistDetailProps {
    upload: Upload;
    extractedData: ExtractedData | null;
}

export default function FormChecklistDetail({ upload: initialUpload, extractedData: initialExtractedData }: FormChecklistDetailProps) {
    const [upload, setUpload] = useState(initialUpload);
    const [extractedData, setExtractedData] = useState(initialExtractedData);
    const [isPolling, setIsPolling] = useState(false);

    useEffect(() => {
        if (upload.status === 'processing' || upload.status === 'uploaded') {
            setIsPolling(true);

            const interval = setInterval(async () => {
                try {
                    const response = await axios.get(`/api/documents/${upload.id_upload}/status`);
                    const data = response.data;

                    setUpload((prev) => ({
                        ...prev,
                        status: data.status,
                        updated_at: data.updated_at,
                    }));

                    if (data.status === 'completed' || data.status === 'failed') {
                        router.reload({ only: ['upload', 'extractedData'] });
                        setIsPolling(false);
                        clearInterval(interval);
                    }
                } catch (error) {
                    console.error('Error polling status:', error);
                }
            }, 3000);

            return () => {
                clearInterval(interval);
                setIsPolling(false);
            };
        }
    }, [upload.status, upload.id_upload]);

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatFileSize = (size: number) => {
        if (size >= 1024 * 1024) {
            return (size / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return (size / 1024).toFixed(2) + ' KB';
    };

    const openPDF = () => {
        window.open(`/storage/${upload.file_path}`, '_blank');
    };

    const goBack = () => {
        router.visit(route('documents.filter', 'form-checklist'));
    };

    const getImageUrl = (localPath: string) => {
        const normalizedPath = localPath.replace(/\\/g, '/');
        return `/storage/${normalizedPath}`;
    };

    const DetailItem = ({ label, value }: { label: string; value: string | null | undefined }) => {
        if (!value) return null;
        return (
            <div className="flex flex-col gap-1 wrap-break-word">
                <span className="text-sm text-muted-foreground">{label}</span>
                <span className="text-sm font-medium wrap-break-word">{value}</span>
            </div>
        );
    };

    const renderContent = () => {
        switch (upload.status) {
            case 'uploaded':
                return (
                    <div className="py-12 text-center">
                        <div className="mx-auto mb-4 h-16 w-16 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                        <p className="text-muted-foreground">Menunggu proses dimulai...</p>
                        {isPolling && <p className="mt-2 text-sm text-muted-foreground">Mengecek status otomatis...</p>}
                    </div>
                );

            case 'processing':
                return (
                    <div className="py-12 text-center">
                        <div className="mx-auto mb-4 h-16 w-16 animate-spin rounded-full border-4 border-yellow-500 border-t-transparent"></div>
                        <p className="text-muted-foreground">Dokumen sedang diproses oleh Python...</p>
                        {isPolling && <p className="mt-2 text-sm text-muted-foreground">✅ Mengecek status otomatis setiap 3 detik</p>}
                    </div>
                );

            case 'failed':
                return (
                    <div className="py-12 text-center">
                        <AlertCircle className="mx-auto mb-4 h-16 w-16 text-red-500" />
                        <p className="font-semibold text-red-400">Proses dokumen gagal</p>
                        <p className="mt-2 text-sm text-muted-foreground">Silakan coba upload ulang</p>
                    </div>
                );

            case 'completed':
                if (!extractedData || !extractedData.data || !extractedData.data.parsed) {
                    return (
                        <div className="py-12 text-center">
                            <p className="text-muted-foreground">Data tidak tersedia</p>
                        </div>
                    );
                }

                const parsed = extractedData.data.parsed;
                const parsedData = parsed.data;

                return (
                    <div className="space-y-6">
                        {/* Header Info */}
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold">Form Checklist Data</h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Tipe:{' '}
                                    <Badge variant="outline" className="border-emerald-500 text-emerald-400">
                                        {parsed.document_type || 'Unknown'}
                                    </Badge>
                                    {parsed.metadata?.parser_used && (
                                        <Badge variant="secondary" className="ml-2">
                                            {parsed.metadata.parser_used}
                                        </Badge>
                                    )}
                                </p>
                            </div>
                            {parsed.metadata && (
                                <Badge variant="outline" className="text-xs">
                                    Confidence: {parsed.metadata.detection_confidence}
                                </Badge>
                            )}
                        </div>

                        {/* Grid Cards */}
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Card Data Remote */}
                            {parsedData.data_remote && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <FileText className="h-5 w-5 text-emerald-500" />
                                            Data Remote
                                        </CardTitle>
                                        <CardDescription>Informasi SPK dan lokasi</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="No. SPK" value={parsedData.data_remote.no_spk} />
                                        <DetailItem label="Tanggal" value={parsedData.data_remote.tanggal} />
                                        <DetailItem label="Nama Pelanggan" value={parsedData.data_remote.nama_pelanggan} />
                                        <DetailItem label="Nomor Jaringan" value={parsedData.data_remote.nomor_jaringan} />
                                        <DetailItem label="Contact Person" value={parsedData.data_remote.contact_person} />
                                        <DetailItem label="Nomor Telepon" value={parsedData.data_remote.nomor_telepon} />
                                        <DetailItem label="Alamat" value={parsedData.data_remote.alamat} />
                                        <DetailItem label="Kota" value={parsedData.data_remote.kota} />
                                        <DetailItem label="Propinsi" value={parsedData.data_remote.propinsi} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Pelaksanaan */}
                            {parsedData.data_remote?.pelaksanaan && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Calendar className="h-5 w-5 text-emerald-500" />
                                            Waktu Pelaksanaan
                                        </CardTitle>
                                        <CardDescription>Timeline pekerjaan</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Jam Perintah" value={parsedData.data_remote.pelaksanaan.jam_perintah} />
                                        <DetailItem label="Jam Persiapan" value={parsedData.data_remote.pelaksanaan.jam_persiapan} />
                                        <DetailItem label="Jam Berangkat" value={parsedData.data_remote.pelaksanaan.jam_berangkat} />
                                        <DetailItem label="Jam Tiba di Lokasi" value={parsedData.data_remote.pelaksanaan.jam_tiba_di_lokasi} />
                                        <DetailItem label="Jam Mulai Kerja" value={parsedData.data_remote.pelaksanaan.jam_mulai_kerja} />
                                        <DetailItem label="Jam Selesai Kerja" value={parsedData.data_remote.pelaksanaan.jam_selesai_kerja} />
                                        <DetailItem label="Jam Tiba di Kantor" value={parsedData.data_remote.pelaksanaan.jam_tiba_di_kantor} />
                                        <DetailItem label="Jam Pulang" value={parsedData.data_remote.pelaksanaan.jam_pulang} />
                                        <DetailItem label="Keterangan" value={parsedData.data_remote.pelaksanaan.keterangan} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Global Checklist - Environment */}
                            {parsedData.global_checklist?.environment && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Thermometer className="h-5 w-5 text-emerald-500" />
                                            Environment
                                        </CardTitle>
                                        <CardDescription>Kondisi lingkungan</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem
                                            label="AC Pendingin Ruangan"
                                            value={parsedData.global_checklist.environment.ac_pendingin_ruangan}
                                        />
                                        <DetailItem
                                            label="Suhu Ruangan Perangkat"
                                            value={parsedData.global_checklist.environment.suhu_ruangan_perangkat}
                                        />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Data Lokasi */}
                            {parsedData.global_checklist?.data_lokasi && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <MapPin className="h-5 w-5 text-emerald-500" />
                                            Data Lokasi
                                        </CardTitle>
                                        <CardDescription>Informasi lokasi perangkat</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Ruang" value={parsedData.global_checklist.data_lokasi.ruang} />
                                        <DetailItem label="Posisi Modem di Lt" value={parsedData.global_checklist.data_lokasi.posisi_modem_di_lt} />
                                        <DetailItem label="Latitude" value={parsedData.global_checklist.data_lokasi.latitude} />
                                        <DetailItem label="Longitude" value={parsedData.global_checklist.data_lokasi.longitude} />
                                    </CardContent>
                                </Card>
                            )}
                        </div>

                        {/* Card Data Perangkat (Full Width) */}
                        {parsedData.data_perangkat && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Package className="h-5 w-5 text-emerald-500" />
                                        Data Perangkat
                                    </CardTitle>
                                    <CardDescription>Daftar perangkat dan statusnya</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {parsedData.data_perangkat.note && (
                                        <div className="rounded-lg bg-muted p-3">
                                            <p className="text-sm text-muted-foreground">
                                                <strong>Note:</strong> {parsedData.data_perangkat.note}
                                            </p>
                                        </div>
                                    )}

                                    {/* Existing Devices */}
                                    {parsedData.data_perangkat.existing && parsedData.data_perangkat.existing.length > 0 && (
                                        <div>
                                            <h4 className="mb-2 flex items-center gap-2 font-semibold">
                                                <CheckCircle2 className="h-4 w-4 text-green-500" />
                                                Perangkat Existing ({parsedData.data_perangkat.existing.length})
                                            </h4>
                                            <div className="space-y-2">
                                                {parsedData.data_perangkat.existing.map((device, index) => (
                                                    <div key={index} className="rounded-lg border p-3">
                                                        <div className="grid gap-2 md:grid-cols-3">
                                                            <DetailItem label="Nama Barang" value={device.nama_barang} />
                                                            <DetailItem label="No. Registrasi" value={device.no_reg} />
                                                            <DetailItem label="Serial Number" value={device.sn} />
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* New/Replacement Devices */}
                                    {parsedData.data_perangkat.pengganti_atau_pasang_baru &&
                                        parsedData.data_perangkat.pengganti_atau_pasang_baru.length > 0 && (
                                            <div>
                                                <h4 className="mb-2 flex items-center gap-2 font-semibold">
                                                    <Package className="h-4 w-4 text-blue-500" />
                                                    Pengganti/Pasang Baru ({parsedData.data_perangkat.pengganti_atau_pasang_baru.length})
                                                </h4>
                                                <div className="space-y-2">
                                                    {parsedData.data_perangkat.pengganti_atau_pasang_baru.map((device, index) => (
                                                        <div key={index} className="rounded-lg border p-3">
                                                            <div className="grid gap-2 md:grid-cols-3">
                                                                <DetailItem label="Nama Barang" value={device.nama_barang} />
                                                                <DetailItem label="No. Registrasi" value={device.no_reg} />
                                                                <DetailItem label="Serial Number" value={device.sn} />
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        )}

                                    {/* Removed Devices */}
                                    {parsedData.data_perangkat.cabut && parsedData.data_perangkat.cabut.length > 0 && (
                                        <div>
                                            <h4 className="mb-2 flex items-center gap-2 font-semibold">
                                                <AlertCircle className="h-4 w-4 text-orange-500" />
                                                Perangkat Cabut ({parsedData.data_perangkat.cabut.length})
                                            </h4>
                                            <div className="space-y-2">
                                                {parsedData.data_perangkat.cabut.map((device, index) => (
                                                    <div key={index} className="rounded-lg border p-3">
                                                        <div className="grid gap-2 md:grid-cols-3">
                                                            <DetailItem label="Nama Barang" value={device.nama_barang} />
                                                            <DetailItem label="No. Registrasi" value={device.no_reg} />
                                                            <DetailItem label="Serial Number" value={device.sn} />
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Electrical */}
                        {parsedData.global_checklist?.electrical && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Zap className="h-5 w-5 text-emerald-500" />
                                        Electrical Check
                                    </CardTitle>
                                    <CardDescription>Pengukuran tegangan listrik</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <DetailItem
                                        label="Grounding Bar Terkoneksi ke"
                                        value={parsedData.global_checklist.electrical.grounding_bar_terkoneksi_ke}
                                    />

                                    {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem && (
                                        <div>
                                            <h4 className="mb-3 font-semibold">Output Tegangan Mengacu Modem</h4>
                                            <div className="overflow-x-auto">
                                                <table className="w-full text-sm">
                                                    <thead>
                                                        <tr className="border-b">
                                                            <th className="pb-2 text-left">Parameter</th>
                                                            <th className="pb-2 text-center">PLN</th>
                                                            <th className="pb-2 text-center">UPS</th>
                                                            <th className="pb-2 text-center">IT</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr className="border-b">
                                                            <td className="py-2">P-N</td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.p_n?.pln || '-'}
                                                            </td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.p_n?.ups || '-'}
                                                            </td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.p_n?.it || '-'}
                                                            </td>
                                                        </tr>
                                                        <tr className="border-b">
                                                            <td className="py-2">P-G</td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.p_g?.pln || '-'}
                                                            </td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.p_g?.ups || '-'}
                                                            </td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.p_g?.it || '-'}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td className="py-2">N-G</td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.n_g?.pln || '-'}
                                                            </td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.n_g?.ups || '-'}
                                                            </td>
                                                            <td className="py-2 text-center">
                                                                {parsedData.global_checklist.electrical.output_tegangan_mengacu_modem.n_g?.it || '-'}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* Line Checklist Cards */}
                        {parsedData.line_checklist && (
                            <>
                                {/* Line FO */}
                                {parsedData.line_checklist.line_fo?.parameter_kualitas && (
                                    <Card>
                                        <CardHeader>
                                            <CardTitle className="flex items-center gap-2">
                                                <Network className="h-5 w-5 text-emerald-500" />
                                                Line FO Checklist
                                            </CardTitle>
                                            <CardDescription>Parameter kualitas fiber optic</CardDescription>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="overflow-x-auto">
                                                <table className="w-full text-sm">
                                                    <thead>
                                                        <tr className="border-b">
                                                            <th className="pb-2 text-left">Checklist</th>
                                                            <th className="pb-2 text-left">Standard</th>
                                                            <th className="pb-2 text-left">Existing</th>
                                                            <th className="pb-2 text-left">Perbaikan</th>
                                                            <th className="pb-2 text-left">Hasil Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {parsedData.line_checklist.line_fo.parameter_kualitas.map((param, index) => (
                                                            <tr key={index} className="border-b">
                                                                <td className="py-2">{param.line_checklist}</td>
                                                                <td className="py-2">{param.standard}</td>
                                                                <td className="py-2">{param.existing || '-'}</td>
                                                                <td className="py-2">{param.perbaikan || '-'}</td>
                                                                <td className="py-2">{param.hasil_akhir || '-'}</td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </CardContent>
                                    </Card>
                                )}

                                {/* Site Area */}
                                {parsedData.line_checklist.site_area?.parameter_kualitas && (
                                    <Card>
                                        <CardHeader>
                                            <CardTitle className="flex items-center gap-2">
                                                <ClipboardCheck className="h-5 w-5 text-emerald-500" />
                                                Site Area Checklist
                                            </CardTitle>
                                            <CardDescription>Pengecekan area site</CardDescription>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="overflow-x-auto">
                                                <table className="w-full text-sm">
                                                    <thead>
                                                        <tr className="border-b">
                                                            <th className="pb-2 text-left">Checklist</th>
                                                            <th className="pb-2 text-left">Standard</th>
                                                            <th className="pb-2 text-left">Existing</th>
                                                            <th className="pb-2 text-left">Perbaikan</th>
                                                            <th className="pb-2 text-left">Hasil Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {parsedData.line_checklist.site_area.parameter_kualitas.map((param, index) => (
                                                            <tr key={index} className="border-b">
                                                                <td className="py-2">{param.line_checklist}</td>
                                                                <td className="py-2">{param.standard}</td>
                                                                <td className="py-2">{param.existing || '-'}</td>
                                                                <td className="py-2">{param.perbaikan || '-'}</td>
                                                                <td className="py-2">{param.hasil_akhir || '-'}</td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </CardContent>
                                    </Card>
                                )}

                                {/* HRB/R-Lintas */}
                                {parsedData.line_checklist.hrb_r_lintas?.parameter_kualitas && (
                                    <Card>
                                        <CardHeader>
                                            <CardTitle className="flex items-center gap-2">
                                                <Network className="h-5 w-5 text-emerald-500" />
                                                HRB/R-Lintas Checklist
                                            </CardTitle>
                                            <CardDescription>Pengecekan HRB dan R-Lintas</CardDescription>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="overflow-x-auto">
                                                <table className="w-full text-sm">
                                                    <thead>
                                                        <tr className="border-b">
                                                            <th className="pb-2 text-left">Checklist</th>
                                                            <th className="pb-2 text-left">Standard</th>
                                                            <th className="pb-2 text-left">Existing</th>
                                                            <th className="pb-2 text-left">Perbaikan</th>
                                                            <th className="pb-2 text-left">Hasil Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {parsedData.line_checklist.hrb_r_lintas.parameter_kualitas.map((param, index) => (
                                                            <tr key={index} className="border-b">
                                                                <td className="py-2">{param.line_checklist}</td>
                                                                <td className="py-2">{param.standard}</td>
                                                                <td className="py-2">{param.existing || '-'}</td>
                                                                <td className="py-2">{param.perbaikan || '-'}</td>
                                                                <td className="py-2">{param.hasil_akhir || '-'}</td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </CardContent>
                                    </Card>
                                )}

                                {/* Tes Konektivitas */}
                                {parsedData.line_checklist.tes_konektivitas?.parameter_kualitas && (
                                    <Card>
                                        <CardHeader>
                                            <CardTitle className="flex items-center gap-2">
                                                <Network className="h-5 w-5 text-emerald-500" />
                                                Tes Konektivitas
                                            </CardTitle>
                                            <CardDescription>Hasil pengujian koneksi</CardDescription>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="overflow-x-auto">
                                                <table className="w-full text-sm">
                                                    <thead>
                                                        <tr className="border-b">
                                                            <th className="pb-2 text-left">Checklist</th>
                                                            <th className="pb-2 text-left">Standard</th>
                                                            <th className="pb-2 text-left">Existing</th>
                                                            <th className="pb-2 text-left">Perbaikan</th>
                                                            <th className="pb-2 text-left">Hasil Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {parsedData.line_checklist.tes_konektivitas.parameter_kualitas.map((param, index) => (
                                                            <tr key={index} className="border-b">
                                                                <td className="py-2">{param.line_checklist}</td>
                                                                <td className="py-2">{param.standard}</td>
                                                                <td className="py-2">{param.existing || '-'}</td>
                                                                <td className="py-2">{param.perbaikan || '-'}</td>
                                                                <td className="py-2">{param.hasil_akhir || '-'}</td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </CardContent>
                                    </Card>
                                )}
                            </>
                        )}

                        {/* Indoor & Outdoor Area Checklist - Menggunakan komponen terpisah */}
{(parsedData.indoor_area_checklist || parsedData.outdoor_area_checklist) && (
    <FormChecklistPerangkatDetail
        indoorAreaChecklist={parsedData.indoor_area_checklist}
        outdoorAreaChecklist={parsedData.outdoor_area_checklist}
    />
)}

                        {/* Card Dokumentasi */}
                        {extractedData.data.dokumentasi && extractedData.data.dokumentasi.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Camera className="h-5 w-5 text-emerald-500" />
                                        Dokumentasi Foto
                                    </CardTitle>
                                    <CardDescription>{extractedData.data.dokumentasi.length} foto dokumentasi</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                        {extractedData.data.dokumentasi.map((doc, index) => (
                                            <div key={index} className="space-y-2 rounded-lg border p-4">
                                                <div className="flex items-center gap-2">
                                                    <Package className="h-4 w-4 text-muted-foreground" />
                                                    <Badge variant="secondary" className="text-xs">
                                                        {doc.jenis}
                                                    </Badge>
                                                </div>

                                                {/* Preview Gambar */}
                                                <div className="relative aspect-video w-full overflow-hidden rounded-lg bg-muted">
                                                    <img
                                                        src={getImageUrl(doc.patch_foto)}
                                                        alt={doc.jenis}
                                                        className="h-full w-full object-cover"
                                                        onError={(e) => {
                                                            e.currentTarget.src = '/placeholder-image.jpg';
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Raw JSON Toggle */}
                        <details className="group">
                            <summary className="cursor-pointer text-sm text-muted-foreground hover:text-foreground">📋 Lihat Raw JSON Data</summary>
                            <Card className="mt-2">
                                <CardContent className="pt-6">
                                    <pre className="max-h-96 overflow-auto rounded bg-muted p-4 text-xs">
                                        {JSON.stringify(extractedData, null, 2)}
                                    </pre>
                                </CardContent>
                            </Card>
                        </details>
                    </div>
                );

            default:
                return (
                    <div className="py-12 text-center">
                        <p className="text-muted-foreground">Status: {upload.status}</p>
                    </div>
                );
        }
    };

    return (
        <AppLayout>
            <Head title={`Form Checklist Detail - ${upload.file_name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <button onClick={goBack} className="rounded-lg p-2 transition-colors hover:bg-accent">
                        <ArrowLeft size={20} />
                    </button>
                    <h1 className="text-2xl font-bold">Form Checklist Detail</h1>

                    {isPolling && (
                        <Badge variant="outline" className="ml-auto">
                            <span className="mr-2 h-2 w-2 animate-pulse rounded-full bg-blue-400"></span>
                            Auto-refresh aktif
                        </Badge>
                    )}
                </div>

                {/* Card Header Info */}
                <Card>
                    <CardHeader>
                        <div className="flex items-start justify-between">
                            <div className="flex-1 space-y-2">
                                <CardTitle className="text-xl">{upload.file_name}</CardTitle>

                                <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                                    <div className="flex items-center gap-2">
                                        <Calendar size={16} />
                                        <span>{formatDate(upload.created_at)}</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <HardDrive size={16} />
                                        <span>{formatFileSize(upload.file_size)}</span>
                                    </div>
                                </div>

                                {/* Status Badge */}
                                <div>
                                    {upload.status === 'completed' && (
                                        <Badge variant="outline" className="border-green-500 text-green-600">
                                            ✅ Selesai Diproses
                                        </Badge>
                                    )}
                                    {upload.status === 'processing' && (
                                        <Badge variant="outline" className="animate-pulse border-yellow-500 text-yellow-600">
                                            ⏳ Sedang Diproses
                                        </Badge>
                                    )}
                                    {upload.status === 'uploaded' && (
                                        <Badge variant="outline" className="animate-pulse border-blue-500 text-blue-600">
                                            📤 Menunggu Proses
                                        </Badge>
                                    )}
                                    {upload.status === 'failed' && (
                                        <Badge variant="outline" className="border-red-500 text-red-600">
                                            ❌ Gagal Diproses
                                        </Badge>
                                    )}
                                </div>
                            </div>

                            <button
                                onClick={openPDF}
                                className="flex items-center gap-2 rounded-lg bg-emerald-500/10 px-4 py-2 text-emerald-400 transition-colors hover:bg-emerald-500/20"
                            >
                                <FileText size={18} />
                                <span>Lihat PDF</span>
                            </button>
                        </div>
                    </CardHeader>
                </Card>

                {/* Content Area */}
                <div>{renderContent()}</div>
            </div>
        </AppLayout>
    );
}
