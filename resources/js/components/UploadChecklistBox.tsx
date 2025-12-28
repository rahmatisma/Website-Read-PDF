import { CloudArrowUpIcon } from '@heroicons/react/24/outline';
import { DocumentIcon } from '@heroicons/react/24/solid';
import { router } from '@inertiajs/react';
import { useRef, useState } from 'react';
import { toast } from 'sonner';

export default function UploadChecklistBox() {
    const fileInputRef = useRef<HTMLInputElement | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [loading, setLoading] = useState(false);

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const handleFile = (file: File) => {
        if (file.type === 'application/pdf') {
            setSelectedFile(file);
        } else {
            toast.error('Hanya file PDF yang diperbolehkan!');
        }
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files?.[0]) {
            handleFile(e.target.files[0]);
        }
    };

    const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(true);
    };

    const handleDragLeave = () => {
        setIsDragging(false);
    };

    const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);

        const file = e.dataTransfer.files?.[0];
        if (file) {
            handleFile(file);
        }
    };

    const handleSubmit = () => {
        if (!selectedFile) return;

        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('document_type', 'pdf');

        setLoading(true);

        router.post('/documents/checklist', formData, {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Upload form checklist berhasil!');
                setSelectedFile(null);
            },
            onError: () => {
                toast.error('Upload gagal, coba lagi.');
            },
            onFinish: () => setLoading(false),
        });
    };

    return (
        <div className="mt-2 flex flex-col items-center px-4">
            <div
                className={`w-full max-w-md rounded-lg border-2 border-dashed ${isDragging ? 'border-pink-400 bg-gray-800' : 'border-purple-400 bg-gray-900'} flex min-h-[250px] flex-col justify-center p-6 text-center transition-colors duration-200`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {!selectedFile ? (
                    <>
                        <CloudArrowUpIcon className="mx-auto mb-4 h-12 w-12 text-emerald-400" />
                        <p className="mb-4 text-sm text-white">{isDragging ? 'Lepaskan file di sini' : 'Drag & drop PDF Form Checklist here'}</p>
                        <button
                            onClick={handleClick}
                            className="mx-auto rounded-full bg-gradient-to-r from-green-400 to-emerald-400 px-6 py-2 text-white shadow transition hover:brightness-110"
                        >
                            Select File
                        </button>
                        <input type="file" accept="application/pdf" ref={fileInputRef} onChange={handleFileChange} className="hidden" />
                    </>
                ) : (
                    <>
                        <DocumentIcon className="mx-auto mb-4 h-12 w-12 text-red-500" />
                        <p className="mb-2 text-sm text-white">{selectedFile.name}</p>
                        <button
                            onClick={handleSubmit}
                            disabled={loading}
                            className="mx-auto mt-4 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 px-6 py-2 text-white shadow transition hover:brightness-110 disabled:opacity-50"
                        >
                            {loading ? 'Uploading...' : 'Submit'}
                        </button>
                        <button onClick={() => setSelectedFile(null)} className="mt-2 text-xs text-gray-400 underline">
                            Ganti file
                        </button>
                    </>
                )}
            </div>
        </div>
    );
}
