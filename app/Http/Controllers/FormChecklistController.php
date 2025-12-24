<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\FormChecklistWirelineService;
use App\Services\FormChecklistWirelessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormChecklistController extends Controller
{
    protected $fcwService;
    protected $fcwlService;

    public function __construct(
        FormChecklistWirelineService $fcwService,
        FormChecklistWirelessService $fcwlService
    ) {
        $this->fcwService = $fcwService;
        $this->fcwlService = $fcwlService;
    }

    /**
     * Process uploaded form checklist JSON
     * 
     * @param int $uploadId
     * @return \Illuminate\Http\JsonResponse
     */
    public function processUpload($uploadId)
    {
        try {
            $upload = Document::findOrFail($uploadId);
            $jsonData = json_decode($upload->json_data, true);

            if (!$jsonData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ], 400);
            }

            // Determine document type
            $documentType = $jsonData['parsed']['document_type'] ??
                $jsonData['data']['parsed']['document_type'] ??
                'unknown';

            $idSpk = $upload->id_spk;

            if (!$idSpk) {
                return response()->json([
                    'success' => false,
                    'message' => 'SPK must be created first'
                ], 400);
            }

            $result = null;

            // Process based on document type
            switch ($documentType) {
                case 'form_checklist_wireline':
                    $result = $this->fcwService->process($jsonData, $idSpk, $uploadId);
                    break;

                case 'form_checklist_wireless':
                    $result = $this->fcwlService->process($jsonData, $idSpk, $uploadId);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => "Unknown document type: {$documentType}"
                    ], 400);
            }

            // Update upload status
            $upload->update([
                'status' => 'processed',
                'processed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Form checklist processed successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process form checklist', [
                'upload_id' => $uploadId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form checklist data (wireline)
     */
    public function getWireline($idFcw)
    {
        try {
            $fcw = \App\Models\FormChecklistWireline::with([
                'waktuPelaksanaan',
                'tegangan',
                'checklistItems',
                'dataPerangkat',
                'guidanceFoto',
                'logs'
            ])->findOrFail($idFcw);

            return response()->json([
                'success' => true,
                'data' => $fcw
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get form checklist data (wireless)
     */
    public function getWireless($idFcwl)
    {
        try {
            $fcwl = \App\Models\FormChecklistWireless::with([
                'waktuPelaksanaan',
                'tegangan',
                'indoorArea.parameters',
                'outdoorArea.parameters',
                'perangkatAntenna',
                'cablingInstallation',
                'dataPerangkat',
                'guidanceFoto',
                'logs'
            ])->findOrFail($idFcwl);

            return response()->json([
                'success' => true,
                'data' => $fcwl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
