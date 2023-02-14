<?php

namespace App\Http\Controllers\API\BillsPayment;

use App\Http\Controllers\Controller;
use App\Models\BillPaymentService;
use App\Services\VtpassService;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillPaymentServiceController extends Controller
{
    use ApiResponder;

    protected $vtpassService;
    public function __construct(VtpassService $vtpassService)
    {
        $this->vtpassService = $vtpassService;
    }

    public function listServices() {
        $services = BillPaymentService::all();

        return $this->successResponse(
            "Services List",
            $services,
            Response::HTTP_OK
        );
    }

    public function getServiceId($identifier) {
        $rep = $this->vtpassService->getServiceId($identifier);

        return $this->successResponse(
            "Services",
            $rep['content'],
            Response::HTTP_OK
        );
    }
}
