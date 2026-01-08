<?php

namespace App\Http\Controllers;

use App\Models\SPK;
use App\Models\Jaringan;
use App\Models\Document;
use App\Models\SpkExecutionInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DokumenSearchController extends Controller
{
    /**
     * Halaman utama pencarian dokumen
     */
    public function index()
    {
        return Inertia::render('Documents/Search', [
            'initialStats' => $this->getQuickStats()
        ]);
    }

    /**
     * Main search endpoint dengan dynamic cascading filter
     */
    public function search(Request $request)
    {
        try {
            $query = SPK::query()
                ->with([
                    'jaringan:no_jaringan,nama_pelanggan,lokasi_pelanggan,jasa',
                    'executionInfo:id_spk,teknisi,nama_vendor,pic_pelanggan,kontak_pic_pelanggan',
                    'upload:id_upload,file_name,file_path,file_size,status,created_at'
                ])
                ->select(
                    'id_spk',
                    'no_spk',
                    'no_jaringan',
                    'jenis_spk',
                    'tanggal_spk',
                    'document_type',
                    'no_mr',
                    'no_fps',
                    'id_upload'
                )
                ->where('is_deleted', false);

            // ========================================
            // KEYWORD SEARCH (Primary Search)
            // ========================================
            $keyword = $request->input('keyword');

            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('no_spk', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_mr', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_fps', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_jaringan', 'LIKE', "%{$keyword}%")
                        // Search di tabel JARINGAN
                        ->orWhereHas('jaringan', function ($q) use ($keyword) {
                            $q->where('nama_pelanggan', 'LIKE', "%{$keyword}%")
                                ->orWhere('lokasi_pelanggan', 'LIKE', "%{$keyword}%")
                                ->orWhere('jasa', 'LIKE', "%{$keyword}%");
                        })
                        // Search di tabel execution_info
                        ->orWhereHas('executionInfo', function ($q) use ($keyword) {
                            $q->where('teknisi', 'LIKE', "%{$keyword}%")
                                ->orWhere('nama_vendor', 'LIKE', "%{$keyword}%")
                                ->orWhere('pic_pelanggan', 'LIKE', "%{$keyword}%");
                        });
                });
            }

            // ========================================
            // ADVANCED FILTERS (Cascading)
            // ========================================

            // Filter by Jenis SPK
            if ($jenis_spk = $request->input('jenis_spk')) {
                $query->where('jenis_spk', $jenis_spk);
            }

            // Filter by Document Type
            if ($document_type = $request->input('document_type')) {
                $query->where('document_type', $document_type);
            }

            // Filter by Teknisi
            if ($teknisi = $request->input('teknisi')) {
                $query->whereHas('executionInfo', function ($q) use ($teknisi) {
                    $q->where('teknisi', $teknisi);
                });
            }

            // Filter by Vendor
            if ($vendor = $request->input('vendor')) {
                $query->whereHas('executionInfo', function ($q) use ($vendor) {
                    $q->where('nama_vendor', $vendor);
                });
            }

            // Filter by Jasa
            if ($jasa = $request->input('jasa')) {
                $query->whereHas('jaringan', function ($q) use ($jasa) {
                    $q->where('jasa', $jasa);
                });
            }

            // Filter by Lokasi/Kota
            if ($lokasi = $request->input('lokasi')) {
                $query->whereHas('jaringan', function ($q) use ($lokasi) {
                    $q->where('lokasi_pelanggan', 'LIKE', "%{$lokasi}%");
                });
            }

            // Filter by Status Upload
            if ($status = $request->input('status')) {
                $query->whereHas('upload', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }

            // Filter by Date Range
            if ($date_from = $request->input('date_from')) {
                $query->where('tanggal_spk', '>=', $date_from);
            }
            if ($date_to = $request->input('date_to')) {
                $query->where('tanggal_spk', '<=', $date_to);
            }

            // Filter by No Jaringan (direct filter)
            if ($no_jaringan = $request->input('no_jaringan')) {
                $query->where('no_jaringan', $no_jaringan);
            }

            // ========================================
            // SORTING
            // ========================================
            $sortBy = $request->input('sort_by', 'tanggal_spk');
            $sortOrder = $request->input('sort_order', 'desc');

            $allowedSortFields = ['tanggal_spk', 'no_spk', 'jenis_spk', 'created_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // ========================================
            // PAGINATION
            // ========================================
            $perPage = min((int)$request->input('per_page', 20), 100); // Max 100 per page
            $results = $query->paginate($perPage);

            // ========================================
            // TRANSFORM RESULTS
            // ========================================
            $transformedData = $results->map(function ($spk) {
                return [
                    'id_spk' => $spk->id_spk,
                    'no_spk' => $spk->no_spk,
                    'no_jaringan' => $spk->no_jaringan,
                    'jenis_spk' => $spk->jenis_spk,
                    'document_type' => $spk->document_type,
                    'tanggal_spk' => $spk->tanggal_spk?->format('Y-m-d'),
                    'no_mr' => $spk->no_mr,
                    'no_fps' => $spk->no_fps,
                    'jaringan' => $spk->jaringan ? [
                        'no_jaringan' => $spk->jaringan->no_jaringan,
                        'nama_pelanggan' => $spk->jaringan->nama_pelanggan,
                        'lokasi_pelanggan' => $spk->jaringan->lokasi_pelanggan,
                        'jasa' => $spk->jaringan->jasa,
                    ] : null,
                    'execution_info' => $spk->executionInfo ? [
                        'teknisi' => $spk->executionInfo->teknisi,
                        'nama_vendor' => $spk->executionInfo->nama_vendor,
                        'pic_pelanggan' => $spk->executionInfo->pic_pelanggan,
                    ] : null,
                    'upload' => $spk->upload ? [
                        'id_upload' => $spk->upload->id_upload,
                        'file_name' => $spk->upload->file_name,
                        'status' => $spk->upload->status,
                        'file_size' => $spk->upload->file_size,
                    ] : null,
                ];
            });

            Log::info('Search dokumen berhasil', [
                'keyword' => $keyword,
                'total_results' => $results->total(),
                'filters' => $request->only([
                    'jenis_spk',
                    'document_type',
                    'teknisi',
                    'vendor',
                    'status',
                    'date_from',
                    'date_to'
                ])
            ]);

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'pagination' => [
                    'total' => $results->total(),
                    'per_page' => $results->perPage(),
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'from' => $results->firstItem(),
                    'to' => $results->lastItem(),
                ],
                'meta' => [
                    'keyword' => $keyword,
                    'filters_applied' => $this->getAppliedFilters($request),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat search dokumen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan pencarian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dynamic filter options berdasarkan hasil pencarian
     * INI YANG BIKIN FILTER CASCADE & MENYESUAIKAN
     */
    public function getFilterOptions(Request $request)
    {
        try {
            // Build INDEPENDENT queries for each filter to allow switching
            $keywordQuery = SPK::query()->where('is_deleted', false);

            // Apply keyword search to base
            $keyword = $request->input('keyword');
            if ($keyword) {
                $keywordQuery->where(function ($q) use ($keyword) {
                    $q->where('no_spk', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_mr', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_fps', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_jaringan', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('jaringan', function ($q) use ($keyword) {
                            $q->where('nama_pelanggan', 'LIKE', "%{$keyword}%")
                                ->orWhere('lokasi_pelanggan', 'LIKE', "%{$keyword}%")
                                ->orWhere('jasa', 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('executionInfo', function ($q) use ($keyword) {
                            $q->where('teknisi', 'LIKE', "%{$keyword}%")
                                ->orWhere('nama_vendor', 'LIKE', "%{$keyword}%");
                        });
                });
            }

            // Apply date filters to base (these always apply)
            if ($date_from = $request->input('date_from')) {
                $keywordQuery->where('tanggal_spk', '>=', $date_from);
            }
            if ($date_to = $request->input('date_to')) {
                $keywordQuery->where('tanggal_spk', '<=', $date_to);
            }

            // Get base SPK IDs (keyword + date filters only)
            $baseSpkIds = $keywordQuery->pluck('id_spk');

            // Now build filter options based on OTHER filters (not the current one)
            // This allows users to switch between options freely
            
            // For each filter, exclude that filter itself but include others
            $currentFilters = [
                'jenis_spk' => $request->input('jenis_spk'),
                'document_type' => $request->input('document_type'),
                'teknisi' => $request->input('teknisi'),
                'vendor' => $request->input('vendor'),
                'jasa' => $request->input('jasa'),
                'lokasi' => $request->input('lokasi'),
            ];

            // Helper function to build query excluding specific filter
            $buildQueryExcluding = function($excludeFilter) use ($baseSpkIds, $currentFilters) {
                $query = SPK::whereIn('id_spk', $baseSpkIds);
                
                foreach ($currentFilters as $filter => $value) {
                    if ($filter === $excludeFilter || !$value) continue;
                    
                    switch ($filter) {
                        case 'jenis_spk':
                            $query->where('jenis_spk', $value);
                            break;
                        case 'document_type':
                            $query->where('document_type', $value);
                            break;
                        case 'teknisi':
                            $query->whereHas('executionInfo', fn($q) => $q->where('teknisi', $value));
                            break;
                        case 'vendor':
                            $query->whereHas('executionInfo', fn($q) => $q->where('nama_vendor', $value));
                            break;
                        case 'jasa':
                            $query->whereHas('jaringan', fn($q) => $q->where('jasa', $value));
                            break;
                        case 'lokasi':
                            $query->whereHas('jaringan', fn($q) => $q->where('lokasi_pelanggan', 'LIKE', "%{$value}%"));
                            break;
                    }
                }
                
                return $query->pluck('id_spk');
            };

            // Get available options for each filter (excluding itself)
            $spkIdsForJenisSpk = $buildQueryExcluding('jenis_spk');
            $spkIdsForDocType = $buildQueryExcluding('document_type');
            $spkIdsForTeknisi = $buildQueryExcluding('teknisi');
            $spkIdsForVendor = $buildQueryExcluding('vendor');
            $spkIdsForJasa = $buildQueryExcluding('jasa');
            $spkIdsForLokasi = $buildQueryExcluding('lokasi');

            // Get final SPK IDs with ALL filters applied (for counts)
            $spkIds = $buildQueryExcluding(null);

            // ========================================
            // DYNAMIC FILTER OPTIONS
            // ========================================

            $options = [
                // Jenis SPK - show all available when this filter is NOT selected
                'jenis_spk' => SPK::whereIn('id_spk', $spkIdsForJenisSpk)
                    ->distinct()
                    ->pluck('jenis_spk')
                    ->filter()
                    ->sort()
                    ->values(),

                // Document Type
                'document_type' => SPK::whereIn('id_spk', $spkIdsForDocType)
                    ->distinct()
                    ->pluck('document_type')
                    ->filter()
                    ->sort()
                    ->values(),

                // Teknisi
                'teknisi' => SpkExecutionInfo::whereIn('id_spk', $spkIdsForTeknisi)
                    ->distinct()
                    ->pluck('teknisi')
                    ->filter()
                    ->sort()
                    ->values(),

                // Vendor
                'vendors' => SpkExecutionInfo::whereIn('id_spk', $spkIdsForVendor)
                    ->distinct()
                    ->pluck('nama_vendor')
                    ->filter()
                    ->sort()
                    ->values(),

                // Jasa
                'jasa' => Jaringan::whereIn('no_jaringan', function ($query) use ($spkIdsForJasa) {
                    $query->select('no_jaringan')
                        ->from('SPK')
                        ->whereIn('id_spk', $spkIdsForJasa);
                })
                    ->distinct()
                    ->pluck('jasa')
                    ->filter()
                    ->sort()
                    ->values(),

                // Lokasi
                'lokasi' => Jaringan::whereIn('no_jaringan', function ($query) use ($spkIdsForLokasi) {
                    $query->select('no_jaringan')
                        ->from('SPK')
                        ->whereIn('id_spk', $spkIdsForLokasi);
                })
                    ->distinct()
                    ->pluck('lokasi_pelanggan')
                    ->filter()
                    ->sort()
                    ->values(),

                // Status upload
                'statuses' => Document::whereIn('id_upload', function ($query) use ($spkIds) {
                    $query->select('id_upload')
                        ->from('SPK')
                        ->whereIn('id_spk', $spkIds)
                        ->whereNotNull('id_upload');
                })
                    ->distinct()
                    ->pluck('status')
                    ->filter()
                    ->sort()
                    ->values(),
            ];

            // ========================================
            // COUNTS (berapa banyak dari setiap option)
            // ========================================
            $counts = [
                'jenis_spk' => $options['jenis_spk']->count(),
                'document_type' => $options['document_type']->count(),
                'teknisi' => $options['teknisi']->count(),
                'vendors' => $options['vendors']->count(),
                'jasa' => $options['jasa']->count(),
                'lokasi' => $options['lokasi']->count(),
                'statuses' => $options['statuses']->count(),
            ];

            // ========================================
            // DATE RANGE (min/max dari hasil)
            // ========================================
            $dateRange = SPK::whereIn('id_spk', $spkIds)
                ->selectRaw('MIN(tanggal_spk) as min_date, MAX(tanggal_spk) as max_date')
                ->first();

            return response()->json([
                'success' => true,
                'options' => $options,
                'counts' => $counts,
                'date_range' => [
                    'min' => $dateRange->min_date,
                    'max' => $dateRange->max_date,
                ],
                'total_results' => $spkIds->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat get filter options', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil filter options',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer summary berdasarkan hasil pencarian
     */
    public function getCustomerSummary(Request $request)
    {
        try {
            $baseQuery = SPK::query()->where('is_deleted', false);

            // Apply keyword search
            $keyword = $request->input('keyword');
            if ($keyword) {
                $baseQuery->where(function ($q) use ($keyword) {
                    $q->where('no_spk', 'LIKE', "%{$keyword}%")
                        ->orWhere('no_jaringan', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('jaringan', function ($q) use ($keyword) {
                            $q->where('nama_pelanggan', 'LIKE', "%{$keyword}%")
                                ->orWhere('lokasi_pelanggan', 'LIKE', "%{$keyword}%");
                        });
                });
            }

            // Apply filters
            $this->applyFiltersToQuery($baseQuery, $request);

            // Group by no_jaringan dan hitung SPK
            $customerSummary = $baseQuery
                ->with('jaringan:no_jaringan,nama_pelanggan,lokasi_pelanggan')
                ->select('no_jaringan', DB::raw('COUNT(*) as spk_count'))
                ->groupBy('no_jaringan')
                ->get()
                ->map(function ($item) {
                    return [
                        'no_jaringan' => $item->no_jaringan,
                        'nama_pelanggan' => $item->jaringan->nama_pelanggan ?? '-',
                        'lokasi' => $item->jaringan->lokasi_pelanggan ?? '-',
                        'spk_count' => $item->spk_count,
                    ];
                });

            return response()->json([
                'success' => true,
                'customers' => $customerSummary,
                'total_customers' => $customerSummary->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat get customer summary', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick stats untuk dashboard
     */
    public function getQuickStats()
    {
        return [
            'total_spk' => SPK::where('is_deleted', false)->count(),
            'total_customers' => Jaringan::distinct('no_jaringan')->count(),
            'this_month' => SPK::whereMonth('tanggal_spk', now()->month)
                ->where('is_deleted', false)
                ->count(),
            'today' => SPK::whereDate('tanggal_spk', today())
                ->where('is_deleted', false)
                ->count(),
        ];
    }

    /**
     * Export hasil pencarian
     */
    public function export(Request $request)
    {
        // TODO: Implementasi export ke Excel/CSV
        // Bisa pakai Laravel Excel atau manual CSV

        return response()->json([
            'success' => false,
            'message' => 'Export feature coming soon'
        ], 501);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function getAppliedFilters(Request $request): array
    {
        $filters = [];

        $filterKeys = [
            'jenis_spk',
            'document_type',
            'teknisi',
            'vendor',
            'jasa',
            'lokasi',
            'status',
            'date_from',
            'date_to',
            'no_jaringan'
        ];

        foreach ($filterKeys as $key) {
            if ($value = $request->input($key)) {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    private function applyFiltersToQuery($query, Request $request)
    {
        if ($jenis_spk = $request->input('jenis_spk')) {
            $query->where('jenis_spk', $jenis_spk);
        }
        if ($document_type = $request->input('document_type')) {
            $query->where('document_type', $document_type);
        }
        if ($teknisi = $request->input('teknisi')) {
            $query->whereHas('executionInfo', fn($q) => $q->where('teknisi', $teknisi));
        }
        if ($vendor = $request->input('vendor')) {
            $query->whereHas('executionInfo', fn($q) => $q->where('nama_vendor', $vendor));
        }
        if ($jasa = $request->input('jasa')) {
            $query->whereHas('jaringan', fn($q) => $q->where('jasa', $jasa));
        }
        if ($date_from = $request->input('date_from')) {
            $query->where('tanggal_spk', '>=', $date_from);
        }
        if ($date_to = $request->input('date_to')) {
            $query->where('tanggal_spk', '<=', $date_to);
        }
    }
}