<?php

namespace App\Http\Controllers;

use App\Models\ImportStatus;
use Illuminate\Http\Request;

class ImportStatusController extends Controller
{
    /**
     * Show the progress of a specific import job.
     */
    public function show($id)
    {
        $status = ImportStatus::find($id);

        if (! $status) {
            return response()->json(['message' => 'Import status not found'], 404);
        }

        return response()->json([
            'file' => $status->file,
            'status' => $status->status,
            'processed_rows' => $status->processed_rows,
            'total_rows' => $status->total_rows,
            'error_message' => $status->error_message,
            'updated_at' => $status->updated_at,
        ]);
    }
}
