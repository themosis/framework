<?php

namespace Themosis\Metabox\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Themosis\Metabox\MetaboxException;
use Themosis\Metabox\MetaboxManagerInterface;

class MetaboxApiController extends Controller
{
    /**
     * @var MetaboxManagerInterface
     */
    protected $manager;

    public function __construct(MetaboxManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * GET /metabox/{id} API route
     *
     * @param Request $request
     * @param string  $id      The metabox unique ID.
     *
     * @return JsonResponse
     */
    public function show(Request $request, $id)
    {
        $abstract = sprintf('themosis.metabox.%s', $id);

        if (app()->bound($abstract)) {
            $metabox = $this->manager->getFields(app($abstract), $request);
        } else {
            throw new HttpResponseException(response()->json([
                'message' => 'The metabox with ID ['.$id.'] does not exist.',
                'errors' => true
            ], 404));
        }

        return response()->json($metabox->toArray());
    }

    /**
     * PUT /metabox/{id} API route
     * Handle metabox fields update/save data.
     *
     * @param Request $request
     * @param string  $id      The metabox unique ID.
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $abstract = sprintf('themosis.metabox.%s', $id);

        if (! app()->bound($abstract)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unable to save data. Metabox not found.',
                'errors' => true
            ], 400));
        }

        try {
            $this->manager->saveFields(app($abstract), $request);
        } catch (MetaboxException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => true
            ], 400);
        }

        return response()->json([
            'message' => 'Metabox data saved.',
            'errors' => false
        ]);
    }
}
