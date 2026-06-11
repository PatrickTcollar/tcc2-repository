<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $clinics = Clinic::orderBy('name')->get();
        return view('auth.register', compact('clinics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'clinic_option' => ['required', 'in:existing,new'],
            'clinic_id'     => ['required_if:clinic_option,existing', 'nullable', 'exists:clinics,id'],
            'clinic_name'   => ['required_if:clinic_option,new', 'nullable', 'string', 'max:255'],
            'clinic_cnpj'   => ['nullable', 'string', 'max:18', 'unique:clinics,cnpj'],
            'clinic_email'  => ['nullable', 'email', 'max:255'],
            'clinic_phone'  => ['nullable', 'string', 'max:20'],
            'clinic_address'=> ['nullable', 'string', 'max:500'],
        ]);

        $clinicId = null;

        if ($request->clinic_option === 'new') {
            $clinic = Clinic::create([
                'name'    => $request->clinic_name,
                'cnpj'    => $request->clinic_cnpj,
                'email'   => $request->clinic_email,
                'phone'   => $request->clinic_phone,
                'address' => $request->clinic_address,
            ]);
            $clinicId = $clinic->id;
        } else {
            $clinicId = $request->clinic_id;
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'clinic_id' => $clinicId,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
