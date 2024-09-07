<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    {!! view_render_event('bagisto.shop.categories.view.description.before') !!}

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
    Artistes
    </div>

    {!! view_render_event('bagisto.shop.categories.view.description.after') !!}

    <div class="container px-[60px] max-lg:px-8 max-md:px-4">
                <div class="flex items-start gap-10 max-lg:gap-5 md:mt-10">
                    <!-- Product Listing Filters -->
                    @include('shop::categories.filters')
                    <div class="flex-1">
                    @foreach($artistes as $art)
                        <a href="{{ route('store.artistes.artiste-view', ['slug' => $art->slug, 'artistes' => $art->id]) }}">
                            {{$art->name}}
                        </a>
                    @endforeach
                    </div>
                </div>
    </div>



    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>