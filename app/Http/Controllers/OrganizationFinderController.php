<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrganizationFinderController extends Controller
{
    public function index()
    {
        return view('display');
    }

    public function show(Request $request )
    {
        $request->validate([
            'domain' => 'required'
        ]);

        return view('display', [
            'organizationName' => $this->getOrganizationName($request->domain),
            'domain' => $request->domain ?? 'Unknown'
        ]);
    }

    private function getOrganizationName($domain)
    {
        return Http::withToken(config('services.openai.secret'))
            ->post(config('services.openai.endpoint'),
                [
                    "model" => "gpt-3.5-turbo",
                    "messages" => [
                        [
                            "role" => "system",
                            "content" => "You are an organization finder assistant, skilled in find the right organization name by domain, subdomain or web URL, just give you a domain, subdomain or a website URL then you will find and provide the exact domain own organization name, if you don't know the answer then just return `Not found` text. For example give you domain 'dn.no' or 'https://dn.no' or 'https://www.dn.no' then the answer is `Dagens Næringsliv`"
                        ],
                        [
                            "role" => "user",
                            "content" => $domain
                        ]
                    ]
                ])->json('choices.0.message.content');
    }
}
