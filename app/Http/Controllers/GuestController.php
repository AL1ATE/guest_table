<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::all();
        return view('guests.index', compact('guests'));
    }

    public function store(Request $request)
    {
        $generalValidator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        if ($generalValidator->fails()) {
            return response()->json(['errors' => $generalValidator->errors()], 422);
        }

        $emailValidator = Validator::make($request->only('email'), [
            'email' => 'required|email|unique:guests,email',
        ], [
            'email.unique' => 'Этот email уже используется.',
        ]);

        $phoneValidator = Validator::make($request->only('phone'), [
            'phone' => 'required|unique:guests,phone',
        ], [
            'phone.unique' => 'Этот номер телефона уже используется.',
        ]);

        $errors = [];

        if ($emailValidator->fails()) {
            $errors['email'] = $emailValidator->errors()->first('email');
        }

        if ($phoneValidator->fails()) {
            $errors['phone'] = $phoneValidator->errors()->first('phone');
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $country = $this->getCountryByPhone($request->phone);

        $formattedPhone = $request->phone;
        if (strpos($formattedPhone, '+') !== 0) {
            $formattedPhone = '+' . $formattedPhone;
        }

        Guest::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $formattedPhone,
            'country' => $country,
        ]);

        return response()->json(['success' => 'Гость успешно добавлен.'], 200);
    }

    public function update(Request $request, Guest $guest)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:guests,email,' . $guest->id,
            'phone' => 'required|unique:guests,phone,' . $guest->id,
        ], [
            'email.unique' => 'Этот email уже используется.',
            'phone.unique' => 'Этот номер телефона уже используется.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if ($request->has('country')) {
                $country = $request->country;
            } else {
                $country = $this->getCountryByPhone($request->phone);
            }

            $guest->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $country,
            ]);

            return response()->json(['success' => 'Данные гостя успешно обновлены.'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['message' => 'Произошла ошибка при обновлении гостя.']], 500);
        }
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->back()->with('success', 'Гость успешно удален');
    }

    private function getCountryByPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strpos($phone, '+') === 0) {
            $phone = substr($phone, 1);
        }
        elseif (strpos($phone, '00') === 0) {
            $phone = substr($phone, 2);
        }

        $countryCodes = [
            '7' => 'Россия',
            '1' => 'США',
            '86' => 'Китай',
            '91' => 'Индия',
            '44' => 'Великобритания',
            '81' => 'Япония',
            '49' => 'Германия',
            '82' => 'Южная Корея',
            '33' => 'Франция',
            '39' => 'Италия',
        ];

        foreach ($countryCodes as $code => $country) {
            if (strpos($phone, $code) === 0) {
                return $country;
            }
        }

        return null;
    }

}
