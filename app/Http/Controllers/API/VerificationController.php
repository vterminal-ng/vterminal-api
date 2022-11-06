<?php

namespace App\Http\Controllers\API;

use App\Constants\IdentityType;
use App\Http\Controllers\Controller;
use App\Http\Resources\VerificationResource;
use App\Jobs\UploadCacCertificate;
use App\Models\Verification;
use App\Services\DojahVerifyService;
use App\Traits\ApiResponder;
use App\Services\VerifyMeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    use ApiResponder;

    protected $verifyMeService;
    protected $dojahVerifyService;

    public function __construct(VerifyMeService $verifyMeService, DojahVerifyService $dojahVerifyService)
    {
        $this->verifyMeService = $verifyMeService;
        $this->dojahVerifyService = $dojahVerifyService;
    }

    public function verifyDetails(Request $request)
    {
        $request->validate([
            'identity_type' => ['required', 'in:bvn,nin,voters_card,drivers_license'],
            'bvn' => ['required_if:identity_type,bvn'],
            'vin' => ['required_if:identity_type,voters_card'],
            'nin' => ['required_if:identity_type,nin'],
            'driver_license_no' => ['required_if:identity_type,drivers_license'],
        ]);

        $user = auth()->user();

        if ($user->verification) {
            return $this->failureResponse("Identity already verified", Response::HTTP_BAD_REQUEST);
        }

        $params = [
            'dob' => $user->userDetail->date_of_birth, // format is Y-m-d 
            'firstname' => $user->userDetail->first_name,
            'lastname' => $user->userDetail->last_name,
        ];

        //dd($votersCardDob);
        $identityType = $request->identity_type;

        switch ($identityType) {
            case IdentityType::DRIVERS_LICENSE:
                // converting date of birth format to the required format (d-m-Y)
                $params['dob'] = Carbon::createFromFormat("Y-m-d", $params['dob'])->format('d-m-Y');
                $response = $this->verifyMeService->verifyLicense($request->driver_license_no, $params);
                // dd($response->data);
                $idNo = $response->data->licenseNo;
                break;
            case IdentityType::NIN:
                // converting date of birth format to the required format (d-m-Y)
                $params['dob'] = Carbon::createFromFormat("Y-m-d", $params['dob'])->format('d-m-Y');
                $response = $this->verifyMeService->verifyNin($request->nin, $params);
                // dd($response->data);
                $idNo = $response->data->nin;
                break;
            case IdentityType::VOTERS_CARD:
                $response = $this->verifyMeService->verifyVin($request->vin, $params);
                // dd($response->data);
                $idNo = $response->data->vin;
                break;
            case IdentityType::BVN:
                // converting date of birth format to the required format (d-m-Y)
                $params['dob'] = Carbon::createFromFormat("Y-m-d", $params['dob'])->format('d-m-Y');
                $response = $this->verifyMeService->verifyBvn($request->bvn, $params);
                // dd($response->data);
                $idNo = $response->data->bvn;
                break;
            default:
                return $this->failureResponse(
                    "An error occured. Invalid Identity Type.",
                    Response::HTTP_BAD_REQUEST
                );
        }

        $verifyMePayload = $response->data;
        $lastName = $verifyMePayload->lastname ?? $verifyMePayload->lastName;
        $isLastNameSame = strtolower($lastName) == strtolower($user->userDetail->last_name);
        // If identity type is voters card, check verification with only lastname, but otherewise use data of birth and lastname 
        if ($identityType == IdentityType::VOTERS_CARD) {
            if (!$isLastNameSame) {
                return $this->failureResponse("Verification Failed. Identity info. does not match your profile data", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $isDateOfBirthSame = $verifyMePayload->birthdate ==  Carbon::createFromFormat("Y-m-d", $user->userDetail->date_of_birth)->format('d-m-Y');
            if (!$isLastNameSame || !$isDateOfBirthSame) {
                return $this->failureResponse("Verification Failed. Identity info. does not match your profile data", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        //dd($user->phone_number);
        $verifyMeServiceData = Verification::create([
            'user_id' => $user->id,
            'identity_type' => $identityType,
            'identity_number' => $idNo,
            'passport_base64_string' => $verifyMePayload->photo ?? null,
            'first_name' => $verifyMePayload->firstname ?? $verifyMePayload->firstName,
            'last_name' => $verifyMePayload->lastname ?? $verifyMePayload->lastName,
            'date_of_birth' => $verifyMePayload->birthdate ?? $request->date_of_birth,
            'phone_number' => $verifyMePayload->phone ?? $user->phone_number,
            'gender' => $verifyMePayload->gender,
            'payload' => json_encode($verifyMePayload)
        ]);

        //dd($verifyMeServiceData);
        return $this->successResponse(
            "Verification Passed. Thank you.",
            new VerificationResource($verifyMeServiceData),
        );
    }

    public function verifyCacInfo(Request $request) {
        $request->validate([
            'cac_rc_number' => ['required', 'string'],
            'company_name' => ['required', 'string'],
            'cac_certificate' => ['mimes:png,jpg,jpeg', 'max:2048']
        ]);

        $user = auth()->user();

        if($user->merchantDetail->business_verified_at !== null) {
            return $this->failureResponse(
                "Business information verified already!",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($request->hasFile('cac_certificate')) {

            $destination = 'storage/' . $user->merchantDetail->reg_certificate;

            if (File::exists($destination)) {
                File::delete($destination);
            }
            
            $certificate = $request->file('cac_certificate')->store('business', 'public');
            
            // CAC Information
            $cacInfo = $this->dojahVerifyService->lookupCacInfo($request->company_name, $request->cac_rc_number);
            
            if($cacInfo->entity->rc_number !== $request->cac_rc_number) {
                return $this->failureResponse(
                    "Business verification failed! Registration number does not match",
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            
            $verifyCac = $user->merchantDetail()->update([
                'business_verified_at' => \Carbon\Carbon::now(),
                'registered_business_name' => $cacInfo->entity->company_name,
                'rc_number' => $cacInfo->entity->rc_number,
                'date_of_registration' => $cacInfo->entity->date_of_registration,
                'reg_certificate' => $certificate,
            ]);

            $this->dispatch(new UploadCacCertificate($verifyCac));
            
            return $this->successResponse(
                "Business information updated.",
                [
                    "certificate->image_path" => 'storage/' . $certificate
                ],
                Response::HTTP_OK
            );
        } else {
            return $this->failureResponse("Please select a valid image file", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
    }
    
    public function verifyTinInfo(Request $request) {
        $request->validate([
            'tin_no' => ['required', 'string'],
        ]);
        
        $user = auth()->user();

        if($user->merchantDetail->tin_verified_at !== null) {
            return $this->failureResponse(
                "TIN verified already!",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Tax Identification No Information
        $tinInfo = $this->dojahVerifyService->LookupTinNo($request->tin_no);

        //dd($tinInfo);

        if($tinInfo->entity->cac_reg_number !== $user->merchantDetail->rc_number) {
            return $this->failureResponse(
                "Verification failed! Business information does not match.",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if($tinInfo->entity->firstin !== $request->tin_no) {
            return $this->failureResponse(
                "TIN Verification failed!",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->merchantDetail()->update([
            'tin_number' => $tinInfo->entity->firstin,
            'tin_verified_at' => \Carbon\Carbon::now(),
        ]);

        return $this->successResponse(
            "TIN Verification Successful",
            NULL,
            Response::HTTP_OK
        );

    }
}
