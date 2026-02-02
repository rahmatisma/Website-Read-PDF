<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 *  VALIDATION RESPONSE HANDLER
 * 
 * Helper untuk validasi apakah document_type cocok dengan kategori yang dipilih user
 */
class DocumentValidationHelper
{
    /**
     * Mapping kategori Laravel ke prefix document type Python
     */
    const CATEGORY_MAPPING = [
        'spk' => ['spk_survey', 'spk_instalasi', 'spk_dismantle', 'spk_aktivasi'],
        'checklist' => ['checklist_wireline', 'checklist_wireless'],
        'pmpop' => [
            'form_pm_1phase_ups',
            'form_pm_3phase_ups',
            'form_pm_ac',
            'form_pm_inverter',
            'form_pm_ruang_shelter',
            'form_pm_rectifier',
            'form_pm_petir_grounding',
            'form_pm_instalasi_kabel',
            'form_pm_battery',
            'form_pm_pole_tower',
            'form_pm_dokumentasi_perangkat',
            'form_pm_genset',
            'form_pm_permohonan_tindak_lanjut',
            'form_pm_tindak_lanjut',
            'form_pm_jadwal_sentral',
        ],
    ];

    /**
     * Validasi apakah document_type cocok dengan kategori
     * 
     * @param string $documentType (contoh: 'form_pm_inverter')
     * @param string $expectedCategory (contoh: 'pmpop')
     * @return array
     */
    public static function validateCategory(string $documentType, string $expectedCategory): array
    {
        $allowedTypes = self::CATEGORY_MAPPING[$expectedCategory] ?? [];
        
        $isValid = in_array($documentType, $allowedTypes);
        
        if (!$isValid) {
            // Cari kategori yang sebenarnya
            $actualCategory = null;
            foreach (self::CATEGORY_MAPPING as $category => $types) {
                if (in_array($documentType, $types)) {
                    $actualCategory = $category;
                    break;
                }
            }
            
            return [
                'is_valid_for_category' => false,
                'expected_category' => $expectedCategory,
                'actual_category' => $actualCategory ?? 'unknown',
                'message' => "Dokumen ini adalah {$documentType}, seharusnya diupload di kategori '{$actualCategory}' bukan '{$expectedCategory}'"
            ];
        }
        
        return [
            'is_valid_for_category' => true,
            'expected_category' => $expectedCategory,
            'actual_category' => $expectedCategory,
            'message' => 'Dokumen sesuai dengan kategori'
        ];
    }

    /**
     * Get friendly name dari document type
     */
    public static function getFriendlyName(string $documentType): string
    {
        $mapping = [
            // SPK
            'spk_survey' => 'SPK Survey',
            'spk_instalasi' => 'SPK Instalasi',
            'spk_dismantle' => 'SPK Dismantle',
            'spk_aktivasi' => 'SPK Aktivasi',
            
            // Checklist
            'checklist_wireline' => 'Form Checklist Wireline',
            'checklist_wireless' => 'Form Checklist Wireless',
            
            // Form PM POP
            'form_pm_1phase_ups' => 'Form PM: 1 Phase UPS',
            'form_pm_3phase_ups' => 'Form PM: 3 Phase UPS',
            'form_pm_ac' => 'Form PM: AC',
            'form_pm_inverter' => 'Form PM: Inverter -48VDC/220VAC',
            'form_pm_ruang_shelter' => 'Form PM: Ruang Shelter',
            'form_pm_rectifier' => 'Form PM: Rectifier',
            'form_pm_petir_grounding' => 'Form PM: Petir & Grounding',
            'form_pm_instalasi_kabel' => 'Form PM: Instalasi Kabel & Panel Distribusi',
            'form_pm_battery' => 'Form PM: Battery',
            'form_pm_pole_tower' => 'Form PM: Pole/Tower',
            'form_pm_dokumentasi_perangkat' => 'Form PM: Dokumentasi & Pendataan Perangkat',
            'form_pm_genset' => 'Form PM: Genset',
            'form_pm_permohonan_tindak_lanjut' => 'Form PM: Permohonan Tindak Lanjut',
            'form_pm_tindak_lanjut' => 'Form PM: Tindak Lanjut',
            'form_pm_jadwal_sentral' => 'Form PM: Jadwal Sentral',
        ];
        
        return $mapping[$documentType] ?? $documentType;
    }
}