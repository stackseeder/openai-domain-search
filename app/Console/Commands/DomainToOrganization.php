<?php

namespace App\Console\Commands;

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

            if ($bar->getProgress() % 500 === 0) {
                sleep(2);
            }

            return [
                'id' => $site[0],
                'name' => $site[1],
                'org_name' => $this->getOrganizationName($site[2]),
                'domain' => $site[2],
                'country' => $site[3],
                'type' => $site[4],
                'category' => $site[5],
                'popularity_index' => $site[6],
            ];
        });

        $sites->each(function ($site) use ($bar) {
            file_put_contents(database_path('data/sites_rows_updated.csv'), implode(',', $site).PHP_EOL, FILE_APPEND);

            $bar->advance();
        });

        $bar->finish();
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
                            'content' => "You are an organization finder assistant based from EU, skilled in find the right organization name by domain, just give you a domain then you will find and provide the exact domain's owner organization name, if you don't know the answer then just return the domain. For example give you domain 'dn.no' or 'https://dn.no' or 'https://www.dn.no' then the answer is Dagens Næringsliv, remember just give the organization name not the domain itself. Now let's start, please find the organization name based on the domain",
                        ],
                        [
                            'role' => 'user',
                            'content' => $domain,
                        ],
                    ],
                ])->json('choices.0.message.content');
    }
}
