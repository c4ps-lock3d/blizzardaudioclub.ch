
<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        Ajouter un artiste
    </x-slot>

    <!-- Page Content -->
    <div class="page-content">
        <h1 class="mb-4 block text-gray-700 text-lg font-bold mb-2">Ajouter un artiste</h1>
        <div class="w-full max-w-lg">
            <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nom</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="name" id="name">
                    @error("name")
                        {{ $message }}
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Description</label>
                    <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="content" id="content"></textarea>
                    @error("content")
                        {{ $message }}
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="facebook">Lien Facebook</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="facebook" id="facebook">
                    @error("facebook")
                        {{ $message }}
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="instagram">Lien Instagram</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="instagram" id="instagram">
                    @error("instagram")
                        {{ $message }}
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tiktok">Lien Tiktok</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="tiktok" id="tiktok">
                    @error("tiktok")
                        {{ $message }}
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="soundclound">Lien Soundclound</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="soundclound" id="soundclound">
                        @error("soundclound")
                            {{ $message }}
                        @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="youtube">Lien Youtube</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="youtube" id="youtube">
                    @error("youtube")
                        {{ $message }}
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Vignette</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="file" name="image" id="image">
                        @error("image")
                            {{ $message }}
                        @enderror
                </div>



                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold rounded py-2 px-2">Valider</button>
            </form>
        </div>
</div>
@push('styles')
    @bagistoVite([
      'src/Resources/assets/css/app.css',
      'src/Resources/assets/js/app.js'
    ], 'zaddartist')
@endpush
</x-admin::layouts>