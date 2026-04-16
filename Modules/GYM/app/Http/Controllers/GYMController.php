<?php

namespace Modules\GYM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;

class GYMController extends Controller
{
    private $gymRepository;

    public function __construct(GymRepositoryInterface $gymRepository)
    {
        $this->gymRepository = $gymRepository;
    }

    /**
     * Display a listing of gyms.
     */
    public function index()
    {
        $gyms = $this->gymRepository->getAllGyms();
        return view('gym::index', compact('gyms'));
    }

    /**
     * Show the form for creating a new gym.
     */
    public function create()
    {
        return view('gym::create');
    }

    /**
     * Store a newly created gym in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:gyms,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'member_count_limit' => 'nullable|integer',
            'plans' => 'nullable|array',
            'plans.*.name' => 'required|string',
            'plans.*.price' => 'required|numeric',
        ]);

        // Note: For now, we'll assign the authenticated user as the owner or use a placeholder
        $data = $request->all();
        $data['owner_id'] = auth()->id(); 

        $this->gymRepository->createGym($data);

        return redirect()->route('gym.index')->with('success', 'Gym created successfully!');
    }

    /**
     * Show the form for editing the specified gym.
     */
    public function edit($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        return view('gym::edit', compact('gym'));
    }

    /**
     * Update the specified gym in storage.
     */
    public function update(Request $request, $uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:gyms,email,' . $gym->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $this->gymRepository->updateGym($uuid, $request->all());

        return redirect()->route('gym.index')->with('success', 'Gym updated successfully!');
    }

    /**
     * Remove the specified gym from storage.
     */
    public function destroy($uuid)
    {
        $this->gymRepository->deleteGym($uuid);
        return redirect()->route('gym.index')->with('success', 'Gym deleted successfully!');
    }
}
