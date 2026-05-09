<x-app-layout>

<div class="max-w-5xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $domain->url }}
            </h1>
            <p class="text-sm text-gray-500">
                Method: {{ $domain->method }} | Timeout: {{ $domain->timeout }}s
            </p>
        </div>
    </div>

    <!-- Status card -->
    <div class="bg-white border rounded-xl p-5 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Current status</p>
                <p class="text-lg font-semibold">
                    @php
                        $last = $checks->first();
                    @endphp

                    @if($last && $last->is_up)
                        <span class="text-green-600">UP</span>
                    @else
                        <span class="text-red-600">DOWN</span>
                    @endif
                </p>
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-500">Last response</p>
                <p class="text-lg font-semibold">
                    {{ $last?->response_time ?? '-' }} ms
                </p>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-gray-900">Last checks</h2>
        </div>

        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 border-b">
                <tr>
                    <th class="p-3">Status</th>
                    <th>Code</th>
                    <th>Time</th>
                    <th>Error</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @foreach($checks as $check)
                    <tr class="border-b">
                        <td class="p-3">
                            @if($check->is_up)
                                <span class="text-green-600 font-medium">UP</span>
                            @else
                                <span class="text-red-600 font-medium">DOWN</span>
                            @endif
                        </td>

                        <td>{{ $check->status_code ?? '-' }}</td>
                        <td>{{ $check->response_time ?? '-' }} ms</td>
                        <td class="text-red-500">
                            {{ $check->error ?? '-' }}
                        </td>
                        <td class="text-gray-500">
                            {{ $check->created_at }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</x-app-layout>