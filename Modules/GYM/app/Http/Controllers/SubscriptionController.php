<?php

namespace Modules\GYM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;

class SubscriptionController extends Controller
{
    private $gymRepository;

    public function __construct(GymRepositoryInterface $gymRepository)
    {
        $this->gymRepository = $gymRepository;
    }

    /**
     * Display a listing of gym subscriptions.
     */
    public function index()
    {
        $subscriptions = $this->gymRepository->getAllSubscriptions();
        return view('gym::subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the specified subscription.
     */
    public function show($id)
    {
        $subscription = $this->gymRepository->getSubscriptionById($id);
        return view('gym::subscriptions.show', compact('subscription'));
    }
}
