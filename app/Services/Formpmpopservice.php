<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\InspectionForm;
use App\Models\InspectionResult;
use App\Models\Location;
use App\Models\Pelaksana;
use App\Models\FormsMaster;
use App\Models\FormsChecklistMaster;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\BatteryBankMetadata;
use App\Models\BatteryMeasurement;
use App\Models\EquipmentInventory;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class FormPmPopService
{
    /**
     * Main entry point untuk process Form PM POP
     */
    public function process(array $parsedData, int $uploadId)
    {
        DB::beginTransaction();
        
        try {
            // Extract document type dari header
            $documentType = $parsedData['header']['no_dok'] ?? null;
            
            // Route berdasarkan document type
            $result = match(true) {
                str_contains($documentType, '003-010') => $this->processBattery($parsedData, $uploadId),
                str_contains($documentType, '003-012') => $this->processInventory($parsedData, $uploadId),
                str_contains($documentType, '003-004') && isset($parsedData['serial_number']['ac_1']) => $this->processAC($parsedData, $uploadId),
                default => $this->processGenericInspection($parsedData, $uploadId)
            };
            
            DB::commit();
            
            Log::info(' Form PM POP successfully processed', [
                'upload_id' => $uploadId,
                'document_type' => $documentType,
                'result' => $result
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process Form PM POP', [
                'upload_id' => $uploadId,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process Battery Form (FM-LAP-D2-SOP-003-010)
     * Struktur khusus: battery_banks[] dengan voltage_soh_table[]
     */
    private function processBattery(array $data, int $uploadId)
    {
        Log::info('🔋 Processing Battery Form', ['upload_id' => $uploadId]);
        
        // 1. Create/Get Location
        $locationId = $this->getOrCreateLocation($data['informasi_umum']['location'] ?? '');
        
        // 2. Parse date
        $inspectionDate = $this->parseDate($data['informasi_umum']['date_time'] ?? null);
        
        // 3. Create Inspection
        $inspection = Inspection::create([
            'inspection_code' => $this->generateInspectionCode(),
            'location_id' => $locationId,
            'inspection_date' => $inspectionDate ?? now(),
            'inspection_time' => null,
            'status' => 'COMPLETED',
            'notes' => $data['notes'] ?? null,
        ]);
        
        // 4. Handle Pelaksana
        $this->attachPelaksana($inspection, $data['pelaksana'] ?? []);
        
        // 5. Get/Create Form Master
        $formMaster = $this->getOrCreateFormMaster(
            $data['header']['no_dok'] ?? 'FM-LAP-D2-SOP-003-010',
            $data['header']['judul'] ?? 'Preventive Maintenance Battery'
        );
        
        // 6. Create Inspection Form
        $inspectionForm = InspectionForm::create([
            'inspection_id' => $inspection->id,
            'form_id' => $formMaster->id,
            'equipment_id' => null,
            'instance_number' => 1,
            'status' => 'COMPLETED',
            'notes' => null,
        ]);
        
        // 7. Process Battery Banks + Measurements
        if (isset($data['battery_banks']) && is_array($data['battery_banks'])) {
            foreach ($data['battery_banks'] as $bankData) {
                $this->processBatteryBank($inspectionForm->id, $bankData, $data);
            }
        }
        
        // 8. Process Measurement Tests (backup test results)
        if (isset($data['measurement_test']) && is_array($data['measurement_test'])) {
            $this->processMeasurementTests($inspectionForm->id, $formMaster->id, $data['measurement_test']);
        }
        
        return [
            'success' => true,
            'inspection_id' => $inspection->id,
            'inspection_form_id' => $inspectionForm->id,
            'battery_banks_count' => count($data['battery_banks'] ?? []),
        ];
    }
    
    /**
     * Process single battery bank + voltage/SOH measurements
     */
    private function processBatteryBank(int $inspectionFormId, array $bankData, array $fullData)
    {
        // Parse production date dari notes jika ada
        $productionDate = $this->extractProductionDate(
            $fullData['notes'] ?? '', 
            $bankData['bank_number']
        );
        
        // Generate unique bank_name yang include type
        $bankName = "Bank {$bankData['bank_number']} {$bankData['bank_type']}";

        // Use updateOrCreate untuk handle duplicate
        $batteryBank = BatteryBankMetadata::updateOrCreate(
            [
                'inspection_form_id' => $inspectionFormId,
                'bank_name' => $bankName, // Unique identifier
            ],
            [
                'bank_number' => (int)$bankData['bank_number'],
                'battery_type' => $bankData['battery_type'] ?? null,
                'battery_brand' => $bankData['battery_brand'] ?? null,
                'battery_capacity' => $bankData['end_device_batt'] ?? null,
                'production_date' => $productionDate,
                'notes' => $fullData['notes'] ?? null,
            ]
        );
        
        // Create Battery Measurements (voltage + SOH table)
        if (isset($bankData['voltage_soh_table']) && is_array($bankData['voltage_soh_table'])) {
            foreach ($bankData['voltage_soh_table'] as $measurement) {
                // Skip empty rows
                if (empty($measurement['voltage']) && empty($measurement['soh'])) {
                    continue;
                }

                $cellNumber = (int)($measurement['no'] ?? 0);

                // Skip kalau cell_number tidak valid
                if ($cellNumber <= 0) {
                    Log::warning('Skipping measurement: cell_number tidak valid', [
                        'raw_no'  => $measurement['no'] ?? 'null',
                        'bank_id' => $batteryBank->id,
                    ]);
                    continue;
                }

                BatteryMeasurement::updateOrCreate(
                    [
                        'battery_bank_id' => $batteryBank->id,
                        'cell_number'     => $cellNumber,
                    ],
                    [
                        'voltage' => $this->parseDecimal($measurement['voltage'] ?? null),
                        'soh'     => !empty($measurement['soh']) ? (int)$measurement['soh'] : null,
                    ]
                );
            }
        }
        
        Log::info(' Battery bank processed', [
            'bank_id' => $batteryBank->id,
            'bank_number' => $bankData['bank_number'],
            'measurements_count' => count($bankData['voltage_soh_table'] ?? [])
        ]);
    }
    
    /**
     * Process measurement tests (backup test, dll)
     */
    private function processMeasurementTests(int $inspectionFormId, int $formMasterId, array $tests)
    {
        foreach ($tests as $test) {
            // Get or create checklist item
            $checklistItem = FormsChecklistMaster::firstOrCreate([
                'form_id' => $formMasterId,
                'item_code' => $test['no'] ?? 'test',
                'item_description' => $test['description'] ?? 'Measurement Test',
            ], [
                'section_number' => 3,
                'section_name' => 'Backup Tests',
                'operational_standard' => $test['operational_standard'] ?? '',
                'item_order' => 100,
            ]);
            
            InspectionResult::create([
                'inspection_form_id' => $inspectionFormId,
                'checklist_item_id' => $checklistItem->id,
                'result_value' => $test['result'] ?? null,
                'status' => $test['status'] ?? null,
                'comment' => null,
                'measurement_label' => null,
            ]);
        }
    }
    
    /**
     * Process Inventory Form (FM-LAP-D2-SOP-003-012)
     * Struktur: inventory.device_sentral[] + inventory.supporting_facilities[]
     */
    private function processInventory(array $data, int $uploadId)
    {
        Log::info('📦 Processing Inventory Form', ['upload_id' => $uploadId]);
        
        // 1. Create/Get Location
        $locationId = $this->getOrCreateLocation($data['informasi_umum']['location'] ?? '');
        
        // 2. Parse date
        $inventoryDate = $this->parseDate($data['informasi_umum']['date_time'] ?? null);
        
        // 3. Create Equipment Inventory (header)
        $inventory = EquipmentInventory::create([
            'form_code' => $data['header']['no_dok'] ?? 'FM-LAP-D2-SOP-003-012',
            'inspection_id' => null,
            'location_id' => $locationId,
            'inventory_date' => $inventoryDate ?? now(),
            'inventory_time' => null,
            'notes' => $data['notes'] ?? null,
        ]);
        
        // 4. Handle Pelaksana
        $this->attachPelaksanaToInventory($inventory, $data['pelaksana'] ?? []);
        
        // 5. Process Device Sentral Items
        $rowNumber = 1;
        if (isset($data['inventory']['device_sentral']) && is_array($data['inventory']['device_sentral'])) {
            foreach ($data['inventory']['device_sentral'] as $item) {
                $this->createInventoryItem($inventory->id, $item, $rowNumber, 'I. DEVICE SENTRAL');
                $rowNumber++;
            }
        }
        
        // 6. Process Supporting Facilities Items
        if (isset($data['inventory']['supporting_facilities']) && is_array($data['inventory']['supporting_facilities'])) {
            foreach ($data['inventory']['supporting_facilities'] as $item) {
                $this->createInventoryItem($inventory->id, $item, $rowNumber, 'II. SUPPORTING FACILITIES');
                $rowNumber++;
            }
        }
        
        return [
            'success' => true,
            'inventory_id' => $inventory->id,
            'items_count' => $rowNumber - 1,
        ];
    }
    
    /**
     * Create single inventory item
     */
    private function createInventoryItem(int $inventoryId, array $itemData, int $rowNumber, string $sectionName)
    {
        InventoryItem::create([
            'inventory_id' => $inventoryId,
            'row_number' => $rowNumber,
            'section_name' => $sectionName,
            'equipment_name' => $itemData['equipment'] ?? '',
            'equipment_id' => null, // Link to equipment master bisa dilakukan nanti
            'quantity' => (int)($itemData['qty'] ?? 0),
            'status' => strtoupper($itemData['status'] ?? 'ACTIVE'),
            'bonding_ground' => $this->normalizeBondingGround($itemData['bonding_ground'] ?? null),
            'remarks' => $itemData['keterangan'] ?? null,
        ]);
    }
    
    /**
     * Process AC Form (FM-LAP-D2-SOP-003-004)
     * Special case: serial_number adalah object {ac_1, ac_2}
     */
    private function processAC(array $data, int $uploadId)
    {
        Log::info('❄️ Processing AC Form', ['upload_id' => $uploadId]);
        
        // 1. Process as generic inspection first
        $result = $this->processGenericInspection($data, $uploadId);
        
        // 2. Create Equipment records for AC units
        $locationId = $this->getOrCreateLocation($data['informasi_umum']['location'] ?? '');
        $equipmentTypeId = $this->getOrCreateEquipmentType('AC', 'Air Conditioning');
        
        // Handle multiple serial numbers
        $serialNumbers = $data['informasi_umum']['serial_number'] ?? [];
        
        if (is_array($serialNumbers)) {
            foreach ($serialNumbers as $key => $serialNumber) {
                if (empty($serialNumber)) continue;
                
                Equipment::create([
                    'equipment_type_id' => $equipmentTypeId,
                    'location_id' => $locationId,
                    'brand' => $data['informasi_umum']['brand_type'] ?? null,
                    'model_type' => null,
                    'capacity' => $data['informasi_umum']['capacity'] ?? null,
                    'reg_number' => $data['informasi_umum']['reg_number'] ?? null,
                    'serial_number' => $serialNumber,
                    'metadata' => json_encode([
                        'ac_unit' => $key, // ac_1, ac_2
                        'form_code' => $data['header']['no_dok'] ?? null,
                    ]),
                    'status' => 'ACTIVE',
                ]);
            }
        }
        
        return $result;
    }
    
    /**
     * Process Generic PM Inspection (untuk form-form standar)
     * Digunakan untuk: inverter, rectifier, shelter, petir_grounding, instalasi_kabel, pole_tower
     */
    private function processGenericInspection(array $data, int $uploadId)
    {
        Log::info('🔧 Processing Generic PM Inspection', [
            'upload_id' => $uploadId,
            'form_code' => $data['header']['no_dok'] ?? 'unknown'
        ]);
        
        // 1. Create/Get Location
        $locationId = $this->getOrCreateLocation($data['informasi_umum']['location'] ?? '');
        
        // 2. Parse date
        $inspectionDate = $this->parseDate($data['informasi_umum']['date_time'] ?? null);
        
        // 3. Create Inspection
        $inspection = Inspection::create([
            'inspection_code' => $this->generateInspectionCode(),
            'location_id' => $locationId,
            'inspection_date' => $inspectionDate ?? now(),
            'inspection_time' => null,
            'status' => 'COMPLETED',
            'notes' => $data['notes'] ?? null,
        ]);
        
        // 4. Handle Pelaksana
        $this->attachPelaksana($inspection, $data['pelaksana'] ?? []);
        
        // 5. Get/Create Form Master
        $formMaster = $this->getOrCreateFormMaster(
            $data['header']['no_dok'] ?? '',
            $data['header']['judul'] ?? ''
        );
        
        // 6. Create Inspection Form
        $inspectionForm = InspectionForm::create([
            'inspection_id' => $inspection->id,
            'form_id' => $formMaster->id,
            'equipment_id' => $this->getOrCreateEquipmentForInspection($data),
            'instance_number' => 1,
            'status' => 'COMPLETED',
            'notes' => null,
        ]);
        
        // 7. Process Checklist Items
        $this->processChecklistItems($inspectionForm->id, $formMaster->id, $data);
        
        return [
            'success' => true,
            'inspection_id' => $inspection->id,
            'inspection_form_id' => $inspectionForm->id,
        ];
    }
    
    /**
     * Process all checklist items from various sections
     */
    private function processChecklistItems(int $inspectionFormId, int $formMasterId, array $data)
    {
        // Detect semua section yang berisi checklist items
        $sections = [
            'physical_check',
            'performance_check',
            'performance_capacity_check',
            'visual_check',
            'performance_measurement',
            'backup_tests',
            'maksure_cable_connection',
            'room_infrastructure',
            'room_temperature',
            'psi_pressure',
            'input_current',
            'output_temperature',
        ];
        
        foreach ($sections as $sectionKey) {
            if (!isset($data[$sectionKey])) continue;
            
            $sectionData = $data[$sectionKey];
            
            // Handle nested objects (seperti performance_measurement.mcb_temperature)
            if (is_array($sectionData) && !isset($sectionData[0])) {
                // Check if this is nested object
                $isNested = false;
                foreach ($sectionData as $value) {
                    if (is_array($value)) {
                        $isNested = true;
                        break;
                    }
                }
                
                if ($isNested) {
                    // Flatten nested arrays
                    foreach ($sectionData as $subSectionKey => $subSection) {
                        if (is_array($subSection)) {
                            $this->processChecklistSection($inspectionFormId, $formMasterId, $subSection, $sectionKey);
                        }
                    }
                    continue;
                }
            }
            
            // Process regular section
            $this->processChecklistSection($inspectionFormId, $formMasterId, $sectionData, $sectionKey);
        }
    }
    
    /**
     * Process single checklist section
     */
    private function processChecklistSection(int $inspectionFormId, int $formMasterId, $sectionData, string $sectionKey)
    {
        // Handle object with checklist key (room_temperature)
        if (is_array($sectionData) && isset($sectionData['checklist'])) {
            $sectionData = $sectionData['checklist'];
        }
        
        // Handle single object (not array of items)
        if (is_array($sectionData) && !isset($sectionData[0])) {
            $sectionData = [$sectionData];
        }
        
        if (!is_array($sectionData)) return;
        
        foreach ($sectionData as $item) {
            if (!is_array($item)) continue;
            
            // Get or create checklist master item
            $checklistItem = FormsChecklistMaster::firstOrCreate([
                'form_id' => $formMasterId,
                'item_code' => $item['no'] ?? $sectionKey,
                'item_description' => $item['description'] ?? $sectionKey,
            ], [
                'section_number' => $this->getSectionNumber($sectionKey),
                'section_name' => ucfirst(str_replace('_', ' ', $sectionKey)),
                'operational_standard' => $item['standard'] ?? '',
                'item_order' => 1,
            ]);
            
            // Create inspection result
            InspectionResult::create([
                'inspection_form_id' => $inspectionFormId,
                'checklist_item_id' => $checklistItem->id,
                'result_value' => $item['result'] ?? null,
                'status' => $item['status'] ?? null,
                'comment' => $item['comment'] ?? null,
                'measurement_label' => $item['measurement_label'] ?? $item['room_type'] ?? $item['threshold'] ?? null,
            ]);
        }
    }
    
    /**
     * Helper: Get section number from section key
     */
    private function getSectionNumber(string $sectionKey): int
    {
        $mapping = [
            'physical_check' => 1,
            'visual_check' => 1,
            'performance_check' => 2,
            'performance_capacity_check' => 2,
            'performance_measurement' => 2,
            'backup_tests' => 3,
            'room_infrastructure' => 2,
            'room_temperature' => 3,
        ];
        
        return $mapping[$sectionKey] ?? 1;
    }
    
    /**
     * Helper: Attach pelaksana to inspection
     */
    private function attachPelaksana(Inspection $inspection, array $pelaksanaData)
    {
        $executors = $pelaksanaData['executor'] ?? [];
        
        if (is_array($executors)) {
            foreach ($executors as $index => $executor) {
                if (empty($executor['Nama'])) continue;
                
                $pelaksana = $this->getOrCreatePelaksana(
                    $executor['Nama'],
                    $executor['Mitra / internal'] ?? null
                );
                
                $field = 'pelaksana' . ($index + 1) . '_id';
                if ($index < 4) {
                    $inspection->$field = $pelaksana->id;
                }
            }
        }
        
        // Verifikator
        if (!empty($pelaksanaData['verifikator'])) {
            $verifikator = $this->getOrCreatePelaksana($pelaksanaData['verifikator']);
            $inspection->verified_by_id = $verifikator->id;
        }
        
        // Head of Sub Department
        if (!empty($pelaksanaData['head_of_sub_department'])) {
            $head = $this->getOrCreatePelaksana($pelaksanaData['head_of_sub_department']);
            $inspection->approved_by_id = $head->id;
        }
        
        $inspection->save();
    }
    
    /**
     * Helper: Attach pelaksana to equipment inventory
     */
    private function attachPelaksanaToInventory(EquipmentInventory $inventory, array $pelaksanaData)
    {
        $executors = $pelaksanaData['executor'] ?? [];
        
        if (is_array($executors)) {
            foreach ($executors as $index => $executor) {
                if (empty($executor['Nama'])) continue;
                
                $pelaksana = $this->getOrCreatePelaksana(
                    $executor['Nama'],
                    $executor['Mitra / internal'] ?? null
                );
                
                $field = 'pelaksana' . ($index + 1) . '_id';
                if ($index < 4) {
                    $inventory->$field = $pelaksana->id;
                }
            }
        }
        
        // Verifikator
        if (!empty($pelaksanaData['verifikator'])) {
            $verifikator = $this->getOrCreatePelaksana($pelaksanaData['verifikator']);
            $inventory->verified_by_id = $verifikator->id;
        }
        
        // Head of Sub Department
        if (!empty($pelaksanaData['head_of_sub_department'])) {
            $head = $this->getOrCreatePelaksana($pelaksanaData['head_of_sub_department']);
            $inventory->approved_by_id = $head->id;
        }
        
        $inventory->save();
    }
    
    /**
     * Helper: Get or create location
     */
    private function getOrCreateLocation(string $locationName): int
    {
        if (empty($locationName)) {
            $locationName = 'Unknown Location';
        }
        
        $location = Location::firstOrCreate(
            ['location_name' => $locationName],
            ['address' => null]
        );
        
        return $location->id;
    }
    
    /**
     * Helper: Get or create pelaksana
     */
    private function getOrCreatePelaksana(string $nama, ?string $department = null)
    {
        return Pelaksana::firstOrCreate(
            ['nama' => $nama],
            [
                'department' => $department,
                'sub_department' => null,
            ]
        );
    }
    
    /**
     * Helper: Get or create form master
     */
    private function getOrCreateFormMaster(string $formCode, string $formTitle)
    {
        return FormsMaster::firstOrCreate(
            ['form_code' => $formCode],
            [
                'form_title' => $formTitle,
                'form_type' => 'INSPECTION',
                'equipment_type_id' => null,
                'version' => '1.0',
                'page_total' => 1,
                'is_active' => true,
            ]
        );
    }
    
    /**
     * Helper: Get or create equipment for inspection
     */
    private function getOrCreateEquipmentForInspection(array $data): ?int
    {
        // Check if form has equipment-specific data
        $infoUmum = $data['informasi_umum'] ?? [];
        
        if (empty($infoUmum['brand_type']) && empty($infoUmum['serial_number'])) {
            return null; // No equipment data
        }
        
        // Determine equipment type from form code
        $formCode = $data['header']['no_dok'] ?? '';
        $equipmentTypeName = $this->inferEquipmentType($formCode);
        
        if (!$equipmentTypeName) {
            return null;
        }
        
        $equipmentTypeId = $this->getOrCreateEquipmentType($equipmentTypeName);
        $locationId = $this->getOrCreateLocation($infoUmum['location'] ?? '');
        
        // Check if equipment already exists
        $serialNumber = is_array($infoUmum['serial_number'] ?? null) 
            ? ($infoUmum['serial_number']['ac_1'] ?? null)
            : ($infoUmum['serial_number'] ?? null);
        
        if ($serialNumber) {
            $existing = Equipment::where('serial_number', $serialNumber)->first();
            if ($existing) {
                return $existing->id;
            }
        }
        
        // Create new equipment
        $equipment = Equipment::create([
            'equipment_type_id' => $equipmentTypeId,
            'location_id' => $locationId,
            'brand' => $infoUmum['brand_type'] ?? null,
            'model_type' => $infoUmum['type'] ?? null,
            'capacity' => $infoUmum['capacity'] ?? null,
            'reg_number' => $infoUmum['reg_number'] ?? null,
            'serial_number' => $serialNumber,
            'kap_power_module' => $infoUmum['kap_power_module'] ?? null,
            'type_pole' => $infoUmum['type_pole'] ?? null,
            'status' => 'ACTIVE',
        ]);
        
        return $equipment->id;
    }
    
    /**
     * Helper: Get or create equipment type
     */
    private function getOrCreateEquipmentType(string $typeName, ?string $description = null): int
    {
        $typeCode = strtoupper(str_replace(' ', '_', $typeName));
        
        $equipmentType = EquipmentType::firstOrCreate(
            ['type_code' => $typeCode],
            ['type_name' => $description ?? $typeName]
        );
        
        return $equipmentType->id;
    }
    
    /**
     * Helper: Infer equipment type from form code
     */
    private function inferEquipmentType(string $formCode): ?string
    {
        if (str_contains($formCode, '003-005')) return 'Inverter';
        if (str_contains($formCode, '003-006')) return 'Shelter Room';
        if (str_contains($formCode, '003-007')) return 'Rectifier';
        if (str_contains($formCode, '003-008')) return 'Lightning Protection';
        if (str_contains($formCode, '003-009')) return 'Distribution Panel';
        if (str_contains($formCode, '003-010')) return 'Battery';
        if (str_contains($formCode, '003-011')) return 'Pole Tower';
        if (str_contains($formCode, '003-004')) return 'AC';
        
        return null;
    }
    
    /**
     * Helper: Generate unique inspection code
     */
    private function generateInspectionCode(): string
    {
        $date = now()->format('Ymd');
        $lastInspection = Inspection::whereDate('created_at', now())->count();
        $sequence = str_pad($lastInspection + 1, 3, '0', STR_PAD_LEFT);
        
        return "INS-{$date}-{$sequence}";
    }
    
    /**
     * Helper: Parse date string
     */
    private function parseDate(?string $dateString): ?Carbon
    {
        if (empty($dateString)) return null;
        
        try {
            // ✅ FIX: Normalize casing dulu sebelum replace
            // "20 maret 2025" → "20 Maret 2025"
            $dateString = ucwords(strtolower(trim($dateString)));

            // Bulan Indonesia mapping
            $indonesianMonths = [
                'Januari' => 'January',
                'Februari' => 'February',
                'Maret' => 'March',
                'April' => 'April',
                'Mei' => 'May',
                'Juni' => 'June',
                'Juli' => 'July',
                'Agustus' => 'August',
                'Agutus' => 'August',
                'September' => 'September',
                'Oktober' => 'October',
                'November' => 'November',
                'Desember' => 'December',
            ];
            
            // Replace Indonesian month names
            $englishDate = str_replace(
                array_keys($indonesianMonths),
                array_values($indonesianMonths),
                $dateString
            );
            
            // Remove day names (Sabtu, Minggu, etc)
            $englishDate = preg_replace('/^(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu),?\s*/i', '', $englishDate);
            
            // Try parsing
            $formats = [
                'd F Y',     // "20 March 2025"
                'd/m/Y',
                'd-m-Y',
                'Y-m-d',
            ];
            
            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, trim($englishDate));
                    if ($date) {
                        return $date;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Fallback
            return Carbon::parse($englishDate);
            
        } catch (\Exception $e) {
            Log::warning('Failed to parse date', [
                'input' => $dateString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Helper: Parse decimal (convert comma to dot)
     */
    private function parseDecimal(?string $value): ?float
    {
        if (empty($value)) return null;
        
        // Replace comma with dot
        $cleaned = str_replace(',', '.', $value);
        
        return is_numeric($cleaned) ? (float)$cleaned : null;
    }
    
    /**
     * Helper: Extract production date from notes
     */
    private function extractProductionDate(string $notes, $bankNumber): ?Carbon
    {
        // Pattern: "Tanggal Produksi battery 1 = 10/09/2021"
        $pattern = "/Tanggal Produksi battery {$bankNumber} = (\d+\/\d+\/\d+)/i";
        
        if (preg_match($pattern, $notes, $matches)) {
            try {
                return Carbon::createFromFormat('d/m/Y', $matches[1]);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }
    
    /**
     * Helper: Normalize bonding ground value
     */
    private function normalizeBondingGround(?string $value): ?string
    {
        if (empty($value)) return null;
        
        $lower = strtolower(trim($value));
        
        if (str_contains($lower, 'connect') || str_contains($lower, 'konek')) {
            return 'CONNECT';
        }
        
        if (str_contains($lower, 'not connect') || str_contains($lower, 'tidak')) {
            return 'NOT CONNECT';
        }
        
        return strtoupper($value);
    }
}