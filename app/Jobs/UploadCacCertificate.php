<?php

namespace App\Jobs;

use App\Models\MerchantDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadCacCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $merchantDetail;

    public function __construct(MerchantDetail $merchantDetail)
    {
        $this->merchantDetail = $merchantDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imageName = $this->merchantDetail->cac_document;
        $file = storage_path() . '/uploads/cac/' . $imageName;

        try {
            // Original
            if (Storage::disk('public')->put('/uploads/cac/' . $imageName, fopen($file, 'r+'))) {
                File::delete($file);
            }

            $this->merchantDetail->update([
                'cac_uploaded_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
