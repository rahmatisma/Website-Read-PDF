import DocumentTable from '@/components/DocumentTable';
import SearchFilter from '@/components/SearchFilter';
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
import UploadPDFBox from '@/components/UploadPDFBox';
import { Document } from '@/types/document';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { toast } from 'sonner';

interface PdfProps {
    documents: Document[];
}

export default function Pdf({ documents }: PdfProps) {
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [documentToDelete, setDocumentToDelete] = useState<number | null>(null);

    const handleDelete = (id: number) => {
        setDocumentToDelete(id);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (documentToDelete === null) {
            return;
        }
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

    return (
        <>
            <Head title="Daftar Dokumen PDF" />

            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadPDFBox />

                {/* Daftar Dokumen */}
                <div className="mt-6">
                    <h2 className="mb-4 text-xl font-semibold">Daftar Dokumen</h2>

                    {/* Search */}
                    <SearchFilter />

                    {/* Table - KIRIM FUNGSI handleDelete */}
                    <DocumentTable documents={documents} type="pdf" onDelete={handleDelete} />
                </div>
            </div>
            <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Hapus dokumen SPK?</AlertDialogTitle>
                        <AlertDialogDescription>
                            SPK yang dihapus tidak dapat dikembalikan. Tindakan ini bersifat permanen.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel onClick={() => setDocumentToDelete(null)}>Batal</AlertDialogCancel>
                        <AlertDialogAction onClick={confirmDelete} className="cursor-pointer bg-red-500 text-white hover:brightness-110">
                            Hapus
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
