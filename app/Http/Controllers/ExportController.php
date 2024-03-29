<?php

namespace App\Http\Controllers;

use App\API\TroubleFree;

class ExportController extends Controller
{
    /**
     * Get the TroubleFree image through the TroubleFree API
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function troubleFreeImage($id)
    {
        $asset = TroubleFree::getFile($id);

        if(!$asset) return response('', 404);

        return response($asset->body(), 200, [
            'Content-Type'        => $asset->header('Content-Type'),
            'Content-Length'      => $asset->header('Content-Length'),
            'Content-Disposition' => $asset->header('Content-Disposition'),
        ]);
    }
}
