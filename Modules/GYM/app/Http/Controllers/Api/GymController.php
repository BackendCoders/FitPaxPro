<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\GYM\app\Http\Requests\RegisterGymRequest;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class GymController extends Controller
{
    public function __construct(
        protected GymRepositoryInterface $gymRepository
    ) {
    }

    /**
     * List all available gym nodes.
     *
     * @OA\Get(
     *     path="/gym",
     *     tags={"Gym Infrastructure"},
     *     summary="List all registered gym infrastructure nodes",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of gym mesh",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $gyms = $this->gymRepository->getAllGyms();
        return response()->json([
            'success' => true,
            'data' => $gyms
        ]);
    }

    /**
     * Register a new gym node.
     *
     * @OA\Post(
     *     path="/gym/store",
     *     tags={"Gym Infrastructure"},
     *     summary="Provision a new gym node into the network",
     *     description="Creates a new gym location with associated metadata and commercial plans.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","phone","address"},
     *             @OA\Property(property="name", type="string", example="Iron Force Elite"),
     *             @OA\Property(property="email", type="string", format="email", example="hq@ironforce.com"),
     *             @OA\Property(property="phone", type="string", example="+919876543210"),
     *             @OA\Property(property="address", type="string", example="123 Tactical Street, Gym District"),
     *             @OA\Property(property="youtube_links", type="array", @OA\Items(type="string", example="https://www.youtube.com/watch?v=dQw4w9WgXcQ")),
     *             @OA\Property(property="member_count_limit", type="integer", example=500),
     *             @OA\Property(property="platform_plan_id", type="string", example="uuid-here"),
     *             @OA\Property(property="template_ids", type="array", @OA\Items(type="string")),
     *             @OA\Property(
     *                 property="custom_plans",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Black Ops Tier"),
     *                     @OA\Property(property="price", type="number", example=2999)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Infrastructure node provisioned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Gym infrastructure node provisioned successfully.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized session"),
     *     @OA\Response(response=422, description="Validation failure")
     * )
     */
    public function store(RegisterGymRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            unset($data['custom_fields'], $data['image'], $data['gallery'], $data['youtube_links']);

            $data['owner_id'] = auth()->id() ?? $request->owner_id;

            if (!$data['owner_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Node ownership assignment failed. Authentication required.'
                ], 401);
            }

            // Create primary node and persist media via repository
            $gym = $this->gymRepository->createGym(
                $request->except(['template_ids', 'custom_plans', 'custom_fields', 'gallery', 'image', 'youtube_links']),
                $request->file('image'),
                $request->file('gallery', []),
                $request->input('youtube_links', [])
            );

            // 3. Sync Platform Blueprints (Templates)
            if ($request->has('template_ids')) {
                $templates = \App\Models\MembershipPlanTemplate::whereIn('id', $request->template_ids)->get();
                foreach ($templates as $template) {
                    \App\Models\GymFeePlan::create([
                        'gym_id' => $gym->id,
                        'name' => $template->name,
                        'tagline' => $template->tagline,
                        'description' => $template->description,
                        'features' => $template->features,
                        'price' => $template->price,
                        'offer_price' => $template->offer_price,
                        'duration_months' => $template->duration_months,
                        'includes_diet_plan' => $template->includes_diet_plan,
                        'includes_trainer' => $template->includes_trainer,
                        'is_active' => true,
                    ]);
                }
            }

            // 4. Provision Custom Plans
            if ($request->has('custom_plans')) {
                foreach ($request->custom_plans as $cp) {
                    $plan = \App\Models\GymFeePlan::create([
                        'gym_id' => $gym->id,
                        'name' => $cp['name'],
                        'tagline' => $cp['tagline'] ?? null,
                        'price' => $cp['price'],
                        'offer_price' => $cp['offer_price'] ?? null,
                        'duration_months' => $cp['duration_months'] ?? 1,
                        'includes_diet_plan' => filter_var($cp['includes_diet_plan'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'includes_trainer' => filter_var($cp['includes_trainer'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'is_active' => true,
                    ]);

                    if (isset($cp['image']) && $cp['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $plan->update(['image' => $cp['image']->store('gyms/plans', 'public')]);
                    }
                }
            }

            // 5. Encrypt Custom Field Intelligence
            if ($request->has('custom_fields')) {
                $gym->saveCustomFields($request->custom_fields);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gym infrastructure node provisioned successfully with all operational assets.',
                'data' => $gym->load(['owner', 'platformPlan', 'feePlans', 'galleryMedia', 'videoMedia'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Signal failure during node registration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing gym node.
     *
     * @OA\Put(
     *     path="/gym/{id}",
     *     tags={"Gym Infrastructure"},
     *     summary="Calibrate existing gym node parameters",
     *     description="Updates profile details, operational status, and custom data field values for a specific node.",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UUID of the gym node to calibrate",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Iron Force Elite V2"),
     *             @OA\Property(property="email", type="string", format="email", example="hq@ironforce.com"),
     *             @OA\Property(property="phone", type="string", example="+919876543210"),
     *             @OA\Property(property="address", type="string", example="123 Tactical Street, Gym Sector 4"),
     *             @OA\Property(property="youtube_links", type="array", @OA\Items(type="string", example="https://www.youtube.com/watch?v=dQw4w9WgXcQ")),
     *             @OA\Property(property="member_count_limit", type="integer", example=1000),
     *             @OA\Property(property="platform_plan_id", type="string", example="uuid-tier-id"),
     *             @OA\Property(property="custom_fields", type="object", example={"color_code": "#FF0000", "branch_id": "BRANCH-01"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Node calibration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Gym profile calibrated successfully.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized access"),
     *     @OA\Response(response=404, description="Node not found")
     * )
     */
    public function update(\Modules\GYM\app\Http\Requests\UpdateGymRequest $request, $id): JsonResponse
    {
        try {
            $gym = $this->gymRepository->getGymById($id);

            $data = $request->validated();

            // Only allow owners or admins to update
            if (auth()->id() !== $gym->owner_id && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You are not the authorized controller of this node.'
                ], 403);
            }

            // Sync Core Data and media via repository
            $this->gymRepository->updateGym(
                $id,
                $data,
                $request->file('image'),
                $request->file('gallery', []),
                $request->input('youtube_links', [])
            );

            // Handle Dynamic Fields Signal
            if ($request->has('custom_fields')) {
                $gym->saveCustomFields($request->custom_fields);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gym profile calibrated successfully.',
                'data' => $gym->fresh(['owner', 'platformPlan', 'feePlans', 'videoMedia', 'galleryMedia'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calibration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve custom field blueprints for the Gym model.
     *
     * @OA\Get(
     *     path="/gym/custom-fields",
     *     tags={"Gym Infrastructure"},
     *     summary="Retrieve dynamic metadata blueprints",
     *     description="Returns all active custom field definitions configured for the Gym infrastructure model.",
     *     @OA\Response(
     *         response=200,
     *         description="Dynamic fields retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getCustomFields(): JsonResponse
    {
        $fields = \App\Models\CustomField::where('model_type', \App\Models\Gym::class)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $fields
        ]);
    }

    /**
     * Get details of a specific gym node.
     *
     * @OA\Get(
     *     path="/gym/{id}",
     *     tags={"Gym Infrastructure"},
     *     summary="Retrieve comprehensive intelligence for a specific node",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UUID of the gym node",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Node intelligence retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Node not found in current sector")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $gym = $this->gymRepository->getGymById($id);
            return response()->json([
                'success' => true,
                'data' => $gym->load(['owner', 'platformPlan', 'feePlans', 'galleryMedia', 'videoMedia'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Node not found in the grid.'
            ], 404);
        }
    }
}
