import { Head, router } from '@inertiajs/react';
import { ArrowLeft, FileText, Calendar, HardDrive, AlertCircle } from 'lucide-react';
import { useEffect, useState } from 'react';
import axios from 'axios';

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

interface DetailProps {
    upload: Upload;
    extractedData: any | null;
}

export default function Detail({ upload: initialUpload, extractedData: initialExtractedData }: DetailProps) {
    // State untuk menyimpan data terbaru
    const [upload, setUpload] = useState(initialUpload);
    const [extractedData, setExtractedData] = useState(initialExtractedData);
    const [isPolling, setIsPolling] = useState(false);

    // ✅ AUTO-POLLING: Cek status setiap 3 detik
    useEffect(() => {
        // Hanya polling jika status masih processing atau uploaded
        if (upload.status === 'processing' || upload.status === 'uploaded') {
            setIsPolling(true);

            const interval = setInterval(async () => {
                try {
                    // Panggil API getStatus
                    const response = await axios.get(`/api/documents/${upload.id_upload}/status`);
                    const data = response.data;

                    console.log('Status check:', data);

                    // Update state dengan status terbaru
                    setUpload(prev => ({
                        ...prev,
                        status: data.status,
                        updated_at: data.updated_at,
                    }));

                    // Jika sudah completed atau failed, reload halaman untuk ambil extractedData
                    if (data.status === 'completed' || data.status === 'failed') {
                        console.log('Status berubah menjadi:', data.status);
                        
                        // Reload halaman untuk ambil data lengkap
                        router.reload({ only: ['upload', 'extractedData'] });
                        
                        // Stop polling
                        setIsPolling(false);
                        clearInterval(interval);
                    }

                } catch (error) {
                    console.error('Error polling status:', error);
                }
            }, 3000); // Setiap 3 detik

            // Cleanup: Stop interval saat component unmount
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

    // Render berdasarkan status
    const renderContent = () => {
        switch (upload.status) {
            case 'uploaded':
                return (
                    <div className="text-center py-12">
                        <div className="animate-spin w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
                        <p className="text-gray-400">Menunggu proses dimulai...</p>
                        {isPolling && (
                            <p className="text-sm text-gray-500 mt-2">Mengecek status otomatis...</p>
                        )}
                    </div>
                );

            case 'processing':
                return (
                    <div className="text-center py-12">
                        <div className="animate-spin w-16 h-16 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-4"></div>
                        <p className="text-gray-400">Dokumen sedang diproses oleh Python...</p>
                        {isPolling && (
                            <p className="text-sm text-gray-500 mt-2">✅ Mengecek status otomatis setiap 3 detik</p>
                        )}
                    </div>
                );

            case 'failed':
                return (
                    <div className="text-center py-12">
                        <AlertCircle className="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <p className="text-red-400 font-semibold">Proses dokumen gagal</p>
                        <p className="text-sm text-gray-500 mt-2">Silakan coba upload ulang</p>
                    </div>
                );

            case 'completed':
                if (!extractedData) {
                    return (
                        <div className="text-center py-12">
                            <p className="text-gray-400">Data tidak tersedia</p>
                        </div>
                    );
                }

                return (
                    <div className="space-y-6">
                        <h3 className="text-xl font-semibold text-white mb-4">📊 Data Extracted</h3>
                        
                        {/* Preview JSON - Nanti akan kita pecah per tipe dokumen */}
                        <div className="bg-gray-800 p-6 rounded-lg max-h-[600px] overflow-auto">
                            <pre className="text-sm text-gray-300">
                                {JSON.stringify(extractedData, null, 2)}
                            </pre>
                        </div>
                    </div>
                );

            default:
                return (
                    <div className="text-center py-12">
                        <p className="text-gray-400">Status: {upload.status}</p>
                    </div>
                );
        }
    };

    return (
        <>
            <Head title={`Detail - ${upload.file_name}`} />

            <div className="p-6 space-y-6">
                {/* Header dengan tombol kembali */}
                <div className="flex items-center gap-4">
                    <button
                        onClick={goBack}
                        className="p-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition-colors"
                    >
                        <ArrowLeft size={20} className="text-gray-400" />
                    </button>
                    <h1 className="text-2xl font-bold text-white">Detail Dokumen</h1>
                    
                    {/* Indikator Polling */}
                    {isPolling && (
                        <span className="ml-auto flex items-center gap-2 text-sm text-blue-400">
                            <span className="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                            Auto-refresh aktif
                        </span>
                    )}
                </div>

                {/* Card Header Info */}
                <div className="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-lg">
                    <div className="flex items-start justify-between">
                        <div className="space-y-3 flex-1">
                            <h2 className="text-xl font-semibold text-white">{upload.file_name}</h2>
                            
                            <div className="flex flex-wrap gap-4 text-sm text-gray-400">
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
                            <div className="flex items-center gap-2">
                                {upload.status === 'completed' && (
                                    <span className="px-3 py-1 text-sm rounded-full bg-green-500/20 text-green-400">
                                        ✅ Selesai Diproses
                                    </span>
                                )}
                                {upload.status === 'processing' && (
                                    <span className="px-3 py-1 text-sm rounded-full bg-yellow-500/20 text-yellow-400 animate-pulse">
                                        ⏳ Sedang Diproses
                                    </span>
                                )}
                                {upload.status === 'uploaded' && (
                                    <span className="px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-400 animate-pulse">
                                        📤 Menunggu Proses
                                    </span>
                                )}
                                {upload.status === 'failed' && (
                                    <span className="px-3 py-1 text-sm rounded-full bg-red-500/20 text-red-400">
                                        ❌ Gagal Diproses
                                    </span>
                                )}
                            </div>
                        </div>

                        {/* Tombol Lihat PDF Asli */}
                        <button
                            onClick={openPDF}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg transition-colors"
                        >
                            <FileText size={18} />
                            <span>Lihat PDF Asli</span>
                        </button>
                    </div>
                </div>

                {/* Content Area */}
                <div className="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-lg">
                    {renderContent()}
                </div>
            </div>
        </>
    );
}