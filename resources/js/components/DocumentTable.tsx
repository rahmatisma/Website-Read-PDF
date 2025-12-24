import { Eye, Trash2 } from 'lucide-react';
import { router } from '@inertiajs/react';

// ✅ INTERFACE YANG BENAR - Sesuai dengan data dari backend
interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    file_type: string;
    file_size: string;
    created_at: string;
    status?: 'uploaded' | 'processing' | 'completed' | 'failed'; // ⬅️ PENTING: Tambahkan ini
}

interface DocumentTableProps {
    documents: Document[];
    type: 'pdf' | 'doc' | 'image';
    onDelete: (id: number) => void;
}

export default function DocumentTable({ documents, type, onDelete }: DocumentTableProps) {
    // ✅ Format tanggal
    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '-';

        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    // ✅ Format ukuran file
    const formatFileSize = (sizeValue: string) => {
        const size = Number(sizeValue);
        if (isNaN(size)) return '-';

        if (size >= 1024 * 1024) {
            return (size / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return (size / 1024).toFixed(2) + ' KB';
    };

    // ✅ Redirect ke halaman detail
    const handleView = (id: number) => {
        router.visit(route('documents.detail', id));
    };

    // ✅ Badge status dokumen
    const getStatusBadge = (status?: string) => {
        if (!status) return null;
        
        switch (status) {
            case 'completed':
                return <span className="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400">✅ Selesai</span>;
            case 'processing':
                return <span className="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400 animate-pulse">🔄 Proses</span>;
            case 'failed':
                return <span className="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400">❌ Gagal</span>;
            case 'uploaded':
                return <span className="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">📤 Upload</span>;
            default:
                return null;
        }
    };

    return (
        <div className="mt-6 overflow-x-auto rounded-2xl border border-gray-800 shadow-lg">
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
                    {documents.length > 0 ? (
                        documents.map((upload, index) => (
                            <tr key={upload.id_upload} className="transition-colors hover:bg-gray-800">
                                <td className="px-4 py-3">{index + 1}</td>

                                <td className="px-4 py-3 font-medium text-white">{upload.file_name}</td>

                                <td className="px-4 py-3">{formatDate(upload.created_at)}</td>

                                <td className="px-4 py-3">{formatFileSize(upload.file_size)}</td>

                                {/* ✅ Kolom Status */}
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
                            <td colSpan={6} className="px-4 py-6 text-center text-gray-500">
                                Belum ada dokumen yang diunggah.
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
}