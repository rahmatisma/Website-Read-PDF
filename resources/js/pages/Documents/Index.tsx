interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    created_at: string;
    file_size: string;
}

interface DocumentTableProps {
    documents: Document[];
}

export default function DocumentTable({ documents }: DocumentTableProps) {
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

    const getFileExtension = (name: string) => {
        return name.split('.').pop()?.toLowerCase() ?? '';
    };

    const isImage = (ext: string) => ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
    const isPdf = (ext: string) => ext === 'pdf';

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
                        documents.map((upload, index) => {
                            const ext = getFileExtension(upload.file_name);

                            return (
                                <tr key={upload.id_upload} className="transition-colors hover:bg-gray-800">
                                    <td className="px-4 py-3">{index + 1}</td>

                                    <td className="px-4 py-3 font-medium text-white">{upload.file_name}</td>

                                    <td className="px-4 py-3">{formatDate(upload.created_at)}</td>

                                    <td className="px-4 py-3">{formatFileSize(upload.file_size)}</td>

                                    <td className="px-4 py-3">
                                        {/* Jika file image muncul thumbnail */}
                                        {isImage(ext) ? (
                                            <a href={`/storage/${upload.file_path}`} target="_blank" rel="noopener noreferrer">
                                                <img
                                                    src={`/storage/${upload.file_path}`}
                                                    alt={upload.file_name}
                                                    className="h-12 w-12 rounded border border-gray-700 object-cover shadow-sm transition-all duration-200 hover:scale-110 hover:shadow-lg"
                                                />
                                            </a>
                                        ) : (
                                            /* Jika PDF / DOC muncul link lihat */
                                            <a
                                                href={`/storage/${upload.file_path}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="text-blue-400 underline hover:text-blue-500"
                                            >
                                                Lihat
                                            </a>
                                        )}
                                    </td>
                                </tr>
                            );
                        })
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
