<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Artistes
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <p class="text-xl">ARTISTESS</p>
    </div>

    <div class="container px-[60px] max-lg:px-8 max-md:px-4">
        <div class="flex items-start gap-10 max-lg:gap-5 md:mt-10">
            <div class="max-md:mt-5">
                <div class="grid grid-cols-4 gap-8 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                    <artistes-index></artistes-index> 
                </div>  
            </div> 
        </div>
    </div>
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>