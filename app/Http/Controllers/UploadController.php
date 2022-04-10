<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Jobs\ProcessUploadedFile;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        if ($save->isFinished()) {
            return response()->json(MediaService::saveFile($save->getFile()));
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Upload file to process',
            'buttonTitle' => 'Process',
            'action' => route('store'),
            'method' => 'POST',
        ];
        return view('upload.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'upload_file' => 'required|string',
            'upload_file_extension' => 'required|string|in:json,xml,csv',
        ]);

        $this->dispatch(new ProcessUploadedFile($request->get('upload_file'), $request->get('upload_file_extension')));

        $horizonLink = route('horizon.index');
        return back()->with('status', "File sent for processing successfully <a href=\"{$horizonLink}\" target=\"_blank\">track processing in Horizon</a>");
    }
}
