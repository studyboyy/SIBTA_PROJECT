<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-9xl font-extrabold text-red-600 tracking-widest">403</h1>
        <div class="bg-red-500 text-white px-2 text-sm rounded rotate-12 absolute">
            Access Denied
        </div>
        <h2 class="text-2xl font-bold mt-4 text-gray-800">Oops! Access Forbidden</h2>
        <p class="text-gray-600 mt-2 mb-6">Sorry, you don't have permission to access this resource.</p>
        <a href="{{ url('/') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            Return Home
        </a>
    </div>
</body>
</html>
