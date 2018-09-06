<?php

namespace Themosis\Metabox\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class MetaboxApiController extends Controller
{
    /**
     * GET /metabox/{id} API route
     */
    public function show(Request $request, $id)
    {
        $abstract = sprintf('themosis.metabox.%s', $id);

        if (app()->bound($abstract)) {
            $metabox = app('themosis.metabox.'.$id);
        } else {
            throw new HttpResponseException(response()->json([
                'message' => 'The metabox with ID ['.$id.'] does not exist.',
                'errors' => true
            ], 404));
        }

        return response()->json($metabox->toArray());
    }
}
