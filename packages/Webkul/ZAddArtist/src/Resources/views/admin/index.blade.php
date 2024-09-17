<x-admin::layouts>

    <!-- Title of the page -->
    <x-slot:title>
        Package ZAddArtist
    </x-slot>

    <!-- Page Content -->
    <div class="page-content">
        <h1>Gestion des artistes</h1>
    </div>

    <div class="page-content">
        <a href="{{ route('admin.zaddartist.create') }}">Ajouter un nouvel artiste</a>
    </div>
</x-admin::layouts>