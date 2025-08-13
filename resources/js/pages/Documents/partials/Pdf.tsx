// pdf.tsx
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import UploadPDFBox from '@/components/UploadPDFBox';

export default function Pdf() {
    return (
        <div>
            <UploadPDFBox />
        </div>
    );
}
