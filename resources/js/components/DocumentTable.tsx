import { Eye, Trash2, RefreshCw, AlertCircle } from 'lucide-react';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';
import { toast } from 'sonner';

interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    file_type: string;
    file_size: string;
    created_at: string;
    status?: 'uploaded' | 'processing' | 'completed' | 'failed';
    extracted_data?: {
        error?: string;
        failed_at?: string;
    };
}

interface DocumentTableProps {
    documents: Document[];
    type: 'spk' | "form-checklist" | "form-pm-pop";
    onDelete: (id: number) => void;
}

export default function DocumentTable({ documents, type, onDelete }: DocumentTableProps) {
    const [localDocuments, setLocalDocuments] = useState<Document[]>(documents);
    const [isPolling, setIsPolling] = useState(false);
    const [pollingCount, setPollingCount] = useState(0);

    useEffect(() => {
        setLocalDocuments(documents);

        const incompleteIds = documents
            .filter(doc => doc.status === 'uploaded' || doc.status === 'processing')
            .map(doc => doc.id_upload);
        
        if (incompleteIds.length === 0) {
            console.log(' No documents to poll');
            setIsPolling(false);
            return;
        }

        console.log('🔄 Starting polling for documents:', incompleteIds);
        setIsPolling(true);
        setPollingCount(0);

        const interval = setInterval(async () => {
            try {
                setPollingCount(prev => prev + 1);
                console.log(`📡 Polling status... (${pollingCount + 1})`);
                
                const response = await axios.post('/api/documents/check-status', { 
                    ids: incompleteIds 
                });
                
                console.log(' Status received:', response.data);

                setLocalDocuments(prevDocs => 
                    prevDocs.map(doc => {
                        const updated = response.data.find((d: any) => d.id_upload === doc.id_upload);
                        
                        if (updated && updated.status !== doc.status) {
                            console.log(`📝 Doc ${doc.id_upload}: ${doc.status} → ${updated.status}`);
                            
                            //  Toast notification untuk perubahan status
                            if (updated.status === 'completed') {
                                toast.success(` ${doc.file_name}`, {
                                    description: 'Dokumen berhasil diproses dan disimpan ke database!',
                                    duration: 4000,
                                    classNames: {
                                        toast: '!bg-gray-900 !border-2 !border-green-400',
                                        title: '!text-white !text-sm',
                                        description: '!text-gray-300 !text-xs',
                                        icon: '!text-green-500',
                                    }
                                });
                            } else if (updated.status === 'failed') {
                                // Get error message dari extracted_data
                                const errorMsg = doc.extracted_data?.error || 'Proses gagal';
                                
                                toast.error(`${doc.file_name}`, {
                                    description: errorMsg,
                                    duration: 6000,
                                    classNames: {
                                        toast: '!bg-gray-900 !border-2 !border-red-400',
                                        title: '!text-white !text-sm',
                                        description: '!text-gray-300 !text-xs !whitespace-pre-line',
                                        icon: '!text-red-500',
                                    }
                                });
                            }
                        }
                        
                        return updated ? { ...doc, status: updated.status } : doc;
                    })
                );

                const stillIncomplete = response.data.some((d: any) => 
                    d.status === 'uploaded' || d.status === 'processing'
                );
                
                if (!stillIncomplete) {
                    console.log(' All documents completed/failed, stopping poll');
                    clearInterval(interval);
                    setIsPolling(false);
                    
                    setTimeout(() => {
                        router.reload({ only: ['documents'] });
                    }, 1000);
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 3000);

        return () => {
            console.log('🛑 Stopping polling');
            clearInterval(interval);
            setIsPolling(false);
        };
    }, [documents]);

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '-';

        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    const formatFileSize = (sizeValue: string) => {
        const size = Number(sizeValue);
        if (isNaN(size)) return '-';

        if (size >= 1024 * 1024) {
            return (size / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return (size / 1024).toFixed(2) + ' KB';
    };

    const handleView = (id: number) => {
        router.visit(route('documents.detail', id));
    };

    const handleRetry = (id: number) => {
        router.post(route('documents.retry', id), {}, {
            onSuccess: () => {
                toast.info('🔄 Dokumen akan diproses ulang', {
                    duration: 3000,
                    classNames: {
                        toast: '!bg-gray-900 !border-2 !border-blue-400',
                        title: '!text-white',
                        icon: '!text-blue-400',
                    }
                });
            }
        });
    };

    const getStatusBadge = (doc: Document) => {
        const status = doc.status;
        
        if (!status) return null;
        
        switch (status) {
            case 'completed':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30 flex items-center gap-1.5 w-fit">
                        <span className="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                        Selesai
                    </span>
                );
            case 'processing':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 flex items-center gap-1.5 w-fit">
                        <svg className="animate-spin h-3 w-3" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none"/>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        Memproses
                    </span>
                );
            case 'failed':
                return (
                    <div className="flex flex-col gap-1">
                        <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30 flex items-center gap-1.5 w-fit">
                            <AlertCircle size={12} />
                            Gagal
                        </span>
                        {doc.extracted_data?.error && (
                            <span className="text-xs text-red-400/70 italic max-w-xs truncate" title={doc.extracted_data.error}>
                                {doc.extracted_data.error}
                            </span>
                        )}
                    </div>
                );
            case 'uploaded':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center gap-1.5 w-fit">
                        <span className="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                        Menunggu
                    </span>
                );
            default:
                return null;
        }
    };

    return (
        <div className="mt-6">
            {/*  Enhanced Polling Indicator */}
            {isPolling && (
                <div className="mb-4 px-4 py-3 bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border border-blue-500/30 rounded-lg">
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <div className="w-2 h-2 bg-blue-400 rounded-full animate-ping absolute"></div>
                            <div className="w-2 h-2 bg-blue-400 rounded-full"></div>
                        </div>
                        <div className="flex-1">
                            <p className="text-sm text-blue-400 font-medium">
                                Memantau status dokumen secara real-time...
                            </p>
                            <p className="text-xs text-blue-400/70 mt-0.5">
                                Polling #{pollingCount} • Refresh otomatis setiap 3 detik
                            </p>
                        </div>
                    </div>
                </div>
            )}

            <div className="overflow-x-auto rounded-2xl border border-gray-800 shadow-lg">
                <table className="min-w-full border-collapse bg-gray-900 text-left text-sm text-gray-300">
                    <thead className="bg-gray-800 text-gray-400">
                        <tr>
                            <th className="px-4 py-3">No</th>
                            <th className="px-4 py-3">Nama File</th>
                            <th className="px-4 py-3">Tanggal Upload</th>
                            <th className="px-4 py-3">Ukuran</th>
                            <th className="px-4 py-3">Status</th>
                            <th className="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        {localDocuments.length > 0 ? (
                            localDocuments.map((upload, index) => (
                                <tr 
                                    key={upload.id_upload} 
                                    className={`transition-colors hover:bg-gray-800 border-b border-gray-800 ${
                                        upload.status === 'failed' ? 'bg-red-500/5' : ''
                                    }`}
                                >
                                    <td className="px-4 py-3">{index + 1}</td>

                                    <td className="px-4 py-3 font-medium text-white">
                                        {upload.file_name}
                                    </td>

                                    <td className="px-4 py-3">{formatDate(upload.created_at)}</td>

                                    <td className="px-4 py-3">{formatFileSize(upload.file_size)}</td>

                                    <td className="px-4 py-3">{getStatusBadge(upload)}</td>

                                    <td className="px-4 py-3">
                                        <div className="flex gap-2 items-center">
                                            {/* Tombol Lihat Detail */}
                                            <button
                                                onClick={() => handleView(upload.id_upload)}
                                                className="p-2 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors duration-200"
                                                title="Lihat detail dokumen"
                                            >
                                                <Eye size={18} />
                                            </button>
                                            
                                            {/* Tombol Retry (hanya untuk failed) */}
                                            {upload.status === 'failed' && (
                                                <button
                                                    onClick={() => handleRetry(upload.id_upload)}
                                                    className="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 transition-colors duration-200"
                                                    title="Coba proses ulang"
                                                >
                                                    <RefreshCw size={18} />
                                                </button>
                                            )}
                                            
                                            {/* Tombol Hapus */}
                                            <button
                                                onClick={() => onDelete(upload.id_upload)}
                                                className="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors duration-200"
                                                title="Hapus dokumen"
                                            >
                                                <Trash2 size={18} />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan={6} className="px-4 py-8 text-center text-gray-500">
                                    Belum ada dokumen yang diunggah.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}