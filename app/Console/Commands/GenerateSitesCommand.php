<?php

namespace App\Console\Commands;

use App\Models\Site;
use Illuminate\Console\Command;

class GenerateSitesCommand extends Command
{
    protected $signature = 'generate:sites';

    protected $description = 'Generate database/data/sites_converted.csv file.';

    public function handle(): void
    {
        $bar = $this->output->createProgressBar(Site::count());
        $sites = Site::all();
        $siteCollection = $sites->map(function ($site) {
            return [
                'id' => $site->csv_id,
                'name' => $this->isDomainValid($site->name) ? $site->org_name : $site->name,
                'domain' => $site->domain,
                'country' => $site->country,
                'type' => $site->type,
                'category' => $site->category,
                'popularity_index' => $site->popularity_index,
            ];
        });

        $siteCollection->prepend([
            'id' => 'id',
            'name' => 'name',
            'domain' => 'domain',
            'country' => 'country',
            'type' => 'type',
            'category' => 'category',
            'popularity_index' => 'popularity_index',
        ]);

        if (file_exists(database_path('data/sites_converted.csv'))) {
            unlink(database_path('data/sites_converted.csv'));
        }

        $siteCollection->each(function ($site) use (&$bar) {
            $bar->advance();
            $fp = fopen(database_path('data/sites_converted.csv'), 'a');
            fputcsv($fp, $site);
            fclose($fp);
        });

        $bar->finish();
    }

    private function isDomainValid(string $domain): bool
    {
        return preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/', $domain);
    }
}
