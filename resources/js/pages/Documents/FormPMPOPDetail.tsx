// FormPMPOPDetail.tsx - Detail page for all Form PM POP types
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { DynamicDataRenderer } from '@/components/FormPMPOP/DynamicDataRenderer';
import axios from 'axios';
import {
    Activity,
    AlertCircle,
    ArrowLeft,
    Battery,
    Building2,
    Calendar,
    Camera,
    CheckCircle2,
    ClipboardCheck,
    Download,
    FileText,
    HardDrive,
    Lightbulb,
    MapPin,
    Package,
    Server,
    Thermometer,
    Users,
    Wind,
    X,
    Zap,
    ZoomIn,
    ZoomOut,
} from 'lucide-react';
import React, { useEffect, useState } from 'react';

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

interface ChecklistItem {
    no: string;
    description: string;
    result?: string;
    standard?: string;
    status?: string;
    comment?: string;
    capacity?: string;
    threshold?: string;
    room_type?: string;
    measurement_label?: string;
    checklist?: any[];
    capacity_options?: any[];
}

interface BatteryBank {
    bank_number: string;
    bank_type: string;
    battery_type?: string;
    battery_brand?: string;
    end_device_batt?: string;
    voltage_soh_table?: Array<{
        no: string;
        voltage: string;
        soh: string;
    }>;
}

interface InventorySection {
    equipment: string;
    qty: string;
    status: string;
    bonding_ground?: string;
    keterangan?: string;
}

interface ExtractedData {
    data: {
        parsed: {
            data: {
                header?: {
                    no_dok?: string;
                    judul?: string;
                    versi?: string;
                    halaman?: string;
                    label?: string;
                };
                informasi_umum?: {
                    location?: string;
                    date_time?: string;
                    brand_type?: string;
                    reg_number?: string;
                    serial_number?: any;
                    capacity?: string;
                    kap_power_module?: string;
                    type_pole?: string;
                    type?: string;
                    battery_temperature?: string;
                    battery_bank?: string;
                    availability_ups?: string;
                };
                physical_check?: ChecklistItem[];
                visual_check?: ChecklistItem[];
                performance_check?: ChecklistItem[];
                performance_capacity_check?: ChecklistItem[];
                performance_measurement?: any;
                room_infrastructure?: ChecklistItem[];
                room_temperature?: {
                    no?: string;
                    description?: string;
                    checklist?: ChecklistItem[];
                };
                backup_tests?: ChecklistItem[];
                maksure_cable_connection?: ChecklistItem[];
                battery_banks?: BatteryBank[];
                measurement_test?: ChecklistItem[];
                inventory?: {
                    device_sentral?: InventorySection[];
                    supporting_facilities?: InventorySection[];
                };
                notes?: string;
                pelaksana?: {
                    executor?: Array<{
                        no: string;
                        Nama: string;
                        'Mitra / internal'?: string;
                    }>;
                    verifikator?: string;
                    head_of_sub_department?: string;
                };
            };
            metadata?: {
                parser_used: string;
                ocr_data_available: boolean;
                detection_confidence: string;
            };
            document_type: string;
        };
        dokumentasi?: DokumentasiItem[];
    };
}

interface FormPMPOPDetailProps {
    upload: Upload;
    extractedData: ExtractedData | null;
}

