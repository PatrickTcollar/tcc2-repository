<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index()
    {
        $clinic = auth()->user()->clinic;
        return view('clinics.show', compact('clinic'));
    }

    public function create()
    {
        return view('clinics.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'cnpj'    => 'nullable|string|max:18|unique:clinics,cnpj',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $clinic = Clinic::create($data);
        auth()->user()->update(['clinic_id' => $clinic->id]);

        return redirect()->route('clinics.show', $clinic)->with('success', 'Clínica cadastrada com sucesso!');
    }

    public function show(Clinic $clinic)
    {
        $clinic->load('users');
        return view('clinics.show', compact('clinic'));
    }

    public function edit(Clinic $clinic)
    {
        return view('clinics.edit', compact('clinic'));
    }

    public function update(Request $request, Clinic $clinic)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'cnpj'    => 'nullable|string|max:18|unique:clinics,cnpj,' . $clinic->id,
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $clinic->update($data);

        return redirect()->route('clinics.show', $clinic)->with('success', 'Clínica atualizada com sucesso!');
    }

    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
        return redirect()->route('dashboard')->with('success', 'Clínica removida.');
    }
}
