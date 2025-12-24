export interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    file_type: string;
    file_size: string;
    created_at: string;
    status?: 'uploaded' | 'processing' | 'completed' | 'failed';
}