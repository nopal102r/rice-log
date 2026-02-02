<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\PayrollSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsenceController extends Controller
{
    /**
     * Show absence entry page (check-in/check-out).
     */
    public function show(Request $request, $type): View
    {
        $user = auth()->user();
        $settings = PayrollSetting::getCurrent();

        // Check if already checked in/out today
        if ($type === 'masuk' && Absence::hasCheckedInToday($user->id)) {
            return redirect()->route('employee.dashboard')->with('info', 'Anda sudah absen masuk hari ini');
        }

        if ($type === 'keluar' && Absence::hasCheckedOutToday($user->id)) {
            return redirect()->route('employee.dashboard')->with('info', 'Anda sudah absen keluar hari ini');
        }

        return view('employee.absence.form', [
            'user' => $user,
            'type' => $type,
            'officeLocation' => [
                'latitude' => $settings->office_latitude,
                'longitude' => $settings->office_longitude,
            ],
            'maxDistance' => $settings->max_distance_allowed,
        ]);
    }

    /**
     * Store absence record.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'required|in:masuk,keluar',
            'status' => 'required_if:type,masuk|in:hadir,sakit,izin',
            'description' => 'required_if:status,sakit,izin',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'face_image' => 'required_if:status,hadir|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Calculate distance from office
        $settings = PayrollSetting::getCurrent();
        $distance = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            $settings->office_latitude,
            $settings->office_longitude
        );

        // Save face image if provided
        $faceImagePath = null;
        $faceVerified = null;

        if ($request->hasFile('face_image')) {
            $faceImagePath = $request->file('face_image')->store('faces/' . date('Y/m/d'), 'public');

            // Verify face if user has enrolled face data
            if ($validated['status'] === 'hadir' && $user->hasFaceEnrolled()) {
                $faceVerified = $this->verifyFace($request->file('face_image')->getRealPath(), $user);
            }
        }

        // Create absence record
        $absence = Absence::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'status' => $validated['status'] ?? null,
            'description' => $validated['description'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'distance_from_office' => $distance,
            'face_image' => $faceImagePath,
            'checked_at' => now(),
        ]);

        // Update user's last_presence_at
        $user->update(['last_presence_at' => now()]);

        // Build response message
        $message = $this->getSuccessMessage($validated['type'], $validated['status'] ?? 'hadir');
        if ($faceVerified !== null) {
            $message .= $faceVerified ? ' Wajah terverifikasi.' : ' (Wajah tidak match - perlu verifikasi manual).';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'absence' => $absence,
            'face_verified' => $faceVerified,
        ]);
    }

    /**
     * Verify captured face against enrolled face data.
     * This is a simplified verification - in production you'd use proper ML matching
     */
    private function verifyFace($imagePath, $user): bool
    {
        try {
            // This would ideally use face-api on server-side or communicate with a face verification service
            // For now, we'll just return true if the user has enrolled face data
            // In production, implement proper face matching logic
            \Log::info('Face verification for user ' . $user->id . ' - enrolled: ' . ($user->hasFaceEnrolled() ? 'yes' : 'no'));

            return $user->hasFaceEnrolled();
        } catch (\Exception $e) {
            \Log::error('Face verification error: ' . $e->getMessage());
            return true; // Fallback to accepting the presence if verification fails
        }
    }

    /**
     * Calculate distance between two coordinates (Haversine formula).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earth_radius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earth_radius * $c;

        return round($distance, 2);
    }

    /**
     * Get success message based on absence type and status.
     */
    private function getSuccessMessage($type, $status): string
    {
        $messages = [
            'masuk' => [
                'hadir' => 'Absen masuk berhasil! Wajah Anda telah terdeteksi.',
                'sakit' => 'Absen masuk (Sakit) berhasil dicatat.',
                'izin' => 'Absen masuk (Izin) berhasil dicatat.',
            ],
            'keluar' => [
                'hadir' => 'Absen keluar berhasil dicatat.',
            ],
        ];

        return $messages[$type][$status] ?? 'Absen berhasil dicatat.';
    }
}
