<?php

namespace App\Console\Commands;

use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DomainToOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:organization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert domain to organization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $total = count(file(database_path('data/sites_rows.csv')));
        $bar = $this->output->createProgressBar($total - 1); // ignore header
        $bar->start();

        $sites = collect(array_map('str_getcsv', file(database_path('data/sites_rows.csv'))))->map(function ($site) use (&$bar) {
            $bar->advance();
            if ($site[0] === 'id') {
                return ['id', 'name', 'org_name', 'domain', 'country', 'type', 'category', 'popularity_index'];
            }

            if ($bar->getProgress() % 300 === 0) {
                sleep(2);
            }

            $checkSite = Site::where('domain', $site[2])->first();

            if ($checkSite && $checkSite->org_name && ! $this->isNotOrgname($checkSite->org_name)) {
                return $checkSite;
            }

            $siteData = [
                'csv_id' => $site[0],
                'name' => $site[1],
                'org_name' => $this->getOrganizationName($site[2]),
                'domain' => preg_replace('/^www\./', '', $site[2]),
                'country' => $site[3],
                'type' => $site[4],
                'category' => $site[5],
                'popularity_index' => $site[6],
            ];

            Site::updateOrCreate(['domain' => $siteData['domain']], $siteData);

            return $siteData;
        });

        $bar->finish();
        $this->info("\nCopy to sites_converted.csv...");
        $this->call('generate:sites');
        $this->info("\nSites converted successfully!");
    }

    public function isNotOrgname($orgName)
    {
        if (empty($orgName)) {
            return true;
        }

        $orgName = strtolower($orgName);

        if (strlen($orgName) > 200) {
            return true;
        }

        if (preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/', $orgName)) {
            return true;
        }

        $orgNameText = [
            'sorry',
            'unfortunately',
            'the organization',
            'no organization',
            'domain',
            'not found',
        ];

        foreach ($orgNameText as $text) {
            if (strpos($orgName, $text) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getOrganizationName($domain)
    {
        return Http::withToken(config('services.openai.secret'))
            ->post(config('services.openai.endpoint'),
                [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an organization finder assistant based from EU, skilled in find the right organization name by domain, just give you a domain then you will find and provide the exact domain's owner organization name, if you don't know the answer then just return the 'Not found' text. For example give you domain 'dn.no' or 'https://dn.no' or 'https://www.dn.no' then the answer is 'Dagens Næringsliv', remember just give the organization name not the domain itself.",
                        ],
                        [
                            'role' => 'user',
                            'content' => $domain,
                        ],
                    ],
                ])->json('choices.0.message.content');
    }
}
