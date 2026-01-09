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
    MapPin,
    Network,
    Package,
    Server,
    Wrench,
} from 'lucide-react';
import { useEffect, useState } from 'react';

// Import all interfaces from Detail.tsx (atau buat shared types file)
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
    jenis_instalasi: string | null;
}

interface PelangganData {
    telepon: string | null;
    kontak_person: string | null;
    nama_pelanggan: string;
    lokasi_pelanggan: string;
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

interface PenempatanPerangkatData {
    kesiapan_ruang_server: string;
    ketersediaan_rak_server: string;
    space_modem_dan_router: string;
    lokasi_penempatan_modem_dan_router: string | null;
    diizinkan_foto_ruang_server_pelanggan: string;
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

interface KawasanUmumData {
    nama_kawasan_umum__pu_yang_dilewati: string | null;
    panjang__jalur_outdoor_di_kawasan_umum: string | null;
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

interface BOQItem {
    paket?: string | null;
    harga_rp?: string | null;
    media_group?: string | null;
    item_type?: string | null;
    detail?: string | null;
    dist_paket?: string | null;
    jasa?: string | null;
    material?: string | null;
    [key: string]: string | null | undefined;
}

interface BOQData {
    items?: { [key: string]: BOQItem };
    total?: string | null;
}

interface HHEksistingItem {
    lokasi_hh_1?: string | null;
    kondisi_hh_1?: string | null;
    kondisi_closure_1?: string | null;
    kapasitas_closure_1?: string | null;
    ketersediaan_closure_1?: string | null;
    longitude_dan_latitude_hh_1?: string | null;
    [key: string]: string | null | undefined;
}

interface HHEksistingData {
    [key: string]: HHEksistingItem;
}

interface HHBaruItem {
    lokasi_hh_1?: string | null;
    kapasitas_closure_1?: string | null;
    longitude_dan_latitude_hh_1?: string | null;
    kebutuhan_penambahan_closure_1?: string | null;
    [key: string]: string | null | undefined;
}

interface HHBaruData {
    [key: string]: HHBaruItem;
}

interface DokumentasiItem {
    jenis: string;
    patch_foto: string;
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

interface ExtractedData {
    data: {
        parsed: {
            data: {
                spk?: SPKData;
                vendor?: VendorData;
                jaringan?: JaringanData;
                pelanggan?: PelangganData;
                informasi_gedung?: InformasiGedungData;
                sarpen_ruang_server?: SarpenRuangServerData;
                lokasi_antena?: LokasiAntenaData;
                perizinan_biaya_gedung?: PerizinanBiayaGedungData;
                penempatan_perangkat?: PenempatanPerangkatData;
                perizinan_biaya_kawasan?: PerizinanBiayaKawasanData;
                kawasan_umum?: KawasanUmumData;
                splitter?: SplitterData;
                hh_eksisting?: HHEksistingData;
                data_hh_eksisting?: HHEksistingData;
                hh_baru?: HHBaruData;
                boq?: BOQData;
                data_hh_baru?: HHBaruData;
                berita_acara?: BeritaAcaraData;
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

export default function SPKSurveyDetail({ upload: initialUpload, extractedData: initialExtractedData }: DetailProps) {
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

    const DetailItem = ({ label, value }: { label: string; value: string | null | undefined }) => {
        if (!value) return null;
        return (
            <div className="flex flex-col gap-1 wrap-break-word">
                <span className="text-sm text-muted-foreground">{label}</span>
                <span className="text-sm font-medium wrap-break-word">{value}</span>
            </div>
        );
    };

    const SectionHeader = ({ title, icon: Icon }: { title: string; icon: any }) => {
        return (
            <div className="mb-4 flex items-center gap-3">
                <div className="flex items-center gap-2">
                    <Icon className="h-6 w-6 text-primary" />
                    <h2 className="text-2xl font-bold">{title}</h2>
                </div>
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
                    </div>
                );

            case 'processing':
                return (
                    <div className="py-12 text-center">
                        <div className="mx-auto mb-4 h-16 w-16 animate-spin rounded-full border-4 border-yellow-500 border-t-transparent"></div>
                        <p className="text-muted-foreground">Dokumen sedang diproses...</p>
                    </div>
                );

            case 'failed':
                return (
                    <div className="py-12 text-center">
                        <AlertCircle className="mx-auto mb-4 h-16 w-16 text-red-500" />
                        <p className="font-semibold text-red-400">Proses dokumen gagal</p>
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
                    <div className="space-y-8">
                        {/* Header Info */}
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold">SPK Survey - Data Extracted</h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Tipe: <Badge variant="outline">Survey</Badge>
                                    {parsed.metadata && (
                                        <Badge variant="outline" className="ml-2 text-xs">
                                            Confidence: {parsed.metadata.detection_confidence}
                                        </Badge>
                                    )}
                                </p>
                            </div>
                        </div>

                        {/* ========== DATA SPK ========== */}
                        {(parsedData.spk || parsedData.jaringan || parsedData.pelanggan) && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA SPK" icon={FileText} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Informasi SPK</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        {parsedData.spk && (
                                            <div className="mb-6 border-b pb-6">
                                                <div className="grid gap-3 md:grid-cols-4">
                                                    <DetailItem label="No. MR" value={parsedData.spk.no_mr} />
                                                    <DetailItem label="No. SPK" value={parsedData.spk.no_spk} />
                                                    <DetailItem label="Tipe SPK" value={parsedData.spk.tipe_spk} />
                                                    <DetailItem label="Tanggal SPK" value={parsedData.spk.tanggal_spk} />
                                                </div>
                                            </div>
                                        )}
                                        <div className="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <div className="mb-3 font-semibold text-primary">Data Pelanggan</div>
                                                <div className="space-y-3">
                                                    <DetailItem label="Nama Pelanggan" value={parsedData.pelanggan?.nama_pelanggan} />
                                                    <DetailItem label="No. Jaringan" value={parsedData.jaringan?.no_jaringan} />
                                                    <DetailItem label="Lokasi" value={parsedData.pelanggan?.lokasi_pelanggan} />
                                                </div>
                                            </div>
                                            <div>
                                                <div className="mb-3 font-semibold text-primary">Data Layanan</div>
                                                <div className="space-y-3">
                                                    <DetailItem label="Jasa" value={parsedData.jaringan?.jasa} />
                                                    <DetailItem label="Kecepatan" value={parsedData.jaringan?.kecepatan} />
                                                    <DetailItem label="Media Akses" value={parsedData.jaringan?.media_akses} />
                                                </div>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== DATA UMUM PELANGGAN ========== */}
                        {(parsedData.pelanggan || parsedData.vendor) && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA UMUM PELANGGAN" icon={Building2} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Informasi Lengkap Pelanggan</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-3">
                                                <DetailItem label="Nama Pelanggan" value={parsedData.pelanggan?.nama_pelanggan} />
                                                <DetailItem label="Alamat" value={parsedData.pelanggan?.lokasi_pelanggan} />
                                                <DetailItem
                                                    label="Koordinat"
                                                    value={
                                                        parsedData.vendor?.latitude && parsedData.vendor?.longitude
                                                            ? `${parsedData.vendor.latitude}, ${parsedData.vendor.longitude}`
                                                            : null
                                                    }
                                                />
                                            </div>
                                            <div className="space-y-3">
                                                <DetailItem label="Jenis Layanan" value={parsedData.jaringan?.jasa} />
                                                <DetailItem label="Bandwidth" value={parsedData.jaringan?.kecepatan} />
                                                <DetailItem label="PIC Pelanggan" value={parsedData.vendor?.pic_pelanggan} />
                                                <DetailItem label="Vendor" value={parsedData.vendor?.nama_vendor} />
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== INFORMASI GEDUNG ========== */}
                        {parsedData.informasi_gedung && (
                            <div className="space-y-4">
                                <SectionHeader title="INFORMASI GEDUNG PELANGGAN" icon={Building2} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Detail Gedung</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem label="Status Gedung" value={parsedData.informasi_gedung.status_gedung} />
                                            <DetailItem label="Kondisi Gedung" value={parsedData.informasi_gedung.kondisi_gedung} />
                                            <DetailItem label="Alamat" value={parsedData.informasi_gedung.alamat} />
                                            <DetailItem label="Kontak Person" value={parsedData.informasi_gedung.kontak_person} />
                                            <DetailItem label="Telpon / Fax" value={parsedData.informasi_gedung.telpon___fax} />
                                            <DetailItem label="Jumlah Lantai" value={parsedData.informasi_gedung.jumlah_lantai_gedung} />
                                            <DetailItem label="Pelanggan bersedia dipasang perangkat" value={parsedData.informasi_gedung.pelanggan_fo} />
                                            <DetailItem label="Penempatan antena" value={parsedData.informasi_gedung.penempatan_antena} />
                                            <DetailItem label="Sewa space antena" value={parsedData.informasi_gedung.sewa_space_antena} />
                                            <DetailItem label="Sewa shaft kabel" value={parsedData.informasi_gedung.sewa_shaft_kabel} />
                                            <DetailItem label="Biaya IKG" value={parsedData.informasi_gedung.biaya_ikg} />
                                            <DetailItem label="Penanggungjawab pengurusan dan biaya sewa" value={parsedData.informasi_gedung.penanggungjawab_sewa} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== SARPEN RUANG SERVER ========== */}
                        {parsedData.sarpen_ruang_server && (
                            <div className="space-y-4">
                                <SectionHeader title="INFORMASI SARPEN DAN RUANG SERVER PELANGGAN" icon={Server} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Sarana Pendukung Ruang Server</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem
                                                label="Power Line / Listrik"
                                                value={parsedData.sarpen_ruang_server.power_line___listrik}
                                            />
                                            <DetailItem
                                                label="Ketersediaan Power Outlet"
                                                value={parsedData.sarpen_ruang_server.ketersediaan_power_outlet_untuk_otb_modem_dan_router}
                                            />
                                            <DetailItem
                                                label="Info Kelistrikan (PLN P-N)"
                                                value={parsedData.sarpen_ruang_server.info_kelistrikan_pln_p_n}
                                            />
                                            <DetailItem
                                                label="Info Kelistrikan (PLN P-G)"
                                                value={parsedData.sarpen_ruang_server.info_kelistrikan_pln_p_g}
                                            />
                                            <DetailItem
                                                label="Info Kelistrikan (PLN N-G)"
                                                value={parsedData.sarpen_ruang_server.info_kelistrikan_pln_n_g}
                                            />
                                            <DetailItem label="Grounding Listrik" value={parsedData.sarpen_ruang_server.grounding_listrik} />
                                            <DetailItem label="UPS" value={parsedData.sarpen_ruang_server.ups} />
                                            <DetailItem label="Ruangan Ber AC" value={parsedData.sarpen_ruang_server.ruangan_ber_ac} />
                                            <DetailItem label="Suhu Ruangan" value={parsedData.sarpen_ruang_server.suhu_ruangan} />
                                            <DetailItem label="Lantai" value={parsedData.sarpen_ruang_server.satu_lantai} />
                                            <DetailItem label="Ruang" value={parsedData.sarpen_ruang_server.dua_ruang} />
                                            <DetailItem label="Perangkat Pelanggan" value={parsedData.sarpen_ruang_server.perangkat_pelanggan} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== LOKASI ANTENA ========== */}
                        {parsedData.lokasi_antena && (
                            <div className="space-y-4">
                                <SectionHeader title="INFORMASI LOKASI ANTENA" icon={Antenna} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Detail Lokasi Antena</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem label="Lokasi Antena" value={parsedData.lokasi_antena.lokasi_antena} />
                                            <DetailItem label="Detail Lokasi" value={parsedData.lokasi_antena.detail_lokasi_antena} />
                                            <DetailItem label="Space Tersedia" value={parsedData.lokasi_antena.space_tersedia} />
                                            <DetailItem
                                                label="Akses Perlu Alat Bantu"
                                                value={parsedData.lokasi_antena.akses_di_lokasi_perlu_alat_bantu}
                                            />
                                            <DetailItem label="Penangkal Petir" value={parsedData.lokasi_antena.penangkal_petir} />
                                            <DetailItem label="Tinggi Penangkal Petir" value={parsedData.lokasi_antena.tinggi_penangkal_petir} />
                                            <DetailItem label="Tower / Pole" value={parsedData.lokasi_antena.tower___pole} />
                                            <DetailItem label="Tindak Lanjut" value={parsedData.lokasi_antena.tindak_lanjut} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== PERIZINAN BIAYA GEDUNG ========== */}
                        {parsedData.perizinan_biaya_gedung && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA PERIZINAN DAN BIAYA DALAM GEDUNG" icon={DollarSign} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Perizinan & Biaya Gedung</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem label="PIC BM" value={parsedData.perizinan_biaya_gedung.pic_bm} />
                                            <DetailItem
                                                label="Material dan Infrastruktur"
                                                value={parsedData.perizinan_biaya_gedung.material_dan_infrastruktur}
                                            />
                                            <DetailItem
                                                label="Panjang Kabel dalam Gedung"
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
                                            <DetailItem label="Supervisi" value={parsedData.perizinan_biaya_gedung.supervisi} />
                                            <DetailItem label="Deposit Kerja" value={parsedData.perizinan_biaya_gedung.deposit_kerja} />
                                            <DetailItem label="IKG" value={parsedData.perizinan_biaya_gedung.ikg_instalasi_kabel_gedung} />
                                            <DetailItem label="Biaya Sewa" value={parsedData.perizinan_biaya_gedung.biaya_sewa} />
                                            <DetailItem label="Biaya Lain" value={parsedData.perizinan_biaya_gedung.biaya_lain} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== PENEMPATAN PERANGKAT ========== */}
                        {parsedData.penempatan_perangkat && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA PENEMPATAN PERANGKAT DI LOKASI PELANGGAN" icon={Package} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Penempatan Perangkat</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem
                                                label="Lokasi Penempatan"
                                                value={parsedData.penempatan_perangkat.lokasi_penempatan_modem_dan_router}
                                            />
                                            <DetailItem label="Kesiapan Ruang Server" value={parsedData.penempatan_perangkat.kesiapan_ruang_server} />
                                            <DetailItem label="Ketersediaan Rak Server" value={parsedData.penempatan_perangkat.ketersediaan_rak_server} />
                                            <DetailItem label="Space Modem dan Router" value={parsedData.penempatan_perangkat.space_modem_dan_router} />
                                            <DetailItem
                                                label="Diizinkan Foto"
                                                value={parsedData.penempatan_perangkat.diizinkan_foto_ruang_server_pelanggan}
                                            />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== DOKUMENTASI PENEMPATAN ========== */}
                        {extractedData.data.dokumentasi && (
                            <div className="space-y-4">
                                <SectionHeader title="FOTO PENEMPATAN & JALUR KABEL" icon={Camera} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Dokumentasi Penempatan</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            {extractedData.data.dokumentasi
                                                .filter(
                                                    (doc) =>
                                                        doc.jenis.toLowerCase().includes('penempatan') ||
                                                        doc.jenis.toLowerCase().includes('jalur kabel'),
                                                )
                                                .map((doc, index) => (
                                                    <div key={index} className="space-y-2 rounded-lg border p-4">
                                                        <Badge variant="secondary" className="text-xs">
                                                            {doc.jenis}
                                                        </Badge>
                                                        <div className="relative aspect-video w-full overflow-hidden rounded-lg bg-muted">
                                                            <img
                                                                src={getImageUrl(doc.patch_foto)}
                                                                alt={doc.jenis}
                                                                className="h-full w-full object-cover"
                                                            />
                                                        </div>
                                                    </div>
                                                ))}
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== PERIZINAN KAWASAN ========== */}
                        {parsedData.perizinan_biaya_kawasan && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA PERIZINAN DAN BIAYA DALAM KAWASAN" icon={MapPin} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Perizinan & Biaya Kawasan</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem
                                                label="Melewati Kawasan Private"
                                                value={parsedData.perizinan_biaya_kawasan.melewati_kawasan_private}
                                            />
                                            <DetailItem label="Nama Kawasan" value={parsedData.perizinan_biaya_kawasan.nama_kawasan} />
                                            <DetailItem label="PIC Kawasan" value={parsedData.perizinan_biaya_kawasan.pic_kawasan} />
                                            <DetailItem label="Kontak PIC" value={parsedData.perizinan_biaya_kawasan.kontak_pic_kawasan} />
                                            <DetailItem
                                                label="Panjang Kabel"
                                                value={parsedData.perizinan_biaya_kawasan.panjang_kabel_dalam_kawasan}
                                            />
                                            <DetailItem
                                                label="Pelaksana Penarikan"
                                                value={parsedData.perizinan_biaya_kawasan.pelaksana_penarikan_kabel_dalam_kawasan}
                                            />
                                            <DetailItem
                                                label="Biaya Penarikan"
                                                value={parsedData.perizinan_biaya_kawasan.biaya_penarikan_kabel_dalam_kawasan}
                                            />
                                            <DetailItem label="Deposit Kerja" value={parsedData.perizinan_biaya_kawasan.deposit_kerja} />
                                            <DetailItem label="Biaya Sewa" value={parsedData.perizinan_biaya_kawasan.biaya_sewa} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== KAWASAN UMUM ========== */}
                        {parsedData.kawasan_umum && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA KAWASAN UMUM" icon={MapPin} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Informasi Kawasan Umum</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-2">
                                            <DetailItem
                                                label="Nama Kawasan Umum / PU yang Dilewati"
                                                value={parsedData.kawasan_umum.nama_kawasan_umum__pu_yang_dilewati}
                                            />
                                            <DetailItem
                                                label="Panjang Jalur Outdoor"
                                                value={parsedData.kawasan_umum.panjang__jalur_outdoor_di_kawasan_umum}
                                            />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== DATA SPLITTER ========== */}
                        {parsedData.splitter && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA SPLITTER" icon={Network} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Informasi Splitter</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-3">
                                            <DetailItem label="Lokasi Splitter" value={parsedData.splitter.lokasi_splitter} />
                                            <DetailItem label="ID Splitter" value={parsedData.splitter.id_splitter} />
                                            <DetailItem label="Kapasitas Splitter" value={parsedData.splitter.kapasitas_splitter} />
                                            <DetailItem label="List Port Kosong" value={parsedData.splitter.list_port_kosong} />
                                            <DetailItem
                                                label="Port Kosong & Redaman"
                                                value={parsedData.splitter.list_port_kosong_dan_redaman}
                                            />
                                            <DetailItem
                                                label="Nama Node (jika tidak ada)"
                                                value={parsedData.splitter.nama_node_jika_tidak_ada_splitter}
                                            />
                                            <DetailItem label="Arah Akses" value={parsedData.splitter.arah_akses} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== HH EKSISTING ========== */}
                        {parsedData.hh_eksisting && Object.keys(parsedData.hh_eksisting).length > 0 && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA HH EKSISTING YANG DIPAKAI" icon={MapPin} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Handhole Eksisting</CardTitle>
                                        <CardDescription>{Object.keys(parsedData.hh_eksisting).length} lokasi HH</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {Object.entries(parsedData.hh_eksisting).map(([hhKey, hhData], index) => (
                                            <div key={hhKey} className="rounded-lg border border-primary/20 bg-primary/5 p-4">
                                                <div className="mb-3">
                                                    <Badge variant="outline" className="font-semibold">
                                                        HH {index + 1}
                                                    </Badge>
                                                </div>
                                                <div className="grid gap-3 md:grid-cols-3">
                                                    <DetailItem label="Kondisi HH" value={hhData.kondisi_hh_1} />
                                                    <DetailItem label="Lokasi" value={hhData.lokasi_hh_1} />
                                                    <DetailItem label="Koordinat" value={hhData.longitude_dan_latitude_hh_1} />
                                                    <DetailItem label="Ketersediaan Closure" value={hhData.ketersediaan_closure_1} />
                                                    <DetailItem label="Kapasitas Closure" value={hhData.kapasitas_closure_1} />
                                                    <DetailItem label="Kondisi Closure" value={hhData.kondisi_closure_1} />
                                                </div>
                                            </div>
                                        ))}
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* ========== HH BARU ========== */}
                        {parsedData.hh_baru && Object.keys(parsedData.hh_baru).length > 0 && (
                            <div className="space-y-4">
                                <SectionHeader title="DATA HH BARU" icon={MapPin} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Rencana Handhole Baru</CardTitle>
                                        <CardDescription>{Object.keys(parsedData.hh_baru).length} lokasi HH baru</CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {Object.entries(parsedData.hh_baru).map(([hhKey, hhData], index) => (
                                            <div key={hhKey} className="rounded-lg border border-primary/20 bg-primary/5 p-4">
                                                <div className="mb-3">
                                                    <Badge variant="outline" className="font-semibold">
                                                        HH Baru {index + 1}
                                                    </Badge>
                                                </div>
                                                <div className="grid gap-3 md:grid-cols-2">
                                                    <DetailItem label="Lokasi" value={hhData.lokasi_hh_1} />
                                                    <DetailItem label="Koordinat" value={hhData.longitude_dan_latitude_hh_1} />
                                                    <DetailItem label="Kebutuhan Penambahan" value={hhData.kebutuhan_penambahan_closure_1} />
                                                    <DetailItem label="Kapasitas Closure" value={hhData.kapasitas_closure_1} />
                                                </div>
                                            </div>
                                        ))}
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                                                {/* ========== BOQ ========== */}
                        <div className="space-y-4">
                            <SectionHeader title="BOQ FO" icon={DollarSign} />
                            <Card>
                                <CardHeader>
                                    <CardTitle>Bill of Quantity</CardTitle>
                                    <CardDescription>JARINGAN: {parsedData.jaringan?.no_jaringan}</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {parsedData.boq && parsedData.boq.items && Object.keys(parsedData.boq.items).length > 0 ? (
                                        <div className="space-y-4">
                                            {/* Table Header */}
                                            <div className="overflow-x-auto">
                                                <table className="w-full border-collapse text-sm">
                                                    <thead>
                                                        <tr className="border-b bg-muted/50">
                                                            <th className="p-3 text-left font-semibold">Paket</th>
                                                            <th className="p-3 text-right font-semibold">Harga (Rp)</th>
                                                            <th className="p-3 text-left font-semibold">Media Group</th>
                                                            <th className="p-3 text-left font-semibold">Item Type</th>
                                                            <th className="p-3 text-left font-semibold">Detail</th>
                                                            <th className="p-3 text-left font-semibold">Dist (Paket)</th>
                                                            <th className="p-3 text-right font-semibold">Jasa</th>
                                                            <th className="p-3 text-right font-semibold">Material</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {Object.entries(parsedData.boq.items).map(([key, item], index) => (
                                                            <tr key={key} className="border-b hover:bg-muted/30">
                                                                <td className="p-3">{item.paket || '-'}</td>
                                                                <td className="p-3 text-right font-medium">
                                                                    {item.harga_rp ? Number(item.harga_rp).toLocaleString('id-ID') : '-'}
                                                                </td>
                                                                <td className="p-3">{item.media_group || '-'}</td>
                                                                <td className="p-3">{item.item_type || '-'}</td>
                                                                <td className="p-3">{item.detail || '-'}</td>
                                                                <td className="p-3">{item.dist_paket || '-'}</td>
                                                                <td className="p-3 text-right">
                                                                    {item.jasa ? Number(item.jasa).toLocaleString('id-ID') : '-'}
                                                                </td>
                                                                <td className="p-3 text-right">
                                                                    {item.material ? Number(item.material).toLocaleString('id-ID') : '-'}
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                    {parsedData.boq.total && (
                                                        <tfoot>
                                                            <tr className="border-t-2 bg-primary/10 font-bold">
                                                                <td className="p-3">Total</td>
                                                                <td className="p-3 text-right" colSpan={7}>
                                                                    Rp {Number(parsedData.boq.total).toLocaleString('id-ID')}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    )}
                                                </table>
                                            </div>
                                        </div>
                                    ) : (
                                        <p className="py-8 text-center text-sm text-muted-foreground italic">
                                            Data BOQ belum tersedia dalam ekstraksi saat ini.
                                        </p>
                                    )}
                                </CardContent>
                            </Card>
                        </div>

                        {/* ========== BERITA ACARA ========== */}
                        {parsedData.berita_acara && (
                            <div className="space-y-4">
                                <SectionHeader title="BERITA ACARA" icon={ClipboardList} />
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Berita Acara Survey</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="grid gap-4 md:grid-cols-2">
                                            <DetailItem label="Nomor SPK" value={parsedData.berita_acara.nomor_spk} />
                                            <DetailItem label="Tanggal" value={parsedData.berita_acara.tanggal} />
                                            <DetailItem label="Nama Pelanggan" value={parsedData.berita_acara.nama_pelanggan} />
                                            <DetailItem label="Lokasi" value={parsedData.berita_acara.lokasi_pelanggan} />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {/* Raw JSON */}
                        <details className="group">
                            <summary className="cursor-pointer text-sm text-muted-foreground hover:text-foreground">
                                📋 Lihat Raw JSON Data
                            </summary>
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
            <Head title={`SPK Survey Detail - ${upload.file_name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center gap-4">
                    <button onClick={goBack} className="rounded-lg p-2 transition-colors hover:bg-accent">
                        <ArrowLeft size={20} />
                    </button>
                    <h1 className="text-2xl font-bold">Detail SPK Survey</h1>

                    {isPolling && (
                        <Badge variant="outline" className="ml-auto">
                            <span className="mr-2 h-2 w-2 animate-pulse rounded-full bg-blue-400"></span>
                            Auto-refresh aktif
                        </Badge>
                    )}
                </div>

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

                <div>{renderContent()}</div>
            </div>
        </AppLayout>
    );
}