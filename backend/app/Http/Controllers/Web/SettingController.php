<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $currentSettings = Setting::getSettings();
        
        // Validate only the fields that are provided
        $rules = [];
        $data = [];
        
        if ($request->filled('latitude')) {
            $rules['latitude'] = 'numeric|between:-90,90';
            $data['latitude'] = $request->latitude;
        }
        
        if ($request->filled('longitude')) {
            $rules['longitude'] = 'numeric|between:-180,180';
            $data['longitude'] = $request->longitude;
        }
        
        if ($request->filled('radius')) {
            $rules['radius'] = 'integer|min:1|max:1000';
            $data['radius'] = $request->radius;
        }
        
        if ($request->filled('checkin_start')) {
            $rules['checkin_start'] = 'date_format:H:i';
            $data['checkin_start'] = $request->checkin_start;
        }
        
        if ($request->filled('checkin_end')) {
            $rules['checkin_end'] = 'date_format:H:i';
            $data['checkin_end'] = $request->checkin_end;
        }
        
        if ($request->filled('checkout_start')) {
            $rules['checkout_start'] = 'date_format:H:i';
            $data['checkout_start'] = $request->checkout_start;
        }
        
        if ($request->filled('checkout_end')) {
            $rules['checkout_end'] = 'date_format:H:i';
            $data['checkout_end'] = $request->checkout_end;
        }
        
        // Validate only the provided data
        if (!empty($rules)) {
            $request->validate($rules);
        }
        
        // Update only the provided fields
        if (!empty($data)) {
            $currentSettings->update($data);
            $updatedFields = [];
            foreach (array_keys($data) as $field) {
                switch ($field) {
                    case 'latitude':
                        $updatedFields[] = 'Latitude';
                        break;
                    case 'longitude':
                        $updatedFields[] = 'Longitude';
                        break;
                    case 'radius':
                        $updatedFields[] = 'Radius';
                        break;
                    case 'checkin_start':
                        $updatedFields[] = 'Waktu Mulai Check-in';
                        break;
                    case 'checkin_end':
                        $updatedFields[] = 'Waktu Akhir Check-in';
                        break;
                    case 'checkout_start':
                        $updatedFields[] = 'Waktu Mulai Check-out';
                        break;
                    case 'checkout_end':
                        $updatedFields[] = 'Waktu Akhir Check-out';
                        break;
                }
            }
            $message = 'Pengaturan berhasil diperbarui. Field yang diubah: ' . implode(', ', $updatedFields);
        } else {
            $message = 'Tidak ada perubahan yang dilakukan pada pengaturan.';
        }

        return redirect()->route('admin.settings.index')
            ->with('success', $message);
    }
}