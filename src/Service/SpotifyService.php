<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpotifyService
{
    private ?string $accessToken = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $clientId,
        private string $clientSecret
    ) {}

    // Étape A : Obtenir un jeton d'authentification
    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = $this->httpClient->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        $data = $response->toArray();
        $this->accessToken = $data['access_token'];

        return $this->accessToken;
    }

    // Étape B : Chercher le morceau
    public function getTrackDuration(string $artistName, string $trackTitle): ?int
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => [
                    // On force la recherche par artiste ET par titre pour plus de précision
                    'q' => $artistName . ' ' . $trackTitle,
                    'type' => 'track',
                    'limit' => 1
                ]
            ]);

            $data = $response->toArray();

            if (!empty($data['tracks']['items'])) {
                $track = $data['tracks']['items'][0];
                // Spotify renvoie la durée en millisecondes, on convertit en secondes
                return (int) round($track['duration_ms'] / 1000);
            }
        } catch (\Exception $e) {
            // Si erreur (ex: jeton expiré, limite atteinte), on retourne null
            return null;
        }

        return null;
    }
}