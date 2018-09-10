<?php

namespace Themosis\Metabox\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        try {
            $metabox = $this->manager->getFields(app($abstract), $request);
        } catch (\Exception $e) {
            throw new HttpResponseException(response()->json([
                'message' => $e->getMessage(),
                'errors' => true
            ], 500));
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

        try {
            $this->manager->saveFields(app($abstract), $request);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => true
            ], 500);
        }

        return response()->json([
            'message' => 'Metabox data saved.',
            'errors' => false
        ]);
    }
}
