import { Eye, Trash2 } from 'lucide-react';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    file_type: string;
    file_size: string;
    created_at: string;
    status?: 'uploaded' | 'processing' | 'completed' | 'failed';
}

interface DocumentTableProps {
    documents: Document[];
    type: 'pdf' | 'doc' | 'image';
    onDelete: (id: number) => void;
}

export default function DocumentTable({ documents, type, onDelete }: DocumentTableProps) {
    const [localDocuments, setLocalDocuments] = useState<Document[]>(documents);
    const [isPolling, setIsPolling] = useState(false);

    useEffect(() => {
        setLocalDocuments(documents);

        // ✅ Cek dokumen yang BELUM selesai (uploaded atau processing)
        const incompleteIds = documents
            .filter(doc => doc.status === 'uploaded' || doc.status === 'processing')
            .map(doc => doc.id_upload);
        
        if (incompleteIds.length === 0) {
            console.log('✅ No documents to poll');
            return;
        }

        console.log('🔄 Starting polling for documents:', incompleteIds);
        setIsPolling(true);

        // ✅ Polling setiap 3 detik
        const interval = setInterval(async () => {
            try {
                console.log('📡 Polling status...');
                
                const response = await axios.post('/api/documents/check-status', { 
                    ids: incompleteIds 
                });
                
                console.log('✅ Status received:', response.data);

                // Update local state
                setLocalDocuments(prevDocs => 
                    prevDocs.map(doc => {
                        const updated = response.data.find((d: any) => d.id_upload === doc.id_upload);
                        
                        if (updated && updated.status !== doc.status) {
                            console.log(`📝 Doc ${doc.id_upload}: ${doc.status} → ${updated.status}`);
                        }
                        
                        return updated ? { ...doc, status: updated.status } : doc;
                    })
                );

                // ✅ Cek apakah masih ada yang belum selesai
                const stillIncomplete = response.data.some((d: any) => 
                    d.status === 'uploaded' || d.status === 'processing'
                );
                
                if (!stillIncomplete) {
                    console.log('✅ All documents completed/failed, stopping poll');
                    clearInterval(interval);
                    setIsPolling(false);
                    
                    // ✅ Refresh halaman untuk data terbaru dari server
                    setTimeout(() => {
                        router.reload({ only: ['documents'] });
                    }, 1000);
                }
            } catch (error) {
                console.error('❌ Polling error:', error);
            }
        }, 3000); // Poll setiap 3 detik

        // Cleanup saat component unmount
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

    const getStatusBadge = (status?: string) => {
        if (!status) return null;
        
        switch (status) {
            case 'completed':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">
                        ✅ Selesai
                    </span>
                );
            case 'processing':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 animate-pulse">
                        🔄 Proses
                    </span>
                );
            case 'failed':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">
                        ❌ Gagal
                    </span>
                );
            case 'uploaded':
                return (
                    <span className="px-3 py-1.5 text-xs font-medium rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 animate-pulse">
                        📤 Menunggu
                    </span>
                );
            default:
                return null;
        }
    };

    return (
        <div className="mt-6">
            {/* ✅ Polling Indicator */}
            {isPolling && (
                <div className="mb-4 px-4 py-3 bg-blue-500/10 border border-blue-500/30 rounded-lg flex items-center gap-3">
                    <div className="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    <span className="text-sm text-blue-400">
                        Memantau status dokumen secara otomatis...
                    </span>
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
                                    className="transition-colors hover:bg-gray-800 border-b border-gray-800"
                                >
                                    <td className="px-4 py-3">{index + 1}</td>

                                    <td className="px-4 py-3 font-medium text-white">
                                        {upload.file_name}
                                    </td>

                                    <td className="px-4 py-3">{formatDate(upload.created_at)}</td>

                                    <td className="px-4 py-3">{formatFileSize(upload.file_size)}</td>

                                    <td className="px-4 py-3">{getStatusBadge(upload.status)}</td>

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