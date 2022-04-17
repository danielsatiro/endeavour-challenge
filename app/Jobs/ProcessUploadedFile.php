<?php

namespace App\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\CSVReader;

class ProcessUploadedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;
    private $fileExtension;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath, $fileExtension)
    {
        $this->filePath = $filePath;
        $this->fileExtension = $fileExtension;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contentArray = [];

        switch ($this->fileExtension) {
            case 'csv':
                $contentArray = CSVReader::readWithHead(Storage::path($this->filePath));
                break;

            case 'json':
                $content = Storage::get($this->filePath);
                $contentArray = json_decode($content, true);
                break;

            case 'xml':
                $content = Storage::get($this->filePath);
                $contentArray = $this->convertXmlToArray($content);
                break;

            default:
                throw new Exception('Undefined or not implemented extension');
                break;
        }

        foreach ($contentArray as $value) {
            ProcessData::dispatch($value);
        }
    }

    private function convertXmlToArray($content)
    {
        $xmlObject = simplexml_load_string($content);
        $json = json_encode($xmlObject);
        $contentDecoded = json_decode($json, true);
        return $contentDecoded['row'];
    }
}
