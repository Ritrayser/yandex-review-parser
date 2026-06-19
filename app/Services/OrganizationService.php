<?php

namespace App\Services;

use App\Models\Organization;
use App\Services\YandexParserService;

class OrganizationService
{
    protected $parser;

    public function __construct(YandexParserService $parser)
    {
        $this->parser = $parser;
    }

    public function getUserOrganizations(int $userId): array
    {
        return Organization::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    public function store(int $userId, string $yandexUrl): Organization
    {
        $existing = Organization::where('user_id', $userId)
            ->where('yandex_url', $yandexUrl)
            ->first();

        if ($existing && $existing->last_parsed_at > now()->subHour()) {
            return $existing;
        }

        $parsedData = $this->parser->parse($yandexUrl);

        return Organization::updateOrCreate(
            [
                'user_id'    => $userId,
                'yandex_url' => $yandexUrl,
            ],
            [
                'organization_id' => $parsedData['organization_id'],
                'name'            => $parsedData['name'],
                'average_rating'  => $parsedData['average_rating'],
                'total_ratings'   => $parsedData['total_ratings'],
                'total_reviews'   => $parsedData['total_reviews'],
                'reviews_cache'   => $parsedData['reviews'],
                'last_parsed_at'  => now(),
            ]
        );
    }

    public function getReviews(Organization $organization, int $page, int $perPage): array
    {
        $reviews = $organization->reviews_cache ?? [];
        $total = count($reviews);
        $offset = ($page - 1) * $perPage;
        $data = array_slice($reviews, $offset, $perPage);

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    public function refresh(Organization $organization): Organization
    {
        $parsedData = $this->parser->parse($organization->yandex_url);

        $organization->update([
            'name'            => $parsedData['name'],
            'average_rating'  => $parsedData['average_rating'],
            'total_ratings'   => $parsedData['total_ratings'],
            'total_reviews'   => $parsedData['total_reviews'],
            'reviews_cache'   => $parsedData['reviews'],
            'last_parsed_at'  => now(),
        ]);

        return $organization;
    }

    public function delete(Organization $organization): void
    {
        $organization->delete();
    }
}