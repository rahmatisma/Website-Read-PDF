import { Eye, Trash2 } from 'lucide-react';

interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    created_at: string;
    file_size: string;
}

interface DocumentTableProps {
    documents: Document[];
    type: 'pdf' | 'doc' | 'image';
    onDelete: (id: number) => void;  // TAMBAHKAN INI
}

export default function DocumentTable({ documents, type, onDelete }: DocumentTableProps) {
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

    const handleView = (filePath: string) => {
        window.open(`/storage/${filePath}`, '_blank');
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

                                <td className="px-4 py-3">
                                    <div className="flex gap-2 items-center">
                                        {/* Tombol Lihat */}
                                        <button
                                            onClick={() => handleView(upload.file_path)}
                                            className="p-2 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors duration-200"
                                            title="Lihat dokumen"
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
                            <td colSpan={5} className="px-4 py-6 text-center text-gray-500">
                                Belum ada dokumen yang diunggah.
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
}