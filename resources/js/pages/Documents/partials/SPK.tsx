// resources/js/Pages/Documents/partials/SPK.tsx
import { useState } from 'react';
import { router } from '@inertiajs/react';
import DocumentTable from '@/components/DocumentTable';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import UploadSPKBox from '@/components/UploadSPKBox';
import { Document } from '@/types/document';
import { Head } from '@inertiajs/react';
import { toast } from 'sonner';
import { Search } from 'lucide-react';

interface SPKProps {
    documents: Document[];
    filters?: {
        keyword?: string;
        date_from?: string;
        date_to?: string;
    };
}

export default function SPK({ documents, filters = {} }: SPKProps) {
    const [documentToDelete, setDocumentToDelete] = useState<number | null>(null);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);

    const handleDelete = (id: number) => {
        setDocumentToDelete(id);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (documentToDelete === null) return;
        
        router.delete(route('documents.destroy', documentToDelete), {
            onSuccess: () => {
                toast.success('Dokumen SPK berhasil dihapus');
                setDeleteDialogOpen(false);
                setDocumentToDelete(null);
            },
            onError: () => {
                toast.error('Gagal menghapus dokumen SPK');
                setDeleteDialogOpen(false);
                setDocumentToDelete(null);
            },
        });
    };

    const goToSearch = () => {
        router.visit(route('documents.search'));
    };

    return (
        <>
            <Head title="Daftar Dokumen SPK" />

            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadSPKBox />

                {/* Header with Search Button */}
                <div className="mt-6">
                    <div className="mb-4 flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-semibold text-white">Daftar Dokumen SPK</h2>
                            <p className="text-sm text-gray-400 mt-1">
                                {documents.length} dokumen tersedia
                            </p>
                        </div>
                        
                        {/* Search Button */}
                        <button
                            onClick={goToSearch}
                            className="flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-white font-medium shadow-lg hover:brightness-110 transition-all"
                        >
                            <Search className="h-5 w-5" />
                            Cari Dokumen
                        </button>
                    </div>

                    {/* Document Table */}
                    <DocumentTable 
                        documents={documents} 
                        type="spk" 
                        onDelete={handleDelete} 
                    />
                </div>
            </div>

            {/* Delete Dialog */}
            <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Hapus dokumen SPK?</AlertDialogTitle>
                        <AlertDialogDescription>
                            SPK yang dihapus tidak dapat dikembalikan. Tindakan ini bersifat permanen.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel onClick={() => setDocumentToDelete(null)}>
                            Batal
                        </AlertDialogCancel>
                        <AlertDialogAction 
                            onClick={confirmDelete} 
                            className="cursor-pointer bg-red-500 text-white hover:brightness-110"
                        >
                            Hapus
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}