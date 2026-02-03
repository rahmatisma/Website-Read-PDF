<?php

namespace App\Http\Controllers;

use App\Models\InspectionForm;
use App\Models\BatteryBankMetadata;
use App\Models\Document;
use App\Models\Location;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class BatteryDataController extends Controller
{
    /**
     *  Get battery chart data by upload_id
     */
    public function getBatteryChartDataByUpload($uploadId)
    {
        try {
            Log::info('🔍 Fetching battery chart data', ['upload_id' => $uploadId]);
            
            // 1. Get document
            $document = Document::findOrFail($uploadId);
            
            // 2. Extract and decode data (handle both string and array)
            if (is_string($document->extracted_data)) {
                $extractedData = json_decode($document->extracted_data, true);
            } elseif (is_array($document->extracted_data)) {
                $extractedData = $document->extracted_data;
            } else {
                $extractedData = [];
            }
            
            if (empty($extractedData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Extracted data is empty'
                ], 400);
            }
            
            Log::info('📊 Extracted data structure', [
                'upload_id' => $uploadId,
                'top_level_keys' => array_keys($extractedData),
            ]);
            
            // 3. Try multiple possible paths to get parsed data
            $parsedData = null;
            $possiblePaths = [
                ['parsed', 'data'],                   //  CORRECT for form_pm_battery
                ['data', 'parsed', 'data'],           // Standard untuk form lain
                ['data', 'parsed', 'parsed', 'data'], // Double 'parsed'
                ['data'],                             // Direct data
            ];
            
            foreach ($possiblePaths as $path) {
                $temp = $extractedData;
                $found = true;
                
                foreach ($path as $key) {
                    if (isset($temp[$key]) && is_array($temp[$key])) {
                        $temp = $temp[$key];
                    } else {
                        $found = false;
                        break;
                    }
                }
                
                if ($found && is_array($temp)) {
                    $parsedData = $temp;
                    Log::info(' Found parsed data', [
                        'path' => implode('.', $path),
                        'keys' => array_keys($temp),
                    ]);
                    break;
                }
            }
            
            if (!$parsedData) {
                Log::error('Could not find parsed data', [
                    'upload_id' => $uploadId,
                    'available_keys' => array_keys($extractedData),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Could not parse document structure',
                    'debug' => [
                        'available_keys' => array_keys($extractedData),
                        'tried_paths' => $possiblePaths,
                    ]
                ], 400);
            }
            
            // 4. Try multiple paths to get location
            $locationName = null;
            $locationPaths = [
                ['informasi_umum', 'location'],
                ['general_info', 'location'],
                ['location'],
                ['header', 'location'],
            ];
            
            foreach ($locationPaths as $path) {
                $temp = $parsedData;
                $found = true;
                
                foreach ($path as $key) {
                    if (isset($temp[$key])) {
                        $temp = $temp[$key];
                    } else {
                        $found = false;
                        break;
                    }
                }
                
                if ($found && is_string($temp) && !empty($temp)) {
                    $locationName = $temp;
                    Log::info(' Found location', [
                        'path' => implode('.', $path),
                        'location' => $locationName,
                    ]);
                    break;
                }
            }
            
            if (!$locationName) {
                Log::error('Location not found in parsed data', [
                    'upload_id' => $uploadId,
                    'parsed_data_keys' => array_keys($parsedData),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found in document data',
                    'debug' => [
                        'parsed_data_keys' => array_keys($parsedData),
                        'tried_location_paths' => $locationPaths,
                    ]
                ], 400);
            }
            
            Log::info('📍 Processing location', ['location' => $locationName]);
            
            // 5. Find location in database
            $location = Location::where('location_name', $locationName)->first();
            
            if (!$location) {
                Log::warning('⚠️ Location not found in database', [
                    'location_name' => $locationName,
                    'upload_id' => $uploadId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found in database. Battery data may not have been processed yet.',
                    'debug' => [
                        'location_name' => $locationName,
                        'suggestion' => 'Make sure FormPmPopService has run successfully'
                    ]
                ], 404);
            }
            
            // 6. Find most recent inspection at this location
            $inspection = Inspection::where('location_id', $location->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$inspection) {
                Log::error('No inspection found', [
                    'location_id' => $location->id,
                    'location_name' => $locationName,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No inspection found for this location'
                ], 404);
            }
            
            // 7. Get inspection form with battery banks
            $inspectionForm = InspectionForm::where('inspection_id', $inspection->id)
                ->with([
                    'batteryBanks.measurements' => function($query) {
                        $query->orderBy('cell_number', 'asc');
                    }
                ])
                ->first();
            
            if (!$inspectionForm) {
                Log::error('No inspection form found', [
                    'inspection_id' => $inspection->id,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No inspection form found'
                ], 404);
            }
            
            $batteryBanks = $inspectionForm->batteryBanks;
            
            if ($batteryBanks->isEmpty()) {
                Log::error('No battery banks found', [
                    'inspection_form_id' => $inspectionForm->id,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No battery data found for this inspection'
                ], 404);
            }
            
            // 8. Transform data untuk charts
            $chartData = $this->transformForCharts($batteryBanks);
            
            Log::info(' Battery chart data generated successfully', [
                'upload_id' => $uploadId,
                'inspection_id' => $inspection->id,
                'inspection_form_id' => $inspectionForm->id,
                'banks_count' => $batteryBanks->count(),
                'total_cells' => $chartData['metadata']['total_cells']
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $chartData,
                'meta' => [
                    'inspection_id' => $inspection->id,
                    'inspection_form_id' => $inspectionForm->id,
                    'location' => $locationName,
                    'inspection_date' => $inspection->inspection_date->format('Y-m-d'),
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Document not found', ['upload_id' => $uploadId]);
            
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate battery chart data', [
                'upload_id' => $uploadId,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate chart data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    private function transformForCharts($batteryBanks)
    {
        $voltageSeries = collect();
        $sohSeries = collect();
        
        $maxCellNumber = $batteryBanks->flatMap(function($bank) {
            return $bank->measurements;
        })->max('cell_number') ?? 20;
        
        for ($cellNum = 1; $cellNum <= $maxCellNumber; $cellNum++) {
            $voltageRow = ['cellNumber' => $cellNum];
            $sohRow = ['cellNumber' => $cellNum];
            
            foreach ($batteryBanks as $bank) {
                $measurement = $bank->measurements->firstWhere('cell_number', $cellNum);
                $bankLabel = $bank->bank_name;
                
                $voltageRow[$bankLabel] = $measurement ? (float)$measurement->voltage : null;
                $sohRow[$bankLabel] = $measurement ? (int)$measurement->soh : null;
            }
            
            $voltageSeries->push($voltageRow);
            $sohSeries->push($sohRow);
        }
        
        $bankSummary = $batteryBanks->map(function($bank) {
            $measurements = $bank->measurements;
            $age = null;
            
            if ($bank->production_date) {
                $age = $bank->production_date->diffInYears(now());
            }
            
            return [
                'bank_name' => $bank->bank_name,
                'bank_type' => $bank->battery_type,
                'battery_brand' => $bank->battery_brand,
                'battery_capacity' => $bank->battery_capacity,
                'production_date' => $bank->production_date ? $bank->production_date->format('d/m/Y') : null,
                'battery_age_years' => $age,
                'avg_voltage' => round($measurements->avg('voltage'), 2),
                'min_voltage' => round($measurements->min('voltage'), 2),
                'max_voltage' => round($measurements->max('voltage'), 2),
                'avg_soh' => round($measurements->avg('soh'), 1),
                'min_soh' => $measurements->min('soh'),
                'max_soh' => $measurements->max('soh'),
                'cells_below_12v' => $measurements->where('voltage', '<', 12.0)->count(),
                'cells_below_80_soh' => $measurements->where('soh', '<', 80)->count(),
                'total_cells' => $measurements->count(),
                'health_status' => $this->calculateHealthStatus($measurements),
            ];
        })->values();
        
        $bankNames = $batteryBanks->pluck('bank_name')->unique()->values();
        
        return [
            'voltage_chart' => $voltageSeries,
            'soh_chart' => $sohSeries,
            'bank_summary' => $bankSummary,
            'bank_names' => $bankNames,
            'metadata' => [
                'total_banks' => $batteryBanks->count(),
                'total_cells' => $batteryBanks->sum(fn($b) => $b->measurements->count()),
                'max_cell_number' => $maxCellNumber,
                'overall_health' => $this->calculateOverallHealth($batteryBanks),
            ]
        ];
    }
    
    private function calculateHealthStatus($measurements)
    {
        $avgSoh = $measurements->avg('soh');
        $minVoltage = $measurements->min('voltage');
        
        if ($avgSoh >= 80 && $minVoltage >= 12.0) {
            return 'EXCELLENT';
        } elseif ($avgSoh >= 70 && $minVoltage >= 11.8) {
            return 'GOOD';
        } elseif ($avgSoh >= 60 && $minVoltage >= 11.5) {
            return 'FAIR';
        } else {
            return 'NEEDS_REPLACEMENT';
        }
    }
    
    private function calculateOverallHealth($batteryBanks)
    {
        $totalMeasurements = $batteryBanks->flatMap(fn($b) => $b->measurements);
        $avgSoh = $totalMeasurements->avg('soh');
        $cellsBelowThreshold = $totalMeasurements->where('soh', '<', 80)->count();
        $totalCells = $totalMeasurements->count();
        $healthPercentage = ($totalCells - $cellsBelowThreshold) / $totalCells * 100;
        
        return [
            'avg_soh' => round($avgSoh, 1),
            'cells_healthy' => $totalCells - $cellsBelowThreshold,
            'cells_total' => $totalCells,
            'health_percentage' => round($healthPercentage, 1),
            'status' => $healthPercentage >= 80 ? 'HEALTHY' : ($healthPercentage >= 60 ? 'WARNING' : 'CRITICAL'),
        ];
    }

    public function getSummary() {
        return response()->json([
            'summary' => [
                'sehat' => Battery::where('status', 'sehat')->count(),
                'warning' => Battery::where('status', 'warning')->count(),
                'kritis' => Battery::where('status', 'kritis')->count(),
            ],
            'criticalBatteries' => Battery::where('status', 'kritis')
                ->orWhere('status', 'warning')
                ->orderBy('soh', 'asc')
                ->limit(5)
                ->get()
        ]);
    }
    
    public function getBatteryBankDetail($batteryBankId)
    {
        try {
            $batteryBank = BatteryBankMetadata::with('measurements')->findOrFail($batteryBankId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'bank' => $batteryBank,
                    'statistics' => [
                        'avg_voltage' => $batteryBank->measurements->avg('voltage'),
                        'min_voltage' => $batteryBank->getMinVoltage(),
                        'avg_soh' => $batteryBank->getAverageSoh(),
                        'low_soh_cells' => $batteryBank->getLowSohCells()->count(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Battery bank not found'
            ], 404);
        }
    }

    /**
     *  GET semua battery summary untuk dashboard
     *  Fetch latest inspection per lokasi, hitung status per cell
     */
    public function getAllBatteryDashboardData()
    {
        try {
            // 1. Ambil semua lokasi yang punya inspection + battery data
            //    Grouping: latest inspection per location
            $inspections = Inspection::with([
                'location',
                'inspectionForms.batteryBanks.measurements' => function ($query) {
                    $query->orderBy('cell_number', 'asc');
                }
            ])
            ->whereHas('inspectionForms.batteryBanks.measurements')
            ->orderBy('inspection_date', 'desc')
            ->get();

            // 2. Ambil hanya latest inspection per location_id
            $latestPerLocation = [];
            foreach ($inspections as $inspection) {
                $locId = $inspection->location_id;
                if (!isset($latestPerLocation[$locId])) {
                    $latestPerLocation[$locId] = $inspection;
                }
            }

            // 3. Loop semua measurements, hitung status & kumpulkan data
            $summary = ['sehat' => 0, 'warning' => 0, 'kritis' => 0];
            $allBatteries = [];

            foreach ($latestPerLocation as $inspection) {
                $locationName = $inspection->location->location_name ?? 'Unknown';

                foreach ($inspection->inspectionForms as $form) {
                    foreach ($form->batteryBanks as $bank) {
                        foreach ($bank->measurements as $cell) {
                            $voltage = (float) $cell->voltage;
                            $soh     = (int)   $cell->soh;

                            // Hitung status
                            $status = $this->getCellStatus($voltage, $soh);
                            $summary[$status]++;

                            // Hitung trend: bandingin dg measurement sebelumnya
                            // (untuk simplisitas kita set 'stable' dulu,
                            //  bisa di-enhance nanti dg historical query)
                            $trend = 'stable';

                            $allBatteries[] = [
                                'location'  => $locationName,
                                'bank'      => $bank->bank_name,
                                'batteryNo' => $cell->cell_number,
                                'voltage'   => $voltage,
                                'soh'       => $soh,
                                'status'    => $status,
                                'trend'     => $trend,
                            ];
                        }
                    }
                }
            }

            // 4. Sort by SOH ascending (terburuk di atas), ambil top 5
            usort($allBatteries, function ($a, $b) {
                // Prioritas: kritis > warning > sehat
                $statusOrder = ['kritis' => 0, 'warning' => 1, 'sehat' => 2];
                $statusDiff  = ($statusOrder[$a['status']] ?? 2) - ($statusOrder[$b['status']] ?? 2);
                if ($statusDiff !== 0) return $statusDiff;

                // Kalau status sama, sort by SOH asc
                return $a['soh'] <=> $b['soh'];
            });

            $criticalBatteries = array_slice($allBatteries, 0, 5);

            return response()->json([
                'summary'            => $summary,
                'criticalBatteries'  => $criticalBatteries,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch battery dashboard data', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch battery data',
            ], 500);
        }
    }

    /**
     * Helper: tentukan status cell berdasarkan voltage & SOH
     */
    private function getCellStatus(float $voltage, int $soh): string
    {
        if ($voltage < 12.0 && $soh < 80) {
            return 'kritis';   // dua-duanya di bawah threshold
        }
        if ($voltage < 12.0 || $soh < 80) {
            return 'warning';  // salah satunya di bawah
        }
        return 'sehat';
    }

    // ========================================================
    // ENDPOINT BARU: Untuk BatteryDashboard filters & chart
    // ========================================================

    /**
     * GET /api/battery/locations
     * Return: list lokasi yang beneran punya battery data di database
     */
    public function getLocationsWithBattery()
    {
        try {
            $locations = Location::whereHas(
                'inspections.inspectionForms.batteryBanks.measurements'
            )
            ->select('id', 'location_name')
            ->distinct()
            ->orderBy('location_name', 'asc')
            ->get();

            return response()->json([
                'success'   => true,
                'locations' => $locations->map(fn ($loc) => [
                    'id'   => $loc->id,
                    'name' => $loc->location_name,
                ]),
            ]);

        } catch (\Exception $e) {
            Log::error('getLocationsWithBattery error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error'], 500);
        }
    }

    /**
     * GET /api/battery/locations/{locationId}/banks-and-cells
     * Return: list bank + list cell numbers per bank
     *         dari LATEST inspection di lokasi tersebut
     */
    public function getBanksAndCells(int $locationId)
    {
        try {
            // Ambil SEMUA inspection_form_id di lokasi ini
            // yang punya battery data (bukan hanya latest)
            $inspectionFormIds = InspectionForm::whereIn(
                'inspection_id',
                Inspection::where('location_id', $locationId)
                    ->whereHas('inspectionForms.batteryBanks.measurements')
                    ->pluck('id')
            )->pluck('id');

            if ($inspectionFormIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data battery di lokasi ini',
                ], 404);
            }

            // Ambil semua bank dari semua inspections,
            // group by bank_name dan gabungkan semua cell_numbers
            $banks = BatteryBankMetadata::whereIn('inspection_form_id', $inspectionFormIds)
                ->with('measurements:cell_number,battery_bank_id')
                ->get();

            // Group by bank_name, gabungkan cells dari semua inspections
            $grouped = [];
            foreach ($banks as $bank) {
                $name = $bank->bank_name;

                if (!isset($grouped[$name])) {
                    $grouped[$name] = [];
                }

                // Tambahkan semua cell_numbers dari bank ini
                foreach ($bank->measurements as $m) {
                    $grouped[$name][] = $m->cell_number;
                }
            }

            // Deduplicate dan sort cells per bank
            $result = [];
            foreach ($grouped as $bankName => $cells) {
                $result[] = [
                    'bank_name' => $bankName,
                    'cells'     => array_values(array_unique($cells)),
                ];

                // Sort cells ascending
                sort($result[array_key_last($result)]['cells']);
            }

            // Sort banks by name
            usort($result, fn($a, $b) => strcmp($a['bank_name'], $b['bank_name']));

            return response()->json([
                'success' => true,
                'banks'   => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('getBanksAndCells error', [
                'location_id' => $locationId,
                'error'       => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error'], 500);
        }
    }

    /**
     * GET /api/battery/trend?location_id=1&bank=UPS&battery_no=5
     * Return: trend overtime -> [{ date, voltage, soh }, ...]
     *         dari SEMUA inspections di lokasi itu (bukan cuma latest)
     *         sorted by inspection_date asc (kronologis)
     */
    public function getBatteryTrend(Request $request)
    {
        try {
            $locationId = $request->integer('location_id');
            $bankName   = $request->string('bank');
            $batteryNo  = $request->integer('battery_no');

            // Validasi
            if (!$locationId || !$bankName || !$batteryNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'location_id, bank, dan battery_no wajib diisi',
                ], 422);
            }

            // Ambil semua inspections di lokasi ini, sorted kronologis
            $inspections = Inspection::where('location_id', $locationId)
                ->whereHas('inspectionForms.batteryBanks.measurements')
                ->orderBy('inspection_date', 'asc')
                ->get();

            $trendData = [];

            foreach ($inspections as $inspection) {
                $found = false;

                foreach ($inspection->inspectionForms as $form) {
                    $banks = BatteryBankMetadata::where('inspection_form_id', $form->id)
                        ->where('bank_name', $bankName)
                        ->with('measurements')
                        ->get();

                    foreach ($banks as $bank) {
                        $cell = $bank->measurements->firstWhere('cell_number', $batteryNo);

                        if ($cell) {
                            $trendData[] = [
                                'date'    => $inspection->inspection_date->format('d M Y'),
                                'voltage' => (float) $cell->voltage,
                                'soh'     => (int) $cell->soh,
                            ];
                            $found = true;
                            break;
                        }
                    }

                    if ($found) break;
                }
            }

            if (empty($trendData)) {
                return response()->json([
                    'success' => false,
                    'message' => "Battery #{$batteryNo} di bank {$bankName} tidak ditemukan",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'trend'   => $trendData,
            ]);

        } catch (\Exception $e) {
            Log::error('getBatteryTrend error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error'], 500);
        }
    }
}