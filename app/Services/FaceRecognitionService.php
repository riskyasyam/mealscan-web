<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceRecognitionService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.face_recognition.api_url', 'http://127.0.0.1:8001');
    }

    /**
     * Register a face for an employee
     */
    public function registerFace(string $employeeId, $imageFile): array
    {
        try {
            $response = Http::attach(
                'file',
                file_get_contents($imageFile),
                'photo.jpg'
            )->post("{$this->apiUrl}/api/face/register", [
                'employee_id' => $employeeId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Failed to register face',
            ];

        } catch (\Exception $e) {
            Log::error('Face registration error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to connect to face recognition service',
            ];
        }
    }

    /**
     * Recognize a face
     */
    public function recognizeFace($imageFile): array
    {
        try {
            $response = Http::attach(
                'file',
                file_get_contents($imageFile),
                'checkin.jpg'
            )->post("{$this->apiUrl}/recognize");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to recognize face',
            ];

        } catch (\Exception $e) {
            Log::error('Face recognition error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to connect to face recognition service',
            ];
        }
    }

    /**
     * Delete face data for an employee
     */
    public function deleteFace(string $employeeId): array
    {
        try {
            $response = Http::delete("{$this->apiUrl}/api/face/delete/{$employeeId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Failed to delete face',
            ];

        } catch (\Exception $e) {
            Log::error('Face deletion error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to connect to face recognition service',
            ];
        }
    }

    /**
     * Check API health
     */
    public function checkHealth(): array
    {
        try {
            $response = Http::get("{$this->apiUrl}/health");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'unhealthy',
            ];

        } catch (\Exception $e) {
            Log::error('Health check error: ' . $e->getMessage());
            return [
                'status' => 'unreachable',
            ];
        }
    }
}
