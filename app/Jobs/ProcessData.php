<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use DateTime;

class ProcessData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Only process records where the age is between 18 and 65 (or unknown)
        if (!empty($this->data['date_of_birth'])) {
            $format = $this->extractDateTimeFormat($this->data['date_of_birth']);
            $date = DateTime::createFromFormat($format, $this->data['date_of_birth']);

            $today = new DateTime('now');
            $age = $today->diff($date)->y;
            if ($age < 18 || $age > 65) {
                return false;
            }

            $this->data['date_of_birth'] = $date->format('Y-m-d H:i:s');
        }

        //Suppose that only records need to be processed for which the credit card number contains three consecutive same digits
        if (!preg_match('/(\d)\1{3,}/', $this->data['credit_card']['number'])) {
            return false;
        }

        $this->data['checked'] = $this->convertToBoolean($this->data['checked']);
        $this->data['interest'] = empty($this->data['interest']) ? null : $this->data['interest'];
        $this->data['date_of_birth'] = empty($this->data['date_of_birth']) ? null : $this->data['date_of_birth'];

        $user = User::create($this->data);
        $user->creditCard()->create($this->data['credit_card']);
    }

    private function extractDateTimeFormat($string)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $string)) {
            return 'Y-m-d';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $string)) {
            return 'Y-m-d H:i:s';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\+|\-)\d{2}:\d{2}$/', $string)) {
            return 'Y-m-d\TH:i:sO';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{3}$/', $string)) {
            return 'Y-m-d H:i:s.v';
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $string)) {
            return 'm/d/Y';
        }

        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $string)) {
            return 'd.m.Y';
        }
    }

    private function convertToBoolean($value)
    {
        $trues = [true, 1, '1', 'true', 'yes', 'True', 'TRUE'];

        if (in_array($value, $trues, true)) {
            return true;
        }

        return false;
    }
}
