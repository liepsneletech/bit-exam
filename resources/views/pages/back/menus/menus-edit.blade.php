<x-back-layout>
    <div class="bg-gray-100 min-h-screen">
        <div class="container">
            <form method="post" action="{{ route('admin-menus-update', $menu) }}" class="w-1/2 mx-auto">
                @csrf
                @method('put')

                <h1 class="primary-heading mb-6 mt-24">Virtuvės redagavimas</h1>

                <!-- Success messages -->
                @if (session()->has('success'))
                    <p class="text-white bg-green-500 rounded-lg py-1 px-4 text-sm mt-3 mb-5">
                        {{ Session::get('success') }}
                    </p>
                @endif

                <x-form.label for="title" :value="__('Virtuvės pavadinimas')" />
                <x-form.input id="title" class="block mt-1 w-full mb-2" type="text" name="title"
                    value="{{ $menu->title }}" autofocus />

                @error('title')
                    <p class="text-white bg-red-500 rounded-lg py-1 px-4 text-sm mt-3 mb-5">
                        {{ $message }}
                    </p>
                @enderror

                <button class="mt-4 mb-10 secondary-btn" type="submit">
                    Pridėti
                </button>
            </form>
        </div>
    </div>
</x-back-layout>
