<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class AdminCertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('user')->latest()->paginate(15);
        return view('admin.certificates.index', compact('certificates'));
    }

    public function edit(Certificate $certificate)
    {
        return view('admin.certificates.edit', compact('certificate'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:5120', // 5MB max
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('certificates'), $fileName);

            $certificate->file = 'certificates/' . $fileName;
        }


        $certificate->status = $request->status;
        $certificate->save();

        return redirect()->route('admin.certificates.index')->with('success', 'Certificate updated successfully.');
    }

}
