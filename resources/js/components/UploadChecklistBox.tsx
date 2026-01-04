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

    /**
     * ✅ STRICT VALIDATION - Block obvious mistakes
     * ❌ BLOCK: File yang JELAS adalah SPK
     * ⚠️  WARN: File yang tidak jelas tapi tetap izinkan
     * ✅ ALLOW: File dengan kata kunci Checklist
     */
    const validateFileName = (
        fileName: string,
    ): {
        valid: boolean;
        blockReason?: string;
        warning?: string;
    } => {
        const lowerName = fileName.toLowerCase();

        // ❌ BLOCK: Kata kunci SPK yang JELAS
        const strictSpkKeywords = [
            'spk survey',
            'spk instalasi',
            'spk dismantle',
            'spk aktivasi',
            'spk_survey',
            'spk_instalasi',
            'spk_dismantle',
            'spk_aktivasi',
            'spk',
            'survey',
            'instalasi',
            'dismantle',
            'dismantl',
            'aktivasi',
            'aktifasi',
        ];

        const hasStrictSpkKeyword = strictSpkKeywords.some((keyword) => lowerName.includes(keyword));

        if (hasStrictSpkKeyword) {
            return {
                valid: false,
                blockReason:
                    'File ini jelas adalah dokumen SPK!\n\n' +
                    'Silakan upload di halaman "Dokumen PDF" yang terpisah.\n\n' +
                    'Halaman ini HANYA untuk Form Checklist (Wireline atau Wireless).',
            };
        }

        // ❌ BLOCK: File dengan kata "spk" tapi tidak ada kata "checklist"
        if (lowerName.includes('spk') && !lowerName.includes('checklist')) {
            return {
                valid: false,
                blockReason:
                    'File ini sepertinya dokumen SPK!\n\n' +
                    'Jika ini memang SPK, silakan upload di halaman "Dokumen PDF".\n\n' +
                    'Jika ini Form Checklist, pastikan nama file mengandung kata "Checklist".',
            };
        }

        // ✅ ALLOW: File dengan kata kunci Checklist yang jelas
        const checklistKeywords = ['checklist', 'check list', 'form checklist', 'wireline', 'wireless', 'fcw', 'fcwl'];

        const hasChecklistKeyword = checklistKeywords.some((keyword) => lowerName.includes(keyword));

        if (hasChecklistKeyword) {
            return { valid: true }; // Perfect!
        }

        // ⚠️ WARN: File ambigu (tidak ada kata kunci jelas)
        // Tetap izinkan upload, tapi beri warning
        return {
            valid: true,
            warning:
                'Tidak dapat mendeteksi jenis dokumen dari nama file.\n\n' +
                'Pastikan ini adalah Form Checklist (Wireline atau Wireless).\n\n' +
                'Jika ini dokumen SPK, upload akan ditolak setelah diproses.',
        };
    };

    const handleFile = (file: File) => {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            toast.error('Hanya file PDF yang diperbolehkan!', {
                duration: 5000,
                classNames: {
                    toast: '!bg-gray-900 !border-2 !border-red-400',
                    title: '!text-white',
                    icon: '!text-red-500',
                },
            });
            return;
        }

        // ✅ STRICT Validasi nama file
        const validation = validateFileName(file.name);

        // ❌ BLOCK: Jika jelas salah
        if (!validation.valid && validation.blockReason) {
            toast.error(validation.blockReason, {
                duration: 8000,
                classNames: {
                    toast: '!bg-gray-900 !border-2 !border-red-500',
                    title: '!text-white !text-sm !whitespace-pre-line',
                    icon: '!text-red-500',
                },
            });
            return;
        }

        // ⚠️ WARN: Jika ambigu tapi tetap izinkan
        if (validation.warning) {
            toast.warning(validation.warning, {
                duration: 6000,
                classNames: {
                    toast: '!bg-gray-900 !border-2 !border-yellow-400',
                    title: '!text-white !text-sm !whitespace-pre-line',
                    icon: '!text-yellow-500',
                },
            });
        }

        setSelectedFile(file);
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
                toast.success('Upload berhasil! Dokumen sedang divalidasi oleh sistem...', {
                    duration: 4000,
                    classNames: {
                        toast: '!bg-gray-900 !border-2 !border-green-400',
                        title: '!text-white !text-sm !whitespace-pre-line text-center',
                        icon: '!text-green-500',
                    },
                });
                setSelectedFile(null);
            },
            onError: (errors) => {
                console.error('Upload errors:', errors);
                const errorMessage = typeof errors === 'object' ? Object.values(errors).flat().join(', ') : 'Upload gagal, coba lagi.';

                toast.error(errorMessage, {
                    duration: 5000,
                    classNames: {
                        toast: '!bg-gray-900 !border-2 !border-red-400',
                        title: '!text-white',
                        icon: '!text-red-500',
                    },
                });
            },
            onFinish: () => setLoading(false),
        });
    };

    return (
        <div className="mt-2 flex flex-col items-center px-4">
            <div
                className={`w-full max-w-md rounded-lg border-2 border-dashed ${isDragging ? 'border-emerald-400 bg-gray-800' : 'border-emerald-400 bg-gray-900'} flex min-h-[250px] flex-col justify-center p-6 text-center transition-colors duration-200`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {!selectedFile ? (
                    <>
                        <CloudArrowUpIcon className="mx-auto mb-4 h-12 w-12 text-emerald-400" />
                        <p className="mb-2 text-sm font-semibold text-white">
                            {isDragging ? 'Lepaskan file di sini' : 'Drag & drop PDF Form Checklist here'}
                        </p>
                        <p className="mb-4 text-xs text-gray-400">Hanya untuk: Form Checklist Wireline atau Wireless</p>
                        <button
                            onClick={handleClick}
                            className="mx-auto rounded-full bg-gradient-to-r from-green-400 to-emerald-400 px-6 py-2 text-white shadow transition hover:brightness-110"
                        >
                            Select Form Checklist
                        </button>
                        <input type="file" accept="application/pdf" ref={fileInputRef} onChange={handleFileChange} className="hidden" />
                    </>
                ) : (
                    <>
                        <DocumentIcon className="mx-auto mb-4 h-12 w-12 text-red-500" />
                        <p className="mb-2 text-sm font-semibold text-white">{selectedFile.name}</p>
                        <p className="mb-4 text-xs text-gray-400">{(selectedFile.size / 1024 / 1024).toFixed(2)} MB</p>
                        <button
                            onClick={handleSubmit}
                            disabled={loading}
                            className="mx-auto mt-4 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 px-6 py-2 text-white shadow transition hover:brightness-110 disabled:opacity-50"
                        >
                            {loading ? 'Uploading...' : 'Submit'}
                        </button>
                        <button onClick={() => setSelectedFile(null)} className="mt-2 text-xs text-gray-400 underline hover:text-gray-300">
                            Ganti file
                        </button>
                    </>
                )}
            </div>

            {/* Enhanced Info Box */}
            {/* <div className="mt-4 w-full max-w-md rounded-lg border border-emerald-400/30 bg-emerald-900/30 p-4">
                <div className="mb-3">
                    <p className="mb-2 text-xs font-semibold text-emerald-200">📋 Dokumen yang BOLEH diupload di halaman ini:</p>
                    <ul className="ml-4 space-y-1 text-xs text-emerald-300">
                        <li>✅ Form Checklist Wireline</li>
                        <li>✅ Form Checklist Wireless</li>
                    </ul>
                </div>
                <div className="border-t border-emerald-400/20 pt-3">
                    <p className="text-xs text-emerald-300">
                        ❌ <strong>Dokumen SPK</strong> tidak bisa diupload di halaman ini.
                        <br />
                        Silakan gunakan halaman <strong>"Dokumen PDF"</strong>.
                    </p>
                </div>
            </div> */}
        </div>
    );
}
