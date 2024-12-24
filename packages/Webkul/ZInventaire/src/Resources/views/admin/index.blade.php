<x-admin::layouts>

    <!-- Title of the page -->
    <x-slot:title>
        Package ZInventaire
    </x-slot>

    <!-- Page Content -->
    <div class="page-content">
        <inventaire-inv></inventaire-inv> 
    </div>
@push('styles')
    @bagistoVite([
      'src/Resources/assets/css/app.css',
      'src/Resources/assets/js/app.js'
    ], 'zinventaire')
@endpush
</x-admin::layouts>