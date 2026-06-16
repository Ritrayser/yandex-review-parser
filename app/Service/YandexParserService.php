<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YandexParserService
{
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36';

    public function extractOrgId(string $url): ?string
    {
        if (preg_match('/org\/[^\/]+\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(\d{7,})/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function parse(string $yandexUrl): array
    {
        $orgId = $this->extractOrgId($yandexUrl);
        if (!$orgId) {
            throw new \Exception('Не удалось извлечь ID организации');
        }

        $info = $this->fetchOrganizationInfo($orgId);
        $reviews = $this->fetchAllReviews($orgId);

        return [
            'organization_id' => $orgId,
            'name' => $info['name'] ?? 'Без названия',
            'average_rating' => $info['rating'] ?? 0,
            'total_ratings' => $info['ratings_count'] ?? 0,
            'total_reviews' => count($reviews),
            'reviews' => $reviews,
        ];
    }

    private function fetchOrganizationInfo(string $orgId): array
    {
        $response = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->timeout(10)
            ->get('https://yandex.ru/maps/api/organization/info', [
                'id' => $orgId,
                'lang' => 'ru',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Ошибка получения информации об организации');
        }

        return $response->json();
    }

    private function fetchAllReviews(string $orgId, int $maxReviews = 600): array
    {
        $allReviews = [];
        $page = 1;
        $perPage = 20;

        while (count($allReviews) < $maxReviews) {
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                ->timeout(10)
                ->get('https://yandex.ru/maps/api/organization/reviews', [
                    'id' => $orgId,
                    'page' => $page,
                    'limit' => $perPage,
                    'lang' => 'ru',
                ]);

            if (!$response->successful()) {
                break;
            }

            $data = $response->json();
            $reviews = $this->extractReviews($data);

            if (empty($reviews)) {
                break;
            }

            $allReviews = array_merge($allReviews, $reviews);
            $page++;
            if ($page > 30) break;
            usleep(500000);
        }

        return array_slice($allReviews, 0, $maxReviews);
    }

    private function extractReviews(array $apiData): array
    {
        $reviews = [];
        $items = $apiData['reviews'] ?? $apiData['items'] ?? [];

        foreach ($items as $review) {
            $reviews[] = [
                'id' => $review['id'] ?? uniqid(),
                'author' => $review['author']['name'] ?? $review['user']['name'] ?? 'Аноним',
                'date' => $review['date'] ?? $review['created_at'] ?? date('Y-m-d'),
                'text' => $review['text'] ?? $review['review_text'] ?? '',
                'rating' => (int)($review['rating'] ?? $review['stars'] ?? 0),
            ];
        }

        return $reviews;
    }
}