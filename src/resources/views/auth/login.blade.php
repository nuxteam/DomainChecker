<x-guest-layout>

    <div class="">

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">Sign in</h1>
            <p class="text-sm text-gray-500 mt-1">Access your domain monitor</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <input class="w-full p-3 mb-4 border rounded-lg focus:ring-2 focus:ring-black"
                   type="email" name="email" placeholder="Email">

            <input class="w-full p-3 mb-4 border rounded-lg focus:ring-2 focus:ring-black"
                   type="password" name="password" placeholder="Password">

            <button class="w-full bg-gray-800 hover:bg-black text-white p-2 rounded-lg transition">
                Sign in
            </button>

        </form>

        <!-- switch -->
        <div class="text-center mt-6 text-sm text-gray-800">
            No account?
            <a href="{{ route('register') }}" class="text-black font-medium hover:underline">
                Create one
            </a>
        </div>

    </div>

</x-guest-layout>