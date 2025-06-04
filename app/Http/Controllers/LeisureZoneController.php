<?php

namespace App\Http\Controllers;

use App\Services\LeisureZoneService;
use Illuminate\Http\Request;

class LeisureZoneController extends Controller
{
    protected $service;

    public function __construct(LeisureZoneService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['city', 'university', 'search']);
        $zones = $this->service->searchZones($filters);

        return view('leisure_zones.index', compact('zones'));
    }

    public function create()
    {
        return view('leisure_zones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'image' => 'nullable|image',
        ]);

        $image = $request->file('image');

        $this->service->createZone($data, $image);

        return redirect()->route('leisure-zones.index')->with('status', 'Zona de ocio creada');
    }

    public function show(int $id)
    {
        $zone = $this->service->findZone($id);
        abort_if(!$zone, 404);
        return view('leisure_zones.show', compact('zone'));
    }
}
