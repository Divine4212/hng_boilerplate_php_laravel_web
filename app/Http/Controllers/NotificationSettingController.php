<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationSettingsController extends Controller
{
    /**
     * Update the user's notification settings.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'email_notifications' => 'required|boolean',
            'push_notifications' => 'required|boolean',
            'sms_notifications' => 'required|boolean',
        ]);

        // Get authenticated user
        $user = $request->user();

        // Update or create notification preferences
        $preferences = NotificationPreference::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        // Send notification about updated settings
        NotificationService::sendEmail($user, 'Notification Settings Updated', 'Your notification settings have been updated.');

        return response()->json([
            'message' => 'Notification settings updated successfully.',
            'data' => $preferences
        ]);
    }

    /**
     * Display the user's notification settings.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        // Get authenticated user
        $user = $request->user();

        // Retrieve user's notification preferences
        $preferences = NotificationPreference::where('user_id', $user->id)->firstOrFail();

        return response()->json($preferences);
    }
}
