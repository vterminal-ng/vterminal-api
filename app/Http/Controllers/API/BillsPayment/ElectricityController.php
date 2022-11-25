<?php

namespace App\Http\Controllers\API\BillsPayment;

use App\Http\Controllers\Controller;
use App\Services\VtpassService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Http\Request;

class ElectricityController extends Controller
{
    use ApiResponder, Generators;
    
    protected $vtpassService;
    
    public function __construct(VtpassService $vtpassService)
    {
        $this->vtpassService = $vtpassService;
    }
    
    public function verifyMeter(Request $request) {
        $request->validate([
            'operator_id' => ['required', 'string'],
            'meter_number' => ['required'],
            'meter_type' => ['required', 'string'],
        ]);

        $rep = $this->vtpassService->verifyElectricityMeter($request->meter_type, $request->meter_number, $request->operator_id);

        dd($rep);
    }
}
