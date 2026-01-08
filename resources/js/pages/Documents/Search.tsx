// ============================================
// resources/js/Pages/Documents/Search.tsx
// ============================================

import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { 
    MagnifyingGlassIcon, 
    FunnelIcon, 
    XMarkIcon,
    ArrowPathIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    MapPinIcon,
    UserIcon,
    BuildingOfficeIcon,
    CalendarIcon,
} from '@heroicons/react/24/outline';
import axios from 'axios';
import { toast } from 'sonner';

// ============================================
// TYPES
// ============================================

interface SPKData {
    id_spk: number;
    no_spk: string;
    no_jaringan: string;
    jenis_spk: string;
    document_type: string;
    tanggal_spk: string;
    no_mr?: string;
    no_fps?: string;
    jaringan?: {
        no_jaringan: string;
        nama_pelanggan: string;
        lokasi_pelanggan: string;
        jasa: string;
    };
    execution_info?: {
        teknisi: string;
        nama_vendor: string;
        pic_pelanggan?: string;
    };
    upload?: {
        id_upload: number;
        file_name: string;
        status: string;
        file_size: number;
    };
}

interface FilterOptions {
    jenis_spk: string[];
    document_type: string[];
    teknisi: string[];
    vendors: string[];
    jasa: string[];
    lokasi: string[];
    statuses: string[];
}

interface CustomerSummary {
    no_jaringan: string;
    nama_pelanggan: string;
    lokasi: string;
    spk_count: number;
}

// ============================================
// MAIN COMPONENT
// ============================================

