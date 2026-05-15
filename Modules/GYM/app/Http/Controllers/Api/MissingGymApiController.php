<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;

/**
 * @OA\Tag(
 *     name="Gym Operations",
 *     description="API Endpoints for Gym Management and Operations"
 * )
 */
class MissingGymApiController extends Controller
{
    protected $gymRepository;

    public function __construct(GymRepositoryInterface $gymRepository)
    {
        $this->gymRepository = $gymRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/gym/dashboard/summary",
     *     summary="Dashboard Summary",
     *     description="Consolidates KPIs for the Gym Owner's main screen.",
     *     tags={"Gym Operations"},
     *     @OA\Parameter(
     *         name="gym_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function dashboardSummary(Request $request)
    {
        $request->validate(['gym_id' => 'required|string']); // fallback to string if uuid validator not present
        $data = $this->gymRepository->getDashboardSummary($request->gym_id);
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/gym/attendance/check-in",
     *     summary="Member Attendance Check-In",
     *     description="Registers a member's visit to the gym.",
     *     tags={"Gym Operations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gym_id", "user_id", "method"},
     *             @OA\Property(property="gym_id", type="string", format="uuid"),
     *             @OA\Property(property="user_id", type="string", format="uuid"),
     *             @OA\Property(property="method", type="string", enum={"qr_code", "manual", "biometric"}),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Check-in successful.")
     * )
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'gym_id' => 'required|string',
            'user_id' => 'required|string',
            'method' => 'required|in:qr_code,manual,biometric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);
        
        $data = $this->gymRepository->checkInMember($request->all());
        return response()->json(['success' => true, 'message' => 'Check-in successful.', 'data' => $data], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/provisions/sections",
     *     summary="Provision Sections",
     *     description="Dynamically configures the app's available modules based on gym settings.",
     *     tags={"Gym Operations"},
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function provisionSections()
    {
        $data = $this->gymRepository->getProvisionSections();
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/gym/enquiries",
     *     summary="Gym Enquiries",
     *     description="Allows potential members to ask questions to a gym owner.",
     *     tags={"Gym Operations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gym_id", "subject", "message", "enquiry_type"},
     *             @OA\Property(property="gym_id", type="string", format="uuid"),
     *             @OA\Property(property="subject", type="string"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="enquiry_type", type="string", enum={"membership_plans", "facilities", "trial_request"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Enquiry sent successfully.")
     * )
     */
    public function storeEnquiry(Request $request)
    {
        $request->validate([
            'gym_id' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string',
            'enquiry_type' => 'required|in:membership_plans,facilities,trial_request'
        ]);
        
        $this->gymRepository->storeEnquiry($request->all());
        return response()->json(['success' => true, 'message' => 'Enquiry sent successfully. The gym will contact you shortly.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/gym/reviews",
     *     summary="Gym Reviews",
     *     description="Submitting member feedback.",
     *     tags={"Gym Operations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gym_id", "rating", "comment"},
     *             @OA\Property(property="gym_id", type="string", format="uuid"),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="anonymous", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Review published.")
     * )
     */
    public function storeReview(Request $request)
    {
        $request->validate([
            'gym_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'anonymous' => 'nullable|boolean'
        ]);
        
        $this->gymRepository->storeReview($request->all());
        return response()->json(['success' => true, 'message' => 'Review published.'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/gym/reports/revenue",
     *     summary="Operations Reports",
     *     description="Fetches historical revenue data for chart plotting.",
     *     tags={"Gym Operations"},
     *     @OA\Parameter(
     *         name="gym_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", enum={"weekly", "monthly", "yearly"})
     *     ),
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function revenueReport(Request $request)
    {
        $request->validate([
            'gym_id' => 'required|string',
            'period' => 'required|in:weekly,monthly,yearly'
        ]);
        
        $data = $this->gymRepository->getRevenueReport($request->gym_id, $request->period);
        return response()->json(['success' => true, 'data' => $data], 200);
    }
}
