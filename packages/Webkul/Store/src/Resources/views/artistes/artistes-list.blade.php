<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Artistes
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <p class="text-xl">ARTISTES</p>
    </div>

    <div class="container px-[60px] max-lg:px-8 max-md:px-4">
        <!-- Sort Dropdown -->
        <div class="mb-8 flex items-center gap-4 md:mt-10">
            <x-shop::dropdown position="bottom-left">
                <x-slot:toggle>
                    <button class="flex w-full max-w-[200px] cursor-pointer items-center justify-between gap-4 rounded-lg border border-black p-3.5 text-base transition-all hover:border-gray-400 focus:border-gray-400">
                        A → Z
                        <span class="icon-arrow-down text-2xl" role="presentation"></span>
                    </button>
                </x-slot>
            
                <x-slot:menu>
                    <x-shop::dropdown.menu.item onclick="window.artistesSortComponent && window.artistesSortComponent.changeSortOrder('asc')">
                        A → Z
                    </x-shop::dropdown.menu.item>
                    <x-shop::dropdown.menu.item onclick="window.artistesSortComponent && window.artistesSortComponent.changeSortOrder('desc')">
                        Z → A
                    </x-shop::dropdown.menu.item>
                </x-slot:menu>
            </x-shop::dropdown>
        </div>

        <div class="flex items-start gap-10 max-lg:gap-5">
            <div class="max-md:mt-5">
                <div class="grid grid-cols-4 gap-8 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                    <artistes-index></artistes-index> 
                </div>  
            </div> 
        </div>
    </div>
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>