export default function Search() {
    // State
    const [keyword, setKeyword] = useState('');
    const [showFilters, setShowFilters] = useState(false);
    const [loading, setLoading] = useState(false);
    const [loadingFilters, setLoadingFilters] = useState(false);
    
    const [results, setResults] = useState<SPKData[]>([]);
    const [filterOptions, setFilterOptions] = useState<FilterOptions>({
        jenis_spk: [],
        document_type: [],
        teknisi: [],
        vendors: [],
        jasa: [],
        lokasi: [],
        statuses: [],
    });
    const [customers, setCustomers] = useState<CustomerSummary[]>([]);
    
    const [filters, setFilters] = useState({
        jenis_spk: '',
        document_type: '',
        teknisi: '',
        vendor: '',
        jasa: '',
        lokasi: '',
        status: '',
        date_from: '',
        date_to: '',
    });

    const [pagination, setPagination] = useState({
        total: 0,
        current_page: 1,
        last_page: 1,
        per_page: 20,
    });

    // ========================================
    // FETCH FUNCTIONS
    // ========================================

    const fetchFilterOptions = async () => {
        setLoadingFilters(true);
        try {
            const params = { keyword, ...filters };
            const response = await axios.get('/search/api/filter-options', { params });
            
            setFilterOptions(response.data.options);
        } catch (error) {
            console.error('Error fetching filter options:', error);
        } finally {
            setLoadingFilters(false);
        }
    };

    const fetchCustomerSummary = async () => {
        if (!keyword) {
            setCustomers([]);
            return;
        }

        try {
            const params = { keyword, ...filters };
            const response = await axios.get('/search/api/customer-summary', { params });
            setCustomers(response.data.customers || []);
        } catch (error) {
            console.error('Error fetching customer summary:', error);
        }
    };

    const fetchSearchResults = async (page = 1) => {
        setLoading(true);
        try {
            const params = { 
                keyword, 
                ...filters, 
                page,
                per_page: pagination.per_page 
            };
            
            const response = await axios.get('/search/api/search', { params });
            
            setResults(response.data.data);
            setPagination({
                total: response.data.pagination.total,
                current_page: response.data.pagination.current_page,
                last_page: response.data.pagination.last_page,
                per_page: response.data.pagination.per_page,
            });

            toast.success(`Ditemukan ${response.data.pagination.total} dokumen`, {
                duration: 2000,
                classNames: {
                    toast: '!bg-gray-900 !border-2 !border-green-400',
                    title: '!text-white !text-sm',
                    icon: '!text-green-500',
                },
            });
        } catch (error) {
            console.error('Error searching:', error);
            toast.error('Gagal melakukan pencarian', {
                duration: 3000,
                classNames: {
                    toast: '!bg-gray-900 !border-2 !border-red-400',
                    title: '!text-white',
                    icon: '!text-red-500',
                },
            });
        } finally {
            setLoading(false);
        }
    };

    // ========================================
    // EFFECTS
    // ========================================

    // Fetch filter options when keyword or filters change
    useEffect(() => {
        const debounce = setTimeout(() => {
            if (keyword || Object.values(filters).some(v => v)) {
                fetchFilterOptions();
                fetchCustomerSummary();
            }
        }, 300);

        return () => clearTimeout(debounce);
    }, [keyword, filters]);

    // Auto search when keyword changes
    useEffect(() => {
        const debounce = setTimeout(() => {
            if (keyword) {
                fetchSearchResults();
            } else {
                setResults([]);
                setCustomers([]);
            }
        }, 500);

        return () => clearTimeout(debounce);
    }, [keyword]);

    // ========================================
    // HANDLERS
    // ========================================

    const handleFilterChange = (key: string, value: string) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    };

    const handleSearch = () => {
        fetchSearchResults();
    };

    const handleResetFilters = () => {
        setFilters({
            jenis_spk: '',
            document_type: '',
            teknisi: '',
            vendor: '',
            jasa: '',
            lokasi: '',
            status: '',
            date_from: '',
            date_to: '',
        });
        setKeyword('');
        setResults([]);
        setCustomers([]);
        toast.info('Filter direset', {
            duration: 2000,
            classNames: {
                toast: '!bg-gray-900 !border-2 !border-blue-400',
                title: '!text-white',
                icon: '!text-blue-400',
            },
        });
    };

    const activeFilterCount = Object.values(filters).filter(v => v !== '').length;

    const getJenisSPKBadge = (jenis: string) => {
        const colors: Record<string, string> = {
            instalasi: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            aktivasi: 'bg-green-500/20 text-green-400 border-green-500/30',
            maintenance: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
            survey: 'bg-purple-500/20 text-purple-400 border-purple-500/30',
            dismantle: 'bg-red-500/20 text-red-400 border-red-500/30',
        };
        return colors[jenis] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    };

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    // ========================================
    // RENDER
    // ========================================

    return (
        <AppLayout>
            <Head title="Pencarian Dokumen SPK" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-white">Pencarian Dokumen SPK</h1>
                        <p className="text-sm text-gray-400 mt-1">
                            Cari berdasarkan No Jaringan, No SPK, Pelanggan, Teknisi, atau Vendor
                        </p>
                    </div>
                </div>

                {/* Search Bar */}
                <div className="flex gap-3">
                    <div className="relative flex-1">
                        <MagnifyingGlassIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Ketik No Jaringan (contoh: NOJAR-123), No SPK, Nama Pelanggan..."
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                            className="w-full rounded-full bg-gray-800 py-3 pl-12 pr-4 text-white placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none"
                        />
                    </div>
                    <button
                        onClick={() => setShowFilters(!showFilters)}
                        className={`flex items-center gap-2 rounded-full px-6 py-3 font-medium transition-all ${
                            showFilters
                                ? 'bg-purple-500/20 text-purple-400 border-2 border-purple-500/30'
                                : 'bg-gray-800 text-gray-300 border-2 border-gray-700 hover:bg-gray-700'
                        }`}
                    >
                        <FunnelIcon className="h-5 w-5" />
                        Filter
                        {activeFilterCount > 0 && (
                            <span className="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-purple-500 text-xs text-white">
                                {activeFilterCount}
                            </span>
                        )}
                    </button>
                </div>

                {/* Search Info */}
                {keyword && (
                    <div className="flex items-center gap-4 text-sm text-gray-400">
                        <span>
                            Ditemukan: <strong className="text-white">{pagination.total} dokumen</strong>
                            {customers.length > 0 && ` dari ${customers.length} pelanggan`}
                        </span>
                        {(keyword || activeFilterCount > 0) && (
                            <button
                                onClick={handleResetFilters}
                                className="flex items-center gap-1 text-purple-400 hover:text-purple-300"
                            >
                                <XMarkIcon className="h-4 w-4" />
                                Reset Pencarian
                            </button>
                        )}
                    </div>
                )}

                {/* Advanced Filters */}
                {showFilters && (
                    <div className="rounded-2xl border-2 border-gray-700 bg-gray-900 p-6 shadow-lg">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-white">Filter Lanjutan</h3>
                            {activeFilterCount > 0 && (
                                <button
                                    onClick={handleResetFilters}
                                    className="flex items-center gap-1 text-sm text-gray-400 hover:text-gray-300"
                                >
                                    <ArrowPathIcon className="h-4 w-4" />
                                    Reset Filter
                                </button>
                            )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {/* Jenis SPK */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Jenis SPK ({filterOptions.jenis_spk.length})
                                </label>
                                <select
                                    value={filters.jenis_spk}
                                    onChange={(e) => handleFilterChange('jenis_spk', e.target.value)}
                                    disabled={loadingFilters || filterOptions.jenis_spk.length === 0}
                                    className="w-full rounded-lg bg-gray-800 px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <option value="">Semua Jenis</option>
                                    {filterOptions.jenis_spk.map(jenis => (
                                        <option key={jenis} value={jenis}>
                                            {jenis.charAt(0).toUpperCase() + jenis.slice(1)}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Document Type */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Tipe Dokumen ({filterOptions.document_type.length})
                                </label>
                                <select
                                    value={filters.document_type}
                                    onChange={(e) => handleFilterChange('document_type', e.target.value)}
                                    disabled={loadingFilters || filterOptions.document_type.length === 0}
                                    className="w-full rounded-lg bg-gray-800 px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <option value="">Semua Tipe</option>
                                    {filterOptions.document_type.map(type => (
                                        <option key={type} value={type}>
                                            {type === 'spk' ? 'SPK' : 
                                             type === 'form_checklist_wireline' ? 'Form Wireline' : 
                                             'Form Wireless'}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Teknisi */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Teknisi ({filterOptions.teknisi.length})
                                </label>
                                <select
                                    value={filters.teknisi}
                                    onChange={(e) => handleFilterChange('teknisi', e.target.value)}
                                    disabled={loadingFilters || filterOptions.teknisi.length === 0}
                                    className="w-full rounded-lg bg-gray-800 px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <option value="">Semua Teknisi</option>
                                    {filterOptions.teknisi.map(tek => (
                                        <option key={tek} value={tek}>{tek}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Vendor */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Vendor ({filterOptions.vendors.length})
                                </label>
                                <select
                                    value={filters.vendor}
                                    onChange={(e) => handleFilterChange('vendor', e.target.value)}
                                    disabled={loadingFilters || filterOptions.vendors.length === 0}
                                    className="w-full rounded-lg bg-gray-800 px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <option value="">Semua Vendor</option>
                                    {filterOptions.vendors.map(ven => (
                                        <option key={ven} value={ven}>{ven}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Jasa */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Jasa ({filterOptions.jasa.length})
                                </label>
                                <select
                                    value={filters.jasa}
                                    onChange={(e) => handleFilterChange('jasa', e.target.value)}
                                    disabled={loadingFilters || filterOptions.jasa.length === 0}
                                    className="w-full rounded-lg bg-gray-800 px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <option value="">Semua Jasa</option>
                                    {filterOptions.jasa.map(j => (
                                        <option key={j} value={j}>{j}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Status */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Status ({filterOptions.statuses.length})
                                </label>
                                <select
                                    value={filters.status}
                                    onChange={(e) => handleFilterChange('status', e.target.value)}
                                    disabled={loadingFilters || filterOptions.statuses.length === 0}
                                    className="w-full rounded-lg bg-gray-800 px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <option value="">Semua Status</option>
                                    {filterOptions.statuses.map(stat => (
                                        <option key={stat} value={stat}>
                                            {stat.charAt(0).toUpperCase() + stat.slice(1)}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Date From */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Tanggal Dari
                                </label>
                                <div className="relative">
                                    <CalendarIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="date"
                                        value={filters.date_from}
                                        onChange={(e) => handleFilterChange('date_from', e.target.value)}
                                        className="w-full rounded-lg bg-gray-800 py-2 pl-10 pr-3 text-white focus:ring-2 focus:ring-purple-500"
                                    />
                                </div>
                            </div>

                            {/* Date To */}
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-300">
                                    Tanggal Sampai
                                </label>
                                <div className="relative">
                                    <CalendarIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="date"
                                        value={filters.date_to}
                                        onChange={(e) => handleFilterChange('date_to', e.target.value)}
                                        className="w-full rounded-lg bg-gray-800 py-2 pl-10 pr-3 text-white focus:ring-2 focus:ring-purple-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="mt-4 pt-4 border-t border-gray-700">
                            <p className="text-sm text-gray-400">
                                💡 <strong>Tips:</strong> Filter hanya menampilkan opsi yang tersedia dari hasil pencarian Anda
                            </p>
                        </div>

                        <div className="mt-4 flex justify-end">
                            <button
                                onClick={handleSearch}
                                disabled={loading}
                                className="rounded-full bg-gradient-to-r from-purple-400 to-pink-400 px-6 py-2 text-white font-medium shadow transition hover:brightness-110 disabled:opacity-50"
                            >
                                {loading ? 'Mencari...' : 'Terapkan Filter'}
                            </button>
                        </div>
                    </div>
                )}

                {/* Customer Summary */}
                {keyword && customers.length > 0 && (
                    <div className="rounded-2xl border-2 border-gray-700 bg-gray-900 p-6">
                        <h3 className="mb-4 text-sm font-semibold text-gray-300">
                            Pelanggan yang Ditemukan:
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                            {customers.map(customer => (
                                <div
                                    key={customer.no_jaringan}
                                    className="flex items-center justify-between rounded-lg border border-purple-500/30 bg-purple-500/10 p-4"
                                >
                                    <div className="flex-1">
                                        <div className="font-semibold text-white">{customer.nama_pelanggan}</div>
                                        <div className="text-sm text-gray-400">
                                            {customer.no_jaringan} • {customer.lokasi}
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="text-2xl font-bold text-purple-400">{customer.spk_count}</div>
                                        <div className="text-xs text-gray-400">SPK</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Results */}
                <div className="rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-lg">
                    {/* Results Header */}
                    <div className="flex items-center justify-between border-b border-gray-700 p-4">
                        <div>
                            <h3 className="text-lg font-semibold text-white">
                                Hasil: {pagination.total} Dokumen
                            </h3>
                            {activeFilterCount > 0 && (
                                <p className="mt-1 text-sm text-gray-400">
                                    Dengan {activeFilterCount} filter aktif
                                </p>
                            )}
                        </div>
                        <button className="flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 px-4 py-2 text-white shadow transition hover:brightness-110">
                            <ArrowDownTrayIcon className="h-4 w-4" />
                            Export
                        </button>
                    </div>

                    {/* Results List */}
                    <div className="divide-y divide-gray-800">
                        {loading ? (
                            <div className="p-12 text-center text-gray-500">
                                <div className="mx-auto mb-4 h-12 w-12 animate-spin rounded-full border-4 border-gray-700 border-t-purple-400"></div>
                                <p className="text-lg font-medium">Mencari dokumen...</p>
                            </div>
                        ) : results.length === 0 ? (
                            <div className="p-12 text-center text-gray-500">
                                <MagnifyingGlassIcon className="mx-auto mb-4 h-12 w-12 text-gray-600" />
                                <p className="text-lg font-medium">Tidak ada dokumen ditemukan</p>
                                <p className="mt-2 text-sm">Coba ubah kata kunci atau filter pencarian Anda</p>
                            </div>
                        ) : (
                            results.map(item => (
                                <div key={item.id_spk} className="p-4 transition-colors hover:bg-gray-800">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="mb-2 flex items-center gap-3">
                                                <h4 className="text-lg font-semibold text-white">{item.no_spk}</h4>
                                                <span className={`rounded-full border px-2 py-1 text-xs font-medium ${getJenisSPKBadge(item.jenis_spk)}`}>
                                                    {item.jenis_spk.toUpperCase()}
                                                </span>
                                                {item.upload && (
                                                    <span className={`rounded-full border px-2 py-1 text-xs font-medium ${
                                                        item.upload.status === 'completed' 
                                                            ? 'bg-green-500/20 text-green-400 border-green-500/30'
                                                            : 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30'
                                                    }`}>
                                                        {item.upload.status}
                                                    </span>
                                                )}
                                            </div>
                                            
                                            <div className="space-y-1 text-sm text-gray-400">
                                                {item.jaringan && (
                                                    <div className="flex items-center gap-2">
                                                        <span className="font-medium text-white">{item.jaringan.nama_pelanggan}</span>
                                                        <span>•</span>
                                                        <span className="font-mono text-purple-400">{item.no_jaringan}</span>
                                                    </div>
                                                )}
                                                <div className="flex items-center gap-4 flex-wrap">
                                                    {item.jaringan && (
                                                        <span className="flex items-center gap-1">
                                                            <MapPinIcon className="h-4 w-4" />
                                                            {item.jaringan.lokasi_pelanggan}
                                                        </span>
                                                    )}
                                                    {item.execution_info && (
                                                        <>
                                                            <span className="flex items-center gap-1">
                                                                <UserIcon className="h-4 w-4" />
                                                                {item.execution_info.teknisi}
                                                            </span>
                                                            <span className="flex items-center gap-1">
                                                                <BuildingOfficeIcon className="h-4 w-4" />
                                                                {item.execution_info.nama_vendor}
                                                            </span>
                                                        </>
                                                    )}
                                                </div>
                                                <div className="flex items-center gap-2 text-gray-500">
                                                    <CalendarIcon className="h-4 w-4" />
                                                    {formatDate(item.tanggal_spk)}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {item.upload && (
                                            <button 
                                                onClick={() => item.upload && (window.location.href = `/documents/${item.upload.id_upload}/detail`)}
                                                className="ml-4 rounded-full bg-purple-500/20 p-2 text-purple-400 transition-colors hover:bg-purple-500/30"
                                            >
                                                <EyeIcon className="h-5 w-5" />
                                            </button>
                                        )}
                                    </div>
                                </div>
                            ))
                        )}
                    </div>

                    {/* Pagination */}
                    {pagination.last_page > 1 && (
                        <div className="flex items-center justify-between border-t border-gray-700 p-4">
                            <p className="text-sm text-gray-400">
                                Menampilkan {((pagination.current_page - 1) * pagination.per_page) + 1} - {Math.min(pagination.current_page * pagination.per_page, pagination.total)} dari {pagination.total} dokumen
                            </p>
                            <div className="flex items-center gap-2">
                                <button
                                    onClick={() => fetchSearchResults(pagination.current_page - 1)}
                                    disabled={pagination.current_page === 1}
                                    className="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white disabled:opacity-50 hover:bg-gray-700"
                                >
                                    Sebelumnya
                                </button>
                                <span className="px-4 py-2 text-sm text-gray-400">
                                    Halaman {pagination.current_page} dari {pagination.last_page}
                                </span>
                                <button
                                    onClick={() => fetchSearchResults(pagination.current_page + 1)}
                                    disabled={pagination.current_page === pagination.last_page}
                                    className="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white disabled:opacity-50 hover:bg-gray-700"
                                >
                                    Selanjutnya
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}