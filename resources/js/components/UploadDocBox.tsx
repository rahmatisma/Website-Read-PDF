import { CloudArrowUpIcon } from '@heroicons/react/24/outline';
import { DocumentIcon } from '@heroicons/react/24/solid';
import { useRef, useState } from 'react';
import { router } from '@inertiajs/react';

export default function UploadDocBox() {
    const fileInputRef = useRef<HTMLInputElement | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [loading, setLoading] = useState(false);

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const handleFile = (file: File) => {
        const allowedTypes = [
            "application/msword", // .doc
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" // .docx
        ];

        if (allowedTypes.includes(file.type)) {
            setSelectedFile(file);
        } else {
            alert("Hanya file DOC atau DOCX yang diperbolehkan!");
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
        formData.append("file", selectedFile);
        formData.append("document_type", "doc");

        setLoading(true);

        router.post("/documents/doc", formData, {
            forceFormData: true,
            onSuccess: () => {
                alert("Upload dokumen berhasil!");
                setSelectedFile(null);
            },
            onError: () => {
                alert("Upload gagal, coba lagi.");
            },
            onFinish: () => setLoading(false),
        });
    };

    return (
        <div className="mt-2 flex flex-col items-center px-4">
            <div
                className={`w-full max-w-md rounded-lg border-2 border-dashed 
                            ${isDragging ? "border-purple-400 bg-gray-800" : "border-indigo-400 bg-gray-900"}
                            p-6 text-center flex flex-col justify-center
                            min-h-[250px] transition-colors duration-200`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {!selectedFile ? (
                    <>
                        <CloudArrowUpIcon className="mx-auto mb-4 h-12 w-12 text-indigo-400" />
                        <p className="mb-4 text-sm text-white">
                            {isDragging ? "Lepaskan file di sini" : "Drag & drop DOC/DOCX untuk upload"}
                        </p>
                        <button
                            onClick={handleClick}
                            className="mx-auto rounded-full bg-gradient-to-r from-indigo-400 to-purple-400 px-6 py-2 text-white shadow hover:brightness-110 transition"
                        >
                            Pilih Dokumen
                        </button>
                        <input
                            type="file"
                            accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            ref={fileInputRef}
                            onChange={handleFileChange}
                            className="hidden"
                        />
                    </>
                ) : (
                    <>
                        <DocumentIcon className="mx-auto mb-4 h-12 w-12 text-purple-500" />
                        <p className="mb-2 text-white text-sm">{selectedFile.name}</p>
                        <button
                            onClick={handleSubmit}
                            disabled={loading}
                            className="mx-auto mt-4 rounded-full bg-gradient-to-r from-purple-400 to-indigo-500 px-6 py-2 text-white shadow hover:brightness-110 transition disabled:opacity-50"
                        >
                            {loading ? "Uploading..." : "Submit"}
                        </button>
                        <button
                            onClick={() => setSelectedFile(null)}
                            className="mt-2 text-xs text-gray-400 underline"
                        >
                            Ganti file
                        </button>
                    </>
                )}
            </div>
        </div>
    );
}