export default function FormPMPOPDetail({ upload: initialUpload, extractedData: initialExtractedData }: FormPMPOPDetailProps) {
    const [upload, setUpload] = useState(initialUpload);
    const [extractedData, setExtractedData] = useState(initialExtractedData);
    const [isPolling, setIsPolling] = useState(false);
    const [lightboxOpen, setLightboxOpen] = React.useState(false);
    const [lightboxImage, setLightboxImage] = React.useState<{ src: string; alt: string } | null>(null);
    const [zoom, setZoom] = React.useState(1);
    
    // Battery chart data state
    const [batteryChartData, setBatteryChartData] = useState<any>(null);
    const [isLoadingChartData, setIsLoadingChartData] = useState(false);

    // Polling effect for document status
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

    // Fetch battery chart data effect
    useEffect(() => {
        const isBatteryForm = extractedData?.data?.parsed?.document_type === 'form_pm_battery';
        const isCompleted = upload.status === 'completed';
        
        if (isBatteryForm && isCompleted && extractedData?.data?.parsed?.data) {
            fetchBatteryChartData();
        }
    }, [upload.status, extractedData]);

    // Lightbox keyboard effect
    React.useEffect(() => {
        const handleEsc = (e: KeyboardEvent) => {
            if (e.key === 'Escape') closeLightbox();
        };

        if (lightboxOpen) {
            window.addEventListener('keydown', handleEsc);
            document.body.style.overflow = 'hidden';
        }

        return () => {
            window.removeEventListener('keydown', handleEsc);
            document.body.style.overflow = 'unset';
        };
    }, [lightboxOpen]);

    /**
     * Fetch battery chart data from API
     */
    const fetchBatteryChartData = async () => {
        setIsLoadingChartData(true);
        
        try {
            const response = await axios.get(`/api/battery/chart-data-by-upload/${upload.id_upload}`);
            
            if (response.data.success) {
                setBatteryChartData(response.data.data);
                console.log(' Battery chart data loaded', {
                    upload_id: upload.id_upload,
                    banks_count: response.data.data.bank_names?.length || 0
                });
            }
        } catch (error: any) {
            console.error('Failed to fetch battery chart data:', error);
            // If failed, will fallback to table view (batteryChartData remains null)
        } finally {
            setIsLoadingChartData(false);
        }
    };

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
        router.visit(route('documents.filter', 'form-pm-pop'));
    };

    const openLightbox = (src: string, alt: string) => {
        setLightboxImage({ src, alt });
        setLightboxOpen(true);
        setZoom(1);
    };

    const closeLightbox = () => {
        setLightboxOpen(false);
        setLightboxImage(null);
        setZoom(1);
    };

    const handleZoomIn = () => {
        setZoom((prev) => Math.min(prev + 0.25, 3));
    };

    const handleZoomOut = () => {
        setZoom((prev) => Math.max(prev - 0.25, 0.5));
    };

    const handleDownload = () => {
        if (!lightboxImage) return;

        const link = document.createElement('a');
        link.href = lightboxImage.src;
        link.download = lightboxImage.alt || 'image.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const getImageUrl = (localPath: string) => {
        const placeholderSVG =
            'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0iIzI4MjgyOCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';

        if (!localPath) {
            return placeholderSVG;
        }

        let normalizedPath = localPath.replace(/\\/g, '/');
        normalizedPath = normalizedPath.startsWith('/') ? normalizedPath.substring(1) : normalizedPath;

        if (normalizedPath.startsWith('output/')) {
            normalizedPath = normalizedPath.substring(7);
        }

        return `/output/${normalizedPath}`;
    };

    const ImageWithFallback = ({ src, alt, className }: { src: string; alt: string; className?: string }) => {
        const [imgSrc, setImgSrc] = React.useState(src);
        const [hasError, setHasError] = React.useState(false);

        const placeholderSVG =
            'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0iIzI4MjgyOCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkltYWdlIE5vdCBGb3VuZDwvdGV4dD48L3N2Zz4=';

        const handleError = () => {
            if (!hasError) {
                console.error('Failed to load image:', src);
                setHasError(true);
                setImgSrc(placeholderSVG);
            }
        };

        return (
            <img
                src={imgSrc}
                alt={alt}
                className={`${className} cursor-pointer transition-opacity hover:opacity-80`}
                onError={handleError}
                loading="lazy"
                onClick={() => !hasError && openLightbox(imgSrc, alt)}
            />
        );
    };

    const DetailItem = ({ label, value }: { label: string; value: string | null | undefined }) => {
        if (!value) return null;
        return (
            <div className="flex flex-col gap-1">
                <span className="text-sm text-muted-foreground">{label}</span>
                <span className="text-sm font-medium">{value}</span>
            </div>
        );
    };

    // Get icon based on document type
    const getDocumentIcon = (docType: string) => {
        if (docType.includes('inverter')) return <Zap className="h-5 w-5 text-blue-500" />;
        if (docType.includes('battery')) return <Battery className="h-5 w-5 text-green-500" />;
        if (docType.includes('ac')) return <Wind className="h-5 w-5 text-cyan-500" />;
        if (docType.includes('rectifier')) return <Activity className="h-5 w-5 text-purple-500" />;
        if (docType.includes('shelter') || docType.includes('ruang')) return <Building2 className="h-5 w-5 text-orange-500" />;
        if (docType.includes('petir') || docType.includes('grounding')) return <Lightbulb className="h-5 w-5 text-yellow-500" />;
        if (docType.includes('kabel') || docType.includes('panel')) return <Zap className="h-5 w-5 text-pink-500" />;
        if (docType.includes('pole') || docType.includes('tower')) return <MapPin className="h-5 w-5 text-red-500" />;
        if (docType.includes('dokumentasi') || docType.includes('inventory')) return <Package className="h-5 w-5 text-indigo-500" />;
        return <FileText className="h-5 w-5 text-gray-500" />;
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
                        {isPolling && <p className="mt-2 text-sm text-muted-foreground"> Mengecek status otomatis setiap 3 detik</p>}
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
                                <h3 className="text-2xl font-bold">Form PM POP Data</h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Tipe:{' '}
                                    <Badge variant="outline" className="border-blue-500 text-blue-400">
                                        {parsed.document_type || 'Unknown'}
                                    </Badge>
                                    {parsedData.header?.no_dok && (
                                        <Badge variant="secondary" className="ml-2">
                                            {parsedData.header.no_dok}
                                        </Badge>
                                    )}
                                </p>
                            </div>
                            {parsed.metadata && (
                                <Badge variant="outline" className="text-xs">
                                    Parser: {parsed.metadata.parser_used}
                                </Badge>
                            )}
                        </div>

                        {/* Dynamic Data Renderer */}
                        <DynamicDataRenderer 
                            parsedData={parsedData} 
                            documentType={parsed.document_type}
                            batteryChartData={batteryChartData}
                        />

                        {/* Loading Indicator for Battery Chart Data */}
                        {isLoadingChartData && (
                            <Card className="border-blue-500/20 bg-blue-500/5">
                                <CardContent className="py-8 text-center">
                                    <div className="mx-auto mb-4 h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                                    <p className="text-sm text-muted-foreground">Loading battery chart data...</p>
                                </CardContent>
                            </Card>
                        )}

                        {/* Dokumentasi Foto */}
                        {extractedData.data.dokumentasi && extractedData.data.dokumentasi.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Camera className="h-5 w-5 text-blue-500" />
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
                                                <div className="relative aspect-video w-full overflow-hidden rounded-lg bg-muted">
                                                    <ImageWithFallback
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
                        )}

                        {/* Raw JSON Toggle */}
                        <details className="group">
                            <summary className="cursor-pointer text-sm text-muted-foreground hover:text-foreground">
                                Lihat Raw JSON Data
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
            <Head title={`Form PM POP Detail - ${upload.file_name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <button onClick={goBack} className="rounded-lg p-2 transition-colors hover:bg-accent">
                        <ArrowLeft size={20} />
                    </button>
                    <h1 className="text-2xl font-bold">Form PM POP Detail</h1>

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
                                             Selesai Diproses
                                        </Badge>
                                    )}
                                    {upload.status === 'processing' && (
                                        <Badge variant="outline" className="animate-pulse border-yellow-500 text-yellow-600">
                                             Sedang Diproses
                                        </Badge>
                                    )}
                                    {upload.status === 'uploaded' && (
                                        <Badge variant="outline" className="animate-pulse border-blue-500 text-blue-600">
                                            Menunggu Proses
                                        </Badge>
                                    )}
                                    {upload.status === 'failed' && (
                                        <Badge variant="outline" className="border-red-500 text-red-600">
                                            Gagal Diproses
                                        </Badge>
                                    )}
                                </div>
                            </div>

                            <button
                                onClick={openPDF}
                                className="flex items-center gap-2 rounded-lg bg-blue-500/10 px-4 py-2 text-blue-400 transition-colors hover:bg-blue-500/20"
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

            {/* Lightbox Modal */}
            {lightboxOpen && lightboxImage && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" onClick={closeLightbox}>
                    {/* Toolbar */}
                    <div className="absolute top-4 right-4 z-10 flex gap-2">
                        <button
                            onClick={(e) => {
                                e.stopPropagation();
                                handleZoomOut();
                            }}
                            className="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition-colors hover:bg-white/20"
                            title="Zoom Out"
                        >
                            <ZoomOut className="h-5 w-5 text-white" />
                        </button>
                        <button
                            onClick={(e) => {
                                e.stopPropagation();
                                handleZoomIn();
                            }}
                            className="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition-colors hover:bg-white/20"
                            title="Zoom In"
                        >
                            <ZoomIn className="h-5 w-5 text-white" />
                        </button>
                        <button
                            onClick={(e) => {
                                e.stopPropagation();
                                handleDownload();
                            }}
                            className="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition-colors hover:bg-white/20"
                            title="Download"
                        >
                            <Download className="h-5 w-5 text-white" />
                        </button>
                        <button
                            onClick={closeLightbox}
                            className="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition-colors hover:bg-white/20"
                            title="Close"
                        >
                            <X className="h-5 w-5 text-white" />
                        </button>
                    </div>

                    {/* Image Label */}
                    <div className="absolute top-4 left-4 z-10">
                        <div className="rounded-lg bg-white/10 px-4 py-2 backdrop-blur-sm">
                            <p className="text-sm font-medium text-white">{lightboxImage.alt}</p>
                        </div>
                    </div>

                    {/* Zoom Indicator */}
                    <div className="absolute bottom-4 left-4 z-10">
                        <div className="rounded-lg bg-white/10 px-3 py-1 backdrop-blur-sm">
                            <p className="text-xs text-white">{Math.round(zoom * 100)}%</p>
                        </div>
                    </div>

                    {/* Image Container */}
                    <div className="relative max-h-[90vh] max-w-[90vw] overflow-auto" onClick={(e) => e.stopPropagation()}>
                        <img
                            src={lightboxImage.src}
                            alt={lightboxImage.alt}
                            className="h-auto w-auto object-contain transition-transform duration-200"
                            style={{ transform: `scale(${zoom})` }}
                        />
                    </div>

                    {/* Close hint */}
                    <div className="absolute right-4 bottom-4">
                        <p className="text-sm text-white/60">Klik di luar gambar atau tekan ESC untuk menutup</p>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}