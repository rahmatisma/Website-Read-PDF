import { CloudArrowUpIcon } from '@heroicons/react/24/outline';
import { DocumentIcon } from '@heroicons/react/24/solid';
import { router } from '@inertiajs/react';
import { useRef, useState } from 'react';
import { toast } from 'sonner';

export default function UploadSPKBox() {
    const fileInputRef = useRef<HTMLInputElement | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [loading, setLoading] = useState(false);

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const validateFileName = (
        fileName: string,
    ): {
        valid: boolean;
        blockReason?: string;
        warning?: string;
    } => {
        const lowerName = fileName.toLowerCase();

        const strictChecklistKeywords = [
            'form checklist',
            'form_checklist',
            'formchecklist',
            'checklist wireline',
            'checklist wireless',
            'fcw',
            'fcwl',
        ];

        const hasStrictChecklistKeyword = strictChecklistKeywords.some((keyword) => lowerName.includes(keyword));

        if (hasStrictChecklistKeyword) {
            return {
                valid: false,
                blockReason:
                    'File ini jelas adalah Form Checklist!\n\n' +
                    'Silakan upload di halaman "Form Checklist" yang terpisah.\n\n' +
                    'Halaman ini HANYA untuk dokumen SPK (Survey, Instalasi, Dismantle, Aktivasi).',
            };
        }

        if (lowerName.includes('checklist') && !lowerName.includes('spk')) {
            return {
                valid: false,
                blockReason:
                    'File ini sepertinya Form Checklist!\n\n' +
                    'Jika ini memang Form Checklist, silakan upload di halaman "Form Checklist".\n\n' +
                    'Jika ini SPK, pastikan nama file mengandung kata "SPK".',
            };
        }

        const spkKeywords = ['spk', 'survey', 'instalasi', 'dismantle', 'dismantl', 'aktivasi', 'aktifasi'];
        const hasSpkKeyword = spkKeywords.some((keyword) => lowerName.includes(keyword));

        if (hasSpkKeyword) {
            return { valid: true };
        }

        return {
            valid: true,
            warning:
                'Tidak dapat mendeteksi jenis dokumen dari nama file.\n\n' +
                'Pastikan ini adalah dokumen SPK (Survey, Instalasi, Dismantle, atau Aktivasi).\n\n' +
                '⚠️ Jika ini Form Checklist, upload akan ditolak setelah divalidasi.',
        };
    };

    const handleFile = (file: File) => {
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

        const validation = validateFileName(file.name);

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

        // 🎯 Toast loading dengan ID untuk update nanti
        const toastId = toast.loading('📤 Uploading dan validasi dokumen SPK...', {
            classNames: {
                toast: '!bg-gray-900 !border-2 !border-blue-400',
                title: '!text-white',
                icon: '!text-blue-400',
            },
        });

        router.post('/documents/spk', formData, {
            forceFormData: true,
            onSuccess: (page) => {
                // ✅ Update toast menjadi success
                toast.success('✅ Upload berhasil! Dokumen sedang divalidasi...', {
                    id: toastId,
                    duration: 4000,
                    description: 'Sistem akan memvalidasi halaman pertama. Cek tabel di bawah untuk status real-time.',
                    classNames: {
                        toast: '!bg-gray-900 !border-2 !border-green-400',
                        title: '!text-white',
                        description: '!text-gray-300 !text-xs',
                        icon: '!text-green-500',
                    },
                });

                setSelectedFile(null);

                // ✅ Info tambahan
                setTimeout(() => {
                    toast.info('💡 Tips: Dokumen akan divalidasi dalam beberapa detik', {
                        duration: 3000,
                        description: 'Jika file bukan SPK, akan otomatis dihapus dan diberi notifikasi.',
                        classNames: {
                            toast: '!bg-gray-900 !border-2 !border-cyan-400',
                            title: '!text-white !text-sm',
                            description: '!text-gray-300 !text-xs',
                            icon: '!text-cyan-400',
                        },
                    });
                }, 1500);
            },
            onError: (errors) => {
                console.error('Upload errors:', errors);

                // ❌ Parse error message
                let errorMessage = 'Upload gagal, coba lagi.';

                if (typeof errors === 'object') {
                    const errorValues = Object.values(errors).flat();
                    errorMessage = errorValues.join('\n');
                }

                // Update toast menjadi error
                toast.error('❌ Upload Gagal!', {
                    id: toastId,
                    duration: 5000,
                    description: errorMessage,
                    classNames: {
                        toast: '!bg-gray-900 !border-2 !border-red-400',
                        title: '!text-white',
                        description: '!text-gray-300 !text-xs !whitespace-pre-line',
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
                className={`w-full max-w-md rounded-lg border-2 border-dashed ${isDragging ? 'border-pink-400 bg-gray-800' : 'border-purple-400 bg-gray-900'} flex min-h-[250px] flex-col justify-center p-6 text-center transition-colors duration-200`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {!selectedFile ? (
                    <>
                        <CloudArrowUpIcon className="mx-auto mb-4 h-12 w-12 text-purple-400" />
                        <p className="mb-2 text-sm font-semibold text-white">
                            {isDragging ? 'Lepaskan file di sini' : 'Drag & drop PDF SPK to upload'}
                        </p>
                        <p className="mb-4 text-xs text-gray-400">Hanya untuk: SPK Survey, SPK Instalasi, SPK Dismantle, SPK Aktivasi</p>
                        <button
                            onClick={handleClick}
                            className="mx-auto rounded-full bg-gradient-to-r from-purple-400 to-pink-400 px-6 py-2 text-white shadow transition hover:brightness-110"
                        >
                            Select PDF SPK
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
                            className="mx-auto mt-4 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 px-6 py-2 text-white shadow transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {loading ? (
                                <span className="flex items-center gap-2">
                                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
                                        <path
                                            className="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        />
                                    </svg>
                                    Uploading...
                                </span>
                            ) : (
                                'Submit'
                            )}
                        </button>
                        <button
                            onClick={() => setSelectedFile(null)}
                            className="mt-2 text-xs text-gray-400 underline hover:text-gray-300"
                            disabled={loading}
                        >
                            Ganti file
                        </button>
                    </>
                )}
            </div>

            {/* Enhanced Info Box */}
            <div className="mt-4 w-full max-w-md rounded-lg bg-purple-900/30 border border-purple-400/30 p-4">
                <div className="mb-3">
                    <p className="mb-2 text-xs font-semibold text-purple-200">📋 Dokumen yang BOLEH diupload di halaman ini:</p>
                    <ul className="ml-4 space-y-1 text-xs text-purple-300">
                        <li>✅ SPK Survey</li>
                        <li>✅ SPK Instalasi</li>
                        <li>✅ SPK Dismantle</li>
                        <li>✅ SPK Aktivasi</li>
                    </ul>
                </div>
                <div className="border-t border-purple-400/20 pt-3">
                    <p className="text-xs text-purple-300">
                        ❌ <strong>Form Checklist</strong> tidak bisa diupload di halaman ini.
                        <br />
                        Silakan gunakan halaman <strong>"Form Checklist"</strong>.
                    </p>
                </div>
            </div>
        </div>
    );
}
