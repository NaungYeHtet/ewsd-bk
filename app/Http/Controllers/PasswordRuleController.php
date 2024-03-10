<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRuleRequest;
use App\Settings\PasswordRuleSettings;

class PasswordRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->responseSuccess([
            'results' => app(PasswordRuleSettings::class)->getAll(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(UpdatePasswordRuleRequest $request)
    {
        $settings = app(PasswordRuleSettings::class);

        $settings->min = $request->min;
        $settings->max = $request->max;
        $settings->letters = $request->letters;
        $settings->numbers = $request->numbers;
        $settings->mixed_case = $request->mixed_case;
        $settings->symbols = $request->symbols;

        $settings->save();

        return $this->responseSuccess([
            'results' => $settings->getAll(),
        ], 'Password rules updated successfully');
    }
}
