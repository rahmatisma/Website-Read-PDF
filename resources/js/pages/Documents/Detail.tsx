import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import axios from 'axios';
import { AlertCircle, ArrowLeft, Building2, Calendar, Camera, ClipboardList, FileText, HardDrive, Network, Package, Users } from 'lucide-react';
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

interface SPKData {
    no_spk: string;
    tipe_spk: string;
    judul_spk: string;
    tanggal_spk: string;
}

interface VendorData {
    teknisi: string;
    latitude: string;
    longitude: string;
    nama_vendor: string;
    pic_pelanggan: string;
    kontak_pic_pelanggan: string;
}

interface JaringanData {
    jasa: string;
    ip_lan: string | null;
    no_fmb: string | null;
    kecepatan: string;
    tgl_rfs_la: string | null;
    no_jaringan: string;
    opsi_router: string | null;
    tgl_rfs_plg: string | null;
    kode_jaringan: string | null;
    manage_router: string | null;
    jenis_aktivasi: string;
}

interface PelangganData {
    telepon: string | null;
    kontak_person: string | null;
    nama_pelanggan: string;
    lokasi_pelanggan: string;
}

interface PelaksanaanData {
    datang: string;
    selesai: string;
    permintaan_pelanggan: string;
}

interface BeritaAcaraData {
    jasa: string;
    ip_lan: string | null;
    no_fps: string | null;
    tanggal: string;
    telepon: string;
    tipe_spk: string;
    judul_spk: string;
    kecepatan: string;
    nomor_spk: string;
    tgl_rfs_la: string | null;
    no_jaringan: string;
    opsi_router: string | null;
    kontak_person: string;
    manage_router: string | null;
    jenis_aktivasi: string;
    nama_pelanggan: string;
    lokasi_pelanggan: string;
    tgl_rfs_pelanggan: string | null;
}

interface InformasiGedungData {
    email: string | null;
    alamat: string;
    telpon___fax: string;
    kontak_person: string;
    bagian___jabatan: string | null;
}

interface DokumentasiItem {
    jenis: string;
    patch_foto: string;
}

