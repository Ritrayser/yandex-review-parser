<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetReviewsRequest;
use App\Http\Requests\StoreOrganizationRequest;
use App\Models\Organization;
use App\Services\OrganizationService;

class OrganizationController extends Controller
{
    protected $orgService;

    public function __construct(OrganizationService $orgService)
    {
        $this->orgService = $orgService;
    }

    public function index()
    {
        $organizations = $this->orgService->getUserOrganizations(auth()->id());

        return response()->json($organizations);
    }
    public function store(StoreOrganizationRequest $request)
    {
        try {
            $organization = $this->orgService->store(
                auth()->id(),
                $request->yandex_url
            );
            return response()->json($organization);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Organization $organization)
    {
        return response()->json($organization);
    }

    public function getReviews(Organization $organization, GetReviewsRequest $request)
    {
        $page = $request->input('page', 1);

        $perPage = $request->input('per_page', 50);

        $reviews = $this->orgService->getReviews($organization, $page, $perPage);

        return response()->json($reviews);
    }

    public function refresh(Organization $organization)
    {
        try {
            $updated = $this->orgService->refresh($organization);
            return response()->json($updated);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Organization $organization)
    {
        $this->orgService->delete($organization);
        
        return response()->json(['message' => 'Организация удалена']);
    }
}
