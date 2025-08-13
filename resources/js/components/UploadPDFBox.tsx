import { CloudArrowUpIcon } from '@heroicons/react/24/outline';
import { useRef, useState } from 'react';

export default function UploadPDFBox() {
    const fileInputRef = useRef<HTMLInputElement | null>(null);
    const [isDragging, setIsDragging] = useState(false);

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const handleFile = (file: File) => {
        if (file.type === "application/pdf") {
            console.log("PDF terpilih:", file.name);
            // TODO: upload file ke server atau proses sesuai kebutuhan
        } else {
            alert("Hanya file PDF yang diperbolehkan!");
        }
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files?.[0]) {
            handleFile(e.target.files[0]);
        }
    };

    const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault(); // wajib supaya bisa drop
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

    return (
        <div className="mt-2 flex flex-col items-center px-4">
            <div
                className={`w-full max-w-md rounded-lg border-2 border-dashed 
                            ${isDragging ? "border-pink-400 bg-gray-800" : "border-purple-400 bg-gray-900"}
                            p-6 text-center flex flex-col justify-center
                            min-h-[300px] sm:min-h-[350px] md:min-h-[400px]
                            transition-colors duration-200`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                <CloudArrowUpIcon className="mx-auto mb-4 h-12 w-12 text-purple-400" />
                <p className="mb-4 text-sm text-white">
                    {isDragging ? "Lepaskan file di sini" : "Drag & drop PDF file to upload"}
                </p>
                <button
                    onClick={handleClick}
                    className="mx-auto rounded-full bg-gradient-to-r from-purple-400 to-pink-400 px-6 py-2 text-white shadow hover:brightness-110 transition"
                >
                    Select PDF
                </button>
                <input
                    type="file"
                    accept="application/pdf"
                    ref={fileInputRef}
                    onChange={handleFileChange}
                    className="hidden"
                />
            </div>
        </div>
    );
}
