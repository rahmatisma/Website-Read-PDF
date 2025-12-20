import { CloudArrowUpIcon } from '@heroicons/react/24/outline';
import { PhotoIcon } from '@heroicons/react/24/solid';
import { useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { toast } from 'sonner';

export default function UploadImageBox() {
    const fileInputRef = useRef<HTMLInputElement | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [loading, setLoading] = useState(false);

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const handleFile = (file: File) => {
        if (file.type.startsWith("image/")) {
            setSelectedFile(file);
        } else {
            toast.error("Hanya file gambar (JPG, PNG) yang diperbolehkan!");
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
        formData.append("image", selectedFile);
        formData.append("document_type", "gambar");

        setLoading(true);

        router.post("/documents/image", formData, {
            forceFormData: true,
            onSuccess: () => {
                toast.success("Upload gambar berhasil!");
                setSelectedFile(null);
            },
            onError: () => {
                toast.error("Upload gagal, coba lagi.");
            },
            onFinish: () => setLoading(false),
        });
    };

    return (
        <div className="mt-2 flex flex-col items-center px-4">
            <div
                className={`w-full max-w-md rounded-lg border-2 border-dashed 
                            ${isDragging ? "border-green-400 bg-gray-800" : "border-blue-400 bg-gray-900"}
                            p-6 text-center flex flex-col justify-center
                            min-h-[250px] transition-colors duration-200`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {!selectedFile ? (
                    <>
                        <CloudArrowUpIcon className="mx-auto mb-4 h-12 w-12 text-blue-400" />
                        <p className="mb-4 text-sm text-white">
                            {isDragging ? "Lepaskan file di sini" : "Drag & drop gambar untuk upload"}
                        </p>
                        <button
                            onClick={handleClick}
                            className="mx-auto rounded-full bg-gradient-to-r from-blue-400 to-green-400 px-6 py-2 text-white shadow hover:brightness-110 transition"
                        >
                            Pilih Gambar
                        </button>
                        <input
                            type="file"
                            accept="image/png,image/jpeg"
                            ref={fileInputRef}
                            onChange={handleFileChange}
                            className="hidden"
                        />
                    </>
                ) : (
                    <>
                        <PhotoIcon className="mx-auto mb-4 h-12 w-12 text-green-500" />
                        <p className="mb-2 text-white text-sm">{selectedFile.name}</p>
                        <button
                            onClick={handleSubmit}
                            disabled={loading}
                            className="mx-auto mt-4 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 px-6 py-2 text-white shadow hover:brightness-110 transition disabled:opacity-50"
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