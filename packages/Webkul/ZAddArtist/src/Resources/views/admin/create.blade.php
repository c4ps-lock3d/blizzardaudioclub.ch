<x-admin::layouts>

    <!-- Title of the page -->
    <x-slot:title>
        Package ZAddArtist
    </x-slot>

    <!-- Page Content -->
    <div class="page-content">
        <h1>Créer un artiste</h1>
    </div>
 
    <form action="" method="post" enctype="multipart/form-data">
        @csrf

        <label for="slug">slug</label>
        <input type="text" name="slug" id="slug"><br>
            @error("slug")
                {{ $message }}
            @enderror
        <label for="name">name</label>
        <input type="text" name="name" id="name"><br>
            @error("name")
                {{ $message }}
            @enderror
            
        <button type="submit">Valider</button>
    </form>
    </div>

</x-admin::layouts>