interface ExtractedData {
    data: {
        parsed: {
            data: {
                spk?: SPKData;
                vendor?: VendorData;
                jaringan?: JaringanData;
                pelanggan?: PelangganData;
                pelaksanaan?: PelaksanaanData;
                berita_acara?: BeritaAcaraData;
                informasi_gedung?: InformasiGedungData;
                pelaksanan_berita_acara?: PelaksanaanData;
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

interface DetailProps {
    upload: Upload;
    extractedData: ExtractedData | null;
}

export default function Detail({ upload: initialUpload, extractedData: initialExtractedData }: DetailProps) {
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
        router.visit(route('documents.filter', 'pdf'));
    };

    const getImageUrl = (localPath: string) => {
        const normalizedPath = localPath.replace(/\\/g, '/');
        return `/storage/${normalizedPath}`;
    };

    // Render detail item dengan label dan value
    const DetailItem = ({ label, value }: { label: string; value: string | null | undefined }) => {
        if (!value) return null;
        return (
            <div className="flex flex-col gap-1">
                <span className="text-sm text-muted-foreground">{label}</span>
                <span className="text-sm font-medium">{value}</span>
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
                                <h3 className="text-2xl font-bold">Data Extracted</h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Tipe: <Badge variant="outline">{extractedData.data.parsed.document_type || 'Unknown'}</Badge>
                                    {extractedData.data.parsed.jenis_spk && (
                                        <Badge variant="secondary" className="ml-2">
                                            {extractedData.data.parsed.jenis_spk}
                                        </Badge>
                                    )}
                                </p>
                            </div>
                            {extractedData.data && (
                                <Badge variant="outline" className="text-xs">
                                    Confidence: {extractedData.data.parsed.metadata?.detection_confidence}
                                </Badge>
                            )}
                        </div>

                        {/* Grid Cards */}
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Card SPK */}
                            {parsedData.spk && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <FileText className="h-5 w-5" />
                                            Informasi SPK
                                        </CardTitle>
                                        <CardDescription>Detail surat perintah kerja</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="No. SPK" value={parsedData.spk.no_spk} />
                                        <DetailItem label="Tipe SPK" value={parsedData.spk.tipe_spk} />
                                        <DetailItem label="Judul" value={parsedData.spk.judul_spk} />
                                        <DetailItem label="Tanggal" value={parsedData.spk.tanggal_spk} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Vendor */}
                            {parsedData.vendor && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Users className="h-5 w-5" />
                                            Informasi Vendor
                                        </CardTitle>
                                        <CardDescription>Detail vendor dan teknisi</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Nama Vendor" value={parsedData.vendor.nama_vendor} />
                                        <DetailItem label="Teknisi" value={parsedData.vendor.teknisi} />
                                        <DetailItem label="PIC Pelanggan" value={parsedData.vendor.pic_pelanggan} />
                                        <DetailItem label="Kontak PIC" value={parsedData.vendor.kontak_pic_pelanggan} />
                                        <DetailItem
                                            label="Koordinat (Latitude, Longitude)"
                                            value={
                                                parsedData.vendor.latitude && parsedData.vendor.longitude
                                                    ? `${parsedData.vendor.latitude}, ${parsedData.vendor.longitude}`
                                                    : null
                                            }
                                        />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Pelanggan */}
                            {parsedData.pelanggan && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Building2 className="h-5 w-5" />
                                            Informasi Pelanggan
                                        </CardTitle>
                                        <CardDescription>Detail pelanggan</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Nama Pelanggan" value={parsedData.pelanggan.nama_pelanggan} />
                                        <DetailItem label="Lokasi" value={parsedData.pelanggan.lokasi_pelanggan} />
                                        <DetailItem label="Kontak Person" value={parsedData.pelanggan.kontak_person} />
                                        <DetailItem label="Telepon" value={parsedData.pelanggan.telepon} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Jaringan */}
                            {parsedData.jaringan && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Network className="h-5 w-5" />
                                            Informasi Jaringan
                                        </CardTitle>
                                        <CardDescription>Detail jaringan dan layanan</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Jasa" value={parsedData.jaringan.jasa} />
                                        <DetailItem label="No. Jaringan" value={parsedData.jaringan.no_jaringan} />
                                        <DetailItem label="Kecepatan" value={parsedData.jaringan.kecepatan} />
                                        <DetailItem label="Jenis Aktivasi" value={parsedData.jaringan.jenis_aktivasi} />
                                        <DetailItem label="IP LAN" value={parsedData.jaringan.ip_lan} />
                                        <DetailItem label="Kode Jaringan" value={parsedData.jaringan.kode_jaringan} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Pelaksanaan */}
                            {parsedData.pelaksanaan && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Calendar className="h-5 w-5" />
                                            Waktu Pelaksanaan
                                        </CardTitle>
                                        <CardDescription>Timeline pelaksanaan pekerjaan</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Permintaan Pelanggan" value={parsedData.pelaksanaan.permintaan_pelanggan} />
                                        <DetailItem label="Teknisi Datang" value={parsedData.pelaksanaan.datang} />
                                        <DetailItem label="Selesai" value={parsedData.pelaksanaan.selesai} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Informasi Gedung */}
                            {parsedData.informasi_gedung && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Building2 className="h-5 w-5" />
                                            Informasi Gedung
                                        </CardTitle>
                                        <CardDescription>Detail lokasi gedung</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Alamat" value={parsedData.informasi_gedung.alamat} />
                                        <DetailItem label="Kontak Person" value={parsedData.informasi_gedung.kontak_person} />
                                        <DetailItem label="Telepon/Fax" value={parsedData.informasi_gedung.telpon___fax} />
                                        <DetailItem label="Email" value={parsedData.informasi_gedung.email} />
                                        <DetailItem label="Bagian/Jabatan" value={parsedData.informasi_gedung.bagian___jabatan} />
                                    </CardContent>
                                </Card>
                            )}
                        </div>

                        {/* Card Berita Acara (Full Width) */}
                        {parsedData.berita_acara && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <ClipboardList className="h-5 w-5" />
                                        Berita Acara
                                    </CardTitle>
                                    <CardDescription>Detail berita acara pelaksanaan</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid gap-3 md:grid-cols-3">
                                        <DetailItem label="Nomor SPK" value={parsedData.berita_acara.nomor_spk} />
                                        <DetailItem label="Tanggal" value={parsedData.berita_acara.tanggal} />
                                        <DetailItem label="Tipe SPK" value={parsedData.berita_acara.tipe_spk} />
                                        <DetailItem label="Nama Pelanggan" value={parsedData.berita_acara.nama_pelanggan} />
                                        <DetailItem label="Lokasi" value={parsedData.berita_acara.lokasi_pelanggan} />
                                        <DetailItem label="Kontak Person" value={parsedData.berita_acara.kontak_person} />
                                        <DetailItem label="Telepon" value={parsedData.berita_acara.telepon} />
                                        <DetailItem label="Jasa" value={parsedData.berita_acara.jasa} />
                                        <DetailItem label="No. Jaringan" value={parsedData.berita_acara.no_jaringan} />
                                        <DetailItem label="Kecepatan" value={parsedData.berita_acara.kecepatan} />
                                        <DetailItem label="Jenis Aktivasi" value={parsedData.berita_acara.jenis_aktivasi} />
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Dokumentasi */}
                        {extractedData.data.dokumentasi && extractedData.data.dokumentasi.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Camera className="h-5 w-5" />
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
                                                            // Fallback jika gambar gagal load
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

                        {/* Raw JSON Toggle (Optional - untuk debugging) */}
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
            <Head title={`Detail - ${upload.file_name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <button onClick={goBack} className="rounded-lg p-2 transition-colors hover:bg-accent">
                        <ArrowLeft size={20} />
                    </button>
                    <h1 className="text-2xl font-bold">Detail Dokumen</h1>

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
                                className="flex items-center gap-2 rounded-lg bg-primary/10 px-4 py-2 transition-colors hover:bg-primary/20"
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
