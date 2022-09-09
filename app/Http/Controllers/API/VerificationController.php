<?php

namespace App\Http\Controllers\API;

use App\Constants\IdentityType;
use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Traits\ApiResponder;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    use ApiResponder;

    public function __construct(VerificationService $verification)
    {
        $this->verification = $verification;
    }

    public function verifyDetails(Request $request)
    {
        $request->validate([
            'identity_type' => ['required', 'in:bvn,nin,voters_card,passport,drivers_license'],
            'bvn' => ['required_if:identity_type,bvn', 'string', 'size:11'],
            'vin' => ['required_if:identity_type,voters_card', 'string'],
            'passport_no' => ['required_if:identity_type,passport', 'string'],
            'nin' => ['required_if:identity_type,nin', 'string'],
            'driver_license_no' => ['required_if:identity_type,drivers_license', 'string'],
            'date_of_birth' => ['required', 'string'],
        ]);

        $user = auth()->user();

        if (!$user->userDetail) {
            return $this->failureResponse("Please complete your profile before you continue", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $params = [
            'dob' => $request->date_of_birth,
            'firstname' => $user->userDetail->first_name,
            'lastname' => $user->userDetail->last_name,
        ];

        $datebirth = $request->date_of_birth;
        $pieces = explode("-", $datebirth);
        $votersCardDob = $pieces[2] . '-' . $pieces[0] . '-' . $pieces[1];
        //dd($votersCardDob);
        $identityType = $request->identity_type;

        switch ($identityType) {
            case IdentityType::DRIVERS_LICENSE:
                $response = $this->verification->verifyLicense($request->driver_license_no, $params);
                $idNo = $response->data->licenseNo;
                break;
            case IdentityType::NIN:
                $response = $this->verification->verifyNin($request->nin, $params);
                $idNo = $response->data->nin;
                break;
            case IdentityType::VOTERS_CARD:
                $response = $this->verification->verifyVin($request->vin, $votersCardDob, $params);
                $idNo = $response->data->vin;
                break;
            case IdentityType::PASSPORT:
                $response = $this->verification->verifyPassport($request->passport_no, $params);
                $idNo = $response->data->passportNo;
                break;
            default:
                $response = $this->verification->verifyBvn($request->bvn, $params);
                $idNo = $response->data->bvn;
                break;
        }

        if ($response->status === 'success') {
            //dd($idNo);
            //   dd($response);
            $data = $response->data;
            $lastName = $data->lastname ?? $data->lastName;

            if ($lastName !== $user->userDetail->last_name) {

                return $this->failureResponse("Verification Failed", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            //dd($user->phone_number);
            $verificationData = Verification::create([
                'user_id' => $user->id,
                'identity_type' => $identityType,
                'identity_number' => $idNo,
                //'passport_base64_string' => $data->photo,
                'first_name' => $data->firstname ?? $data->firstName,
                'last_name' => $data->lastname ?? $data->lastName,
                'date_of_birth' => $data->birthdate ?? $request->date_of_birth,
                'phone_number' => $data->phone ?? $user->phone_number,
                'gender' => $data->gender,
                'payload' => $data
            ]);

            //dd($verificationData);
            return $this->successResponse(
                "Verification Passed. Thank you.",
                $verificationData,
                Response::HTTP_OK
            );
        }

        return $this->failureResponse(
            "An error occured. Please contact support",
            Response::HTTP_BAD_REQUEST
        );
    }
}
