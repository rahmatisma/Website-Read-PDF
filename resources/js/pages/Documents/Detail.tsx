import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import axios from 'axios';
import {
    AlertCircle,
    Antenna,
    ArrowLeft,
    Building2,
    Calendar,
    Camera,
    ClipboardList,
    DollarSign,
    FileText,
    HardDrive,
    Network,
    Package,
    Server,
    Users,
    Wifi,
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

interface SPKData {
    no_mr: string;
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
    pop: string;
    jasa: string;
    ip_lan: string | null;
    no_fmb: string | null;
    kecepatan: string;
    tgl_rfs_la: string | null;
    media_akses: string | null;
    no_jaringan: string;
    opsi_router: string | null;
    tgl_rfs_plg: string | null;
    kode_jaringan: string | null;
    manage_router: string | null;
    jenis_aktivasi: string | null;
}

interface ListItems {
    kode?: string | null;
    deskripsi?: string | null;
    [key: string]: string | null | undefined;
}

interface ListItemsData {
    [key: string]: ListItems;
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
    pop: string;
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
    media_akses: string | null;
    no_jaringan: string;
    opsi_router: string | null;
    kontak_person: string | null;
    manage_router: string | null;
    nama_pelanggan: string;
    lokasi_pelanggan: string;
    tgl_rfs_pelanggan: string | null;
}

interface PekerjaCabutData {
    teknisi: string | null;
    nama_vendor: string | null;
    pic_pelanggan: string | null;
    kontak_pic_pelanggan: string | null;
}

interface HHBaruItem {
    lokasi_hh_1?: string | null;
    kapasitas_closure_1?: string | null;
    longitude_dan_latitude_hh_1?: string | null;
    kebutuhan_penambahan_closure_1?: string | null;
    // Support dinamis field
    [key: string]: string | null | undefined;
}

interface HHBaruData {
    [key: string]: HHBaruItem;
}

interface KawasanUmumData {
    nama_kawasan_umum__pu_yang_dilewati: string | null;
    panjang__jalur_outdoor_di_kawasan_umum: string | null;
}

interface InformasiGedungData {
    email: string | null;
    alamat: string;
    biaya_ikg: string;
    pelanggan_fo: string;
    telpon___fax: string;
    kontak_person: string;
    status_gedung: string | null;
    kondisi_gedung: string | null;
    bagian___jabatan: string | null;
    pemilik_bangunan: string | null;
    sewa_shaft_kabel: string;
    penempatan_antena: string;
    sewa_space_antena: string;
    jumlah_lantai_gedung: string | null;
    penanggungjawab_sewa: string;
}

interface HHEksistingItem {
    lokasi_hh_1?: string | null;
    kondisi_hh_1?: string | null;
    kondisi_closure_1?: string | null;
    kapasitas_closure_1?: string | null;
    ketersediaan_closure_1?: string | null;
    longitude_dan_latitude_hh_1?: string | null;
    // Support dinamis field
    [key: string]: string | null | undefined;
}

interface HHEksistingData {
    [key: string]: HHEksistingItem;
}

interface SplitterData {
    arah_akses: string;
    id_splitter: string | null;
    lokasi_splitter: string | null;
    list_port_kosong: string | null;
    kapasitas_splitter: string | null;
    list_port_kosong_dan_redaman: string | null;
    nama_node_jika_tidak_ada_splitter: string | null;
}

interface LokasiAntenaData {
    tower___pole: string;
    lokasi_antena: string | null;
    tindak_lanjut: string | null;
    space_tersedia: string;
    penangkal_petir: string;
    detail_lokasi_antena: string | null;
    tinggi_penangkal_petir: string | null;
    akses_di_lokasi_perlu_alat_bantu: string;
}

interface SarpenRuangServerData {
    ups: string;
    dua_ruang: string;
    satu_lantai: string | null;
    suhu_ruangan: string;
    ruangan_ber_ac: string;
    grounding_listrik: string;
    perangkat_pelanggan: string;
    power_line___listrik: string | null;
    info_kelistrikan_pln_n_g: string | null;
    info_kelistrikan_pln_p_g: string | null;
    info_kelistrikan_pln_p_n: string | null;
    ketersediaan_power_outlet_untuk_otb_modem_dan_router: string;
}

interface PenempatanPerangkatData {
    kesiapan_ruang_server: string;
    ketersediaan_rak_server: string;
    space_modem_dan_router: string;
    lokasi_penempatan_modem_dan_router: string | null;
    diizinkan_foto_ruang_server_pelanggan: string;
}

interface PerizinanBiayaGedungData {
    pic_bm: string | null;
    supervisi: string | null;
    biaya_sewa: string | null;
    biaya_lain: string | null;
    deposit_kerja: string | null;
    info_lain__lain_jika_ada: string | null;
    ikg_instalasi_kabel_gedung: string | null;
    material_dan_infrastruktur: string;
    panjang_kabel_dalam_gedung: string | null;
    waktu_pelaksanaan_penarikan_kabel: string | null;
    pelaksana_penarikan_kabel_dalam_gedung: string | null;
}

interface PerizinanBiayaKawasanData {
    supervisi: string | null;
    biaya_sewa: string | null;
    pic_kawasan: string | null;
    nama_kawasan: string | null;
    biaya_lain: string | null;
    deposit_kerja: string | null;
    kontak_pic_kawasan: string | null;
    melewati_kawasan_private: string;
    info_lain___lain_jika_ada: string | null;
    panjang_kabel_dalam_kawasan: string | null;
    biaya_penarikan_kabel_dalam_kawasan: string | null;
    pelaksana_penarikan_kabel_dalam_kawasan: string | null;
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
                list_item?: ListItemsData;
                pelanggan?: PelangganData;
                pelaksanaan?: PelaksanaanData;
                berita_acara?: BeritaAcaraData;
                pekerja_cabut?: PekerjaCabutData;
                kawasan_umum?: KawasanUmumData;
                informasi_gedung?: InformasiGedungData;
                pelaksanan_berita_acara?: PelaksanaanData;
                splitter?: SplitterData;
                lokasi_antena?: LokasiAntenaData;
                hh_eksisting?: HHEksistingData;
                penempatan_perangkat?: PenempatanPerangkatData;
                sarpen_ruang_server?: SarpenRuangServerData;
                perizinan_biaya_gedung?: PerizinanBiayaGedungData;
                perizinan_biaya_kawasan?: PerizinanBiayaKawasanData;
                data_hh_baru?: HHBaruData;
                data_hh_eksisting?: HHEksistingData;
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
                                        <DetailItem label="No. MR" value={parsedData.spk.no_mr} />
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
                                        <DetailItem label="Pop" value={parsedData.jaringan.pop} />
                                        <DetailItem label="Jasa" value={parsedData.jaringan.jasa} />
                                        <DetailItem label="IP LAN" value={parsedData.jaringan.ip_lan} />
                                        <DetailItem label="No FMB" value={parsedData.jaringan.no_fmb} />
                                        <DetailItem label="Kecepatan" value={parsedData.jaringan.kecepatan} />
                                        <DetailItem label="Tanggal RFS LA" value={parsedData.jaringan.tgl_rfs_la} />
                                        <DetailItem label="Media Akses" value={parsedData.jaringan.media_akses} />
                                        <DetailItem label="No. Jaringan" value={parsedData.jaringan.no_jaringan} />
                                        <DetailItem label="Opsi Router" value={parsedData.jaringan.opsi_router} />
                                        <DetailItem label="Tanggal RFS PLG" value={parsedData.jaringan.tgl_rfs_plg} />
                                        <DetailItem label="Kode Jaringan" value={parsedData.jaringan.kode_jaringan} />
                                        <DetailItem label="Manage Router" value={parsedData.jaringan.manage_router} />
                                        <DetailItem label="Jenis Aktivasi" value={parsedData.jaringan.jenis_aktivasi} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card List Items */}
                            {parsedData.list_item && Object.keys(parsedData.list_item).length > 0 && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Package className="h-5 w-5" />
                                            List Items
                                        </CardTitle>
                                        <CardDescription>Daftar peralatan - {Object.keys(parsedData.list_item).length} item</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {Object.entries(parsedData.list_item).map(([itemKey, itemData], index) => (
                                            <div key={itemKey} className="rounded-lg border border-primary/20 bg-primary/5 p-4">
                                                <div className="mb-3 flex items-center gap-2">
                                                    <Badge variant="outline" className="font-semibold">
                                                        {itemKey.replace(/_/g, ' ').toUpperCase()}
                                                    </Badge>
                                                    <span className="text-sm text-muted-foreground">Item {index + 1}</span>
                                                </div>
                                                <div className="grid gap-3 md:grid-cols-2">
                                                    <DetailItem label="Kode" value={itemData.kode} />
                                                    <DetailItem label="Deskripsi" value={itemData.deskripsi} />
                                                </div>
                                            </div>
                                        ))}
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

                            {/* Card HH Baru */}
                            {parsedData.data_hh_baru && Object.keys(parsedData.data_hh_baru).length > 0 && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Network className="h-5 w-5" />
                                            Data HH Baru
                                        </CardTitle>
                                        <CardDescription>
                                            Informasi Handhole baru - {Object.keys(parsedData.data_hh_baru).length} lokasi
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {Object.entries(parsedData.data_hh_baru).map(([hhKey, hhData], index) => (
                                            <div key={hhKey} className="rounded-lg border border-primary/20 bg-primary/5 p-4">
                                                <div className="mb-3 flex items-center gap-2">
                                                    <Badge variant="outline" className="font-semibold">
                                                        {hhKey.replace(/_/g, ' ').toUpperCase()}
                                                    </Badge>
                                                    <span className="text-sm text-muted-foreground">Lokasi {index + 1}</span>
                                                </div>
                                                <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                                                    <DetailItem label="Lokasi" value={hhData.lokasi_hh_1} />
                                                    <DetailItem label="Kapasitas Closure" value={hhData.kapasitas_closure_1} />
                                                    <DetailItem label="Koordinat (Longitude, Latitude)" value={hhData.longitude_dan_latitude_hh_1} />
                                                    <DetailItem label="Kebutuhan Penambahan Closure" value={hhData.kebutuhan_penambahan_closure_1} />
                                                </div>
                                            </div>
                                        ))}
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card HH Eksisting */}
                            {parsedData.data_hh_eksisting && Object.keys(parsedData.data_hh_eksisting).length > 0 && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Network className="h-5 w-5" />
                                            Data HH Eksisting
                                        </CardTitle>
                                        <CardDescription>
                                            Informasi Handhole eksisting - {Object.keys(parsedData.data_hh_eksisting).length} lokasi
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {Object.entries(parsedData.data_hh_eksisting).map(([hhKey, hhData], index) => (
                                            <div key={hhKey} className="rounded-lg border border-primary/20 bg-primary/5 p-4">
                                                <div className="mb-3 flex items-center gap-2">
                                                    <Badge variant="outline" className="font-semibold">
                                                        {hhKey.replace(/_/g, ' ').toUpperCase()}
                                                    </Badge>
                                                    <span className="text-sm text-muted-foreground">Lokasi {index + 1}</span>
                                                </div>
                                                <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                                                    <DetailItem label="Lokasi" value={hhData.lokasi_hh_1} />
                                                    <DetailItem label="Kondisi HH" value={hhData.kondisi_hh_1} />
                                                    <DetailItem label="Kondisi Closure" value={hhData.kondisi_closure_1} />
                                                    <DetailItem label="Kapasitas Closure" value={hhData.kapasitas_closure_1} />
                                                    <DetailItem label="Ketersediaan Closure" value={hhData.ketersediaan_closure_1} />
                                                    <DetailItem label="Koordinat (Longitude, Latitude)" value={hhData.longitude_dan_latitude_hh_1} />
                                                </div>
                                            </div>
                                        ))}
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Kawasan Umum */}
                            {parsedData.kawasan_umum && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Building2 className="h-5 w-5" />
                                            Kawasan Umum
                                        </CardTitle>
                                        <CardDescription>Detail kawasan umum</CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-3 md:grid-cols-3">
                                            <DetailItem
                                                label="Nama Kawasan Umum / PU yang Dilewati"
                                                value={parsedData.kawasan_umum.nama_kawasan_umum__pu_yang_dilewati}
                                            />
                                            <DetailItem
                                                label="Panjang Jalur Outdoor di Kawasan Umum"
                                                value={parsedData.kawasan_umum.panjang__jalur_outdoor_di_kawasan_umum}
                                            />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

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
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Pekerja Cabut */}
                            {parsedData.pekerja_cabut && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Building2 className="h-5 w-5" />
                                            Pekerja Cabut
                                        </CardTitle>
                                        <CardDescription>Detail pekerja cabut</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Nama Pelanggan" value={parsedData.pekerja_cabut.teknisi} />
                                        <DetailItem label="Lokasi" value={parsedData.pekerja_cabut.nama_vendor} />
                                        <DetailItem label="Kontak Person" value={parsedData.pekerja_cabut.pic_pelanggan} />
                                        <DetailItem label="Telepon" value={parsedData.pekerja_cabut.kontak_pic_pelanggan} />
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
                                        <DetailItem label="Biaya IKG" value={parsedData.informasi_gedung.biaya_ikg} />
                                        <DetailItem label="Pelanggan FO" value={parsedData.informasi_gedung.pelanggan_fo} />
                                        <DetailItem label="Telepon/Fax" value={parsedData.informasi_gedung.telpon___fax} />
                                        <DetailItem label="Kontak Person" value={parsedData.informasi_gedung.kontak_person} />
                                        <DetailItem label="Status Gedung" value={parsedData.informasi_gedung.status_gedung} />
                                        <DetailItem label="Kondisi Gedung" value={parsedData.informasi_gedung.kondisi_gedung} />
                                        <DetailItem label="Bagian Jabatan" value={parsedData.informasi_gedung.bagian___jabatan} />
                                        <DetailItem label="Pemilik Bangunan" value={parsedData.informasi_gedung.pemilik_bangunan} />
                                        <DetailItem label="Sewa Shaft Kabel" value={parsedData.informasi_gedung.sewa_shaft_kabel} />
                                        <DetailItem label="Penempatan Antena" value={parsedData.informasi_gedung.penempatan_antena} />
                                        <DetailItem label="Sewa Space Antena" value={parsedData.informasi_gedung.sewa_space_antena} />
                                        <DetailItem label="Jumlah Lantai Gedung" value={parsedData.informasi_gedung.jumlah_lantai_gedung} />
                                        <DetailItem label="Penanggungjawab Sewa" value={parsedData.informasi_gedung.penanggungjawab_sewa} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Splitter */}
                            {parsedData.splitter && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Wifi className="h-5 w-5" />
                                            Data Splitter
                                        </CardTitle>
                                        <CardDescription>Informasi splitter fiber optic</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="ID Splitter" value={parsedData.splitter.id_splitter} />
                                        <DetailItem label="Lokasi Splitter" value={parsedData.splitter.lokasi_splitter} />
                                        <DetailItem label="Kapasitas Splitter" value={parsedData.splitter.kapasitas_splitter} />
                                        <DetailItem label="Arah Akses" value={parsedData.splitter.arah_akses} />
                                        <DetailItem label="List Port Kosong" value={parsedData.splitter.list_port_kosong} />
                                        <DetailItem label="Port Kosong & Redaman" value={parsedData.splitter.list_port_kosong_dan_redaman} />
                                        <DetailItem
                                            label="Nama Node (jika tidak ada splitter)"
                                            value={parsedData.splitter.nama_node_jika_tidak_ada_splitter}
                                        />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Lokasi Antena */}
                            {parsedData.lokasi_antena && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Antenna className="h-5 w-5" />
                                            Lokasi Antena
                                        </CardTitle>
                                        <CardDescription>Detail penempatan antena</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Tower/Pole" value={parsedData.lokasi_antena.tower___pole} />
                                        <DetailItem label="Lokasi Antena" value={parsedData.lokasi_antena.lokasi_antena} />
                                        <DetailItem label="Detail Lokasi Antena" value={parsedData.lokasi_antena.detail_lokasi_antena} />
                                        <DetailItem label="Space Tersedia" value={parsedData.lokasi_antena.space_tersedia} />
                                        <DetailItem label="Penangkal Petir" value={parsedData.lokasi_antena.penangkal_petir} />
                                        <DetailItem label="Tinggi Penangkal Petir" value={parsedData.lokasi_antena.tinggi_penangkal_petir} />
                                        <DetailItem
                                            label="Akses Perlu Alat Bantu"
                                            value={parsedData.lokasi_antena.akses_di_lokasi_perlu_alat_bantu}
                                        />
                                        <DetailItem label="Tindak Lanjut" value={parsedData.lokasi_antena.tindak_lanjut} />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Sarpen Ruang Server */}
                            {parsedData.sarpen_ruang_server && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Server className="h-5 w-5" />
                                            Sarpen Ruang Server
                                        </CardTitle>
                                        <CardDescription>Sarana pendukung ruang server</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-3">
                                        <DetailItem label="Ruangan Ber-AC" value={parsedData.sarpen_ruang_server.ruangan_ber_ac} />
                                        <DetailItem label="Suhu Ruangan" value={parsedData.sarpen_ruang_server.suhu_ruangan} />
                                        <DetailItem label="UPS" value={parsedData.sarpen_ruang_server.ups} />
                                        <DetailItem label="Grounding Listrik" value={parsedData.sarpen_ruang_server.grounding_listrik} />
                                        <DetailItem label="Power Line/Listrik" value={parsedData.sarpen_ruang_server.power_line___listrik} />
                                        <DetailItem
                                            label="Ketersediaan Power Outlet"
                                            value={parsedData.sarpen_ruang_server.ketersediaan_power_outlet_untuk_otb_modem_dan_router}
                                        />
                                        <DetailItem label="Perangkat Pelanggan" value={parsedData.sarpen_ruang_server.perangkat_pelanggan} />
                                        <DetailItem label="Satu Lantai" value={parsedData.sarpen_ruang_server.satu_lantai} />
                                        <DetailItem label="Dua Ruang" value={parsedData.sarpen_ruang_server.dua_ruang} />
                                        <DetailItem
                                            label="Info Kelistrikan PLN (P-N)"
                                            value={parsedData.sarpen_ruang_server.info_kelistrikan_pln_p_n}
                                        />
                                        <DetailItem
                                            label="Info Kelistrikan PLN (P-G)"
                                            value={parsedData.sarpen_ruang_server.info_kelistrikan_pln_p_g}
                                        />
                                        <DetailItem
                                            label="Info Kelistrikan PLN (N-G)"
                                            value={parsedData.sarpen_ruang_server.info_kelistrikan_pln_n_g}
                                        />
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Penempatan Perangkat */}
                            {parsedData.penempatan_perangkat && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <Building2 className="h-5 w-5" />
                                            Penempatan Perangkat
                                        </CardTitle>
                                        <CardDescription>Detail penempatan perangkat</CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-3 md:grid-cols-3">
                                            <DetailItem label="Kesiapan Ruang Server" value={parsedData.penempatan_perangkat.kesiapan_ruang_server} />
                                            <DetailItem
                                                label="Ketersediaan Rak Server"
                                                value={parsedData.penempatan_perangkat.ketersediaan_rak_server}
                                            />
                                            <DetailItem
                                                label="Space Modem dan Router"
                                                value={parsedData.penempatan_perangkat.space_modem_dan_router}
                                            />
                                            <DetailItem
                                                label="Lokasi Penempatan Modem dan Router"
                                                value={parsedData.penempatan_perangkat.lokasi_penempatan_modem_dan_router}
                                            />
                                            <DetailItem
                                                label="Diizinkan Foto Ruang Server Pelanggan"
                                                value={parsedData.penempatan_perangkat.diizinkan_foto_ruang_server_pelanggan}
                                            />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {/* Card Perizinan Biaya Gedung (Full Width) */}
                            {parsedData.perizinan_biaya_gedung && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2">
                                            <DollarSign className="h-5 w-5" />
                                            Perizinan & Biaya Gedung
                                        </CardTitle>
                                        <CardDescription>Detail perizinan dan biaya instalasi gedung</CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-3 md:grid-cols-3">
                                            <DetailItem
                                                label="Material & Infrastruktur"
                                                value={parsedData.perizinan_biaya_gedung.material_dan_infrastruktur}
                                            />
                                            <DetailItem
                                                label="IKG (Instalasi Kabel Gedung)"
                                                value={parsedData.perizinan_biaya_gedung.ikg_instalasi_kabel_gedung}
                                            />
                                            <DetailItem
                                                label="Panjang Kabel Dalam Gedung"
                                                value={parsedData.perizinan_biaya_gedung.panjang_kabel_dalam_gedung}
                                            />
                                            <DetailItem
                                                label="Pelaksana Penarikan Kabel"
                                                value={parsedData.perizinan_biaya_gedung.pelaksana_penarikan_kabel_dalam_gedung}
                                            />
                                            <DetailItem
                                                label="Waktu Pelaksanaan"
                                                value={parsedData.perizinan_biaya_gedung.waktu_pelaksanaan_penarikan_kabel}
                                            />
                                            <DetailItem label="Biaya Sewa" value={parsedData.perizinan_biaya_gedung.biaya_sewa} />
                                            <DetailItem label="Deposit Kerja" value={parsedData.perizinan_biaya_gedung.deposit_kerja} />
                                            <DetailItem label="Biaya Lain" value={parsedData.perizinan_biaya_gedung.biaya_lain} />
                                            <DetailItem label="Supervisi" value={parsedData.perizinan_biaya_gedung.supervisi} />
                                            <DetailItem label="PIC BM" value={parsedData.perizinan_biaya_gedung.pic_bm} />
                                            <DetailItem label="Info Lain-lain" value={parsedData.perizinan_biaya_gedung.info_lain__lain_jika_ada} />
                                        </div>
                                    </CardContent>
                                </Card>
                            )}
                        </div>

                        {/* Card Perizinan Biaya Kawasan (Full Width) */}
                        {parsedData.perizinan_biaya_kawasan && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Building2 className="h-5 w-5" />
                                        Perizinan & Biaya Kawasan
                                    </CardTitle>
                                    <CardDescription>Detail perizinan dan biaya penarikan kabel kawasan</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid gap-3 md:grid-cols-3">
                                        <DetailItem
                                            label="Melewati Kawasan Private"
                                            value={parsedData.perizinan_biaya_kawasan.melewati_kawasan_private}
                                        />
                                        <DetailItem label="Nama Kawasan" value={parsedData.perizinan_biaya_kawasan.nama_kawasan} />
                                        <DetailItem label="PIC Kawasan" value={parsedData.perizinan_biaya_kawasan.pic_kawasan} />
                                        <DetailItem label="Kontak PIC Kawasan" value={parsedData.perizinan_biaya_kawasan.kontak_pic_kawasan} />
                                        <DetailItem
                                            label="Panjang Kabel Dalam Kawasan"
                                            value={parsedData.perizinan_biaya_kawasan.panjang_kabel_dalam_kawasan}
                                        />
                                        <DetailItem
                                            label="Pelaksana Penarikan Kabel"
                                            value={parsedData.perizinan_biaya_kawasan.pelaksana_penarikan_kabel_dalam_kawasan}
                                        />
                                        <DetailItem
                                            label="Biaya Penarikan Kabel"
                                            value={parsedData.perizinan_biaya_kawasan.biaya_penarikan_kabel_dalam_kawasan}
                                        />
                                        <DetailItem label="Biaya Sewa" value={parsedData.perizinan_biaya_kawasan.biaya_sewa} />
                                        <DetailItem label="Deposit Kerja" value={parsedData.perizinan_biaya_kawasan.deposit_kerja} />
                                        <DetailItem label="Biaya Lain" value={parsedData.perizinan_biaya_kawasan.biaya_lain} />
                                        <DetailItem label="Supervisi" value={parsedData.perizinan_biaya_kawasan.supervisi} />
                                        <DetailItem label="Info Lain-lain" value={parsedData.perizinan_biaya_kawasan.info_lain___lain_jika_ada} />
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
