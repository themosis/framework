<?php

namespace Themosis\Metabox\Controllers;

use App\Http\Controllers\Controller;

class MetaboxApiController extends Controller
{
    /**
     * Handle /metabox/{id} API route.
     */
    public function show($id)
    {
        return response()->json([
            'id' => $id
        ]);
    }
}
