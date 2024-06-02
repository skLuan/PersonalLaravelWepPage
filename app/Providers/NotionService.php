<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class NotionService extends ServiceProvider
{
    protected $token;
    protected $databaseId;

    public function __construct()
    {

        $this->token = env('NOTION_API_TOKEN');
        $this->databaseId = env('NOTION_CURRICULUM_VITAE_ID');
    }
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public function getPortfolio(){ {
            // $url = "https://api.notion.com/v1/blocks/{$this->databaseId}/children";
            $url = "https://api.notion.com/v1/blocks/be694f82-eff2-4dac-80aa-537a518712f2/children";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
                'Notion-Version' => '2022-06-28',
            ])->get($url);

            return $response->json();
        }
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
