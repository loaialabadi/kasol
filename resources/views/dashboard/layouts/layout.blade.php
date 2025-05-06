<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Use Tailwind CSS from a CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<style>
    *{
        direction:rtl !important;
    }
    .main-wrapper{
     margin-right: 260px !important;   
     margin-left: 0px !important;   
    }
    .sidebar-wrapper{
        right:0px  !important;
        direction:rtl  !important;
    }
    .sidebar-wrapper *{
        direction:rtl  !important; 
    }
    .sidebar-wrapper .metismenu a{
        display:flex;
        direction: rtl !important;
  display: flex;
  justify-content: inherit;
    }
    [data-bs-theme="blue-theme"] body .sidebar-nav .metismenu a {
  color: #a7acb1;
  display: flex;
  justify-content: flex-start;
}

@media(max-width:767px){
    .form-control{
        width:100% !important;
    }
    .select2{
        width:100% !important;
    }
}
</style>
<body dir="rtl" class="antialiased">
    <div class="min-h-screen bg-gray-100">
        {{-- @include('layouts.navigation') --}}
        @include('dashboard.layouts.sidebar') <!-- Sidebar included here -->
        @include('dashboard.layouts.header') <!-- Sidebar included here -->

        <!-- Page Content -->
        <main>
            @yield('content') <!-- Child content will be injected here -->
        </main>
    </div>

    <!-- Include Footer -->
    @include('dashboard.layouts.footer')

    <!-- Alpine.js (optional, if required by Breeze) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js" defer></script>
</body>
</html>
