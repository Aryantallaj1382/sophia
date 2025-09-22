<?php

namespace App\Http\Controllers\Api\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $for = $request->query('for');
        $user = auth()->user();
        $certificates = Certificate::where('user_id', $user->id)->where('for', $for)->paginate();
        $certificates->getCollection()->transform(function ($certificate) {
            return [
                'id' => $certificate->id,
                'for' => $certificate->for,
                'created_at' => $certificate->created_at,
                'updated_at' => $certificate->updated_at,
                'status' => $certificate->status,
                'type' => $certificate->type,
                'file' => $certificate->file,
                'title' => $certificate->title,
            ];
        });
        return api_response($certificates);

    }
}
