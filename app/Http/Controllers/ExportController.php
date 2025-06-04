<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use ZipArchive;

class ExportController extends Controller
{
    /**
     * Export the authenticated user's data as a ZIP of CSV files.
     */
    public function exportUserData(): Response
    {
        $user = auth()->user();

        $profileData = $user->profile ? [$user->profile->toArray()] : [];
        $listingsData = $user->listings()->get()->toArray();
        $eventsData = $user->events()->get()->toArray();

        $zip = new ZipArchive();
        $fileName = storage_path('app/user_export_' . $user->id . '.zip');

        if ($zip->open($fileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No se pudo crear el archivo de exportación.');
        }

        $zip->addFromString('profile.csv', $this->toCsv($profileData));
        $zip->addFromString('listings.csv', $this->toCsv($listingsData));
        $zip->addFromString('events.csv', $this->toCsv($eventsData));
        $zip->close();

        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    protected function toCsv(array $data): string
    {
        $handle = fopen('php://temp', 'r+');

        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }

        rewind($handle);
        return stream_get_contents($handle);
    }
}
