<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $organizationName ?? 'Organization finder tool' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full p-6">
    <form class="w-full mx-auto max-w-lg" method="POST" action="{{ route('organization.show') }}" >
        @csrf
        <div class="flex items-center border-b border-teal-500 py-2">
            <input autocomplete="off" name="domain" value="{{ $domain ?? '' }}" class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none" type="text" placeholder="Domain or Web URL" aria-label="Domain">
            <button class="flex-shrink-0 bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 rounded" type="submit">
                Find the Organization
            </button>
        </div>
        @error('domain')
        <div class="text-red-500">{{ $message }}</div>
        @enderror
        <div class="mt-5 px-2 font-medium">{{ $organizationName ?? '' }}</div>
    </form>
</body>
</html>
