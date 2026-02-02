// resources/js/Pages/Documents/partials/FormPMPOP.tsx
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
import UploadPMPOPBox from '@/components/UploadPMPOP';
import { Document } from '@/types/document';
import { Head } from '@inertiajs/react';
import { toast } from 'sonner';
import { Search } from 'lucide-react';

interface FormPMPOPProps {
    documents: Document[];
    filters?: {
        keyword?: string;
        date_from?: string;
        date_to?: string;
    };
}

export default function FormPMPOP({ documents, filters = {} }: FormPMPOPProps) {
    const [documentToDelete, setDocumentToDelete] = useState<number | null>(null);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);

    const handleDelete = (id: number) => {
        setDocumentToDelete(id);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (documentToDelete === null) return;
        
        router.delete(route('documents.pmpop.destroy', documentToDelete), {
            onSuccess: () => {
                toast.success('Form PM POP berhasil dihapus');
                setDeleteDialogOpen(false);
                setDocumentToDelete(null);
            },
            onError: () => {
                toast.error('Gagal menghapus Form PM POP');
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
            <Head title="Form PM POP" />

            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadPMPOPBox />

                {/* Header with Search Button */}
                <div className="mt-6">
                    <div className="mb-4 flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-semibold text-white">Daftar Form PM POP</h2>
                            <p className="text-sm text-gray-400 mt-1">
                                {documents.length} dokumen tersedia
                            </p>
                        </div>
                        
                        {/* Search Button - Warna biru untuk membedakan dari FormChecklist */}
                        <button
                            onClick={goToSearch}
                            className="flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-3 text-white font-medium shadow-lg hover:brightness-110 transition-all"
                        >
                            <Search className="h-5 w-5" />
                            Cari Dokumen
                        </button>
                    </div>

                    {/* Document Table */}
                    <DocumentTable 
                        documents={documents} 
                        type="form-pm-pop" 
                        onDelete={handleDelete} 
                    />
                </div>
            </div>

            {/* Delete Dialog */}
            <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Hapus Form PM POP?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Form PM POP yang dihapus tidak dapat dikembalikan. Tindakan ini bersifat permanen.
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