{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'channel_id' => core()->getCurrentChannel()->id,
    ]);
@endphp

<footer class="mt-10 bg-lightOrange max-sm:mt-10">
    <div class="flex justify-between p-[40px] max-1060:flex-col-reverse max-md:gap-5 max-md:p-8 max-sm:px-4 max-sm:py-5">
        <!-- For Desktop View -->
        <div class="">
            <ul>
                <img
                    src="{{ core()->getCurrentChannel()->logo_url }}"
                    width="131"
                    height="29"
                    alt="{{ config('app.name') }}"   
                    class="mb-6"   
                >
                <span class="fa-stack mr-2">
                    <a href="https://blizzardaudioclub.bandcamp.com/" target="_blank" class="fab fa-bandcamp fa-2x fa-inverse"></a>
                </span>
                <span class="fa-stack mr-2">
                    <a href="https://instagram.com/blizzardaudioclub" target="_blank" class="fab fa-instagram fa-2x fa-inverse"></a>
                </span>
                <span class="fa-stack mr-4">
                    <a href="https://soundcloud.com/blizzardaudioclub" target="_blank" class="fab fa-soundcloud fa-2x fa-inverse"></a>
                </span>
                <span class="fa-stack">
                    <a href="https://youtube.com/c/blizzardaudioclub" target="_blank" class="fab fa-youtube fa-2x fa-inverse"></a>
                </span>
            </ul>
        </div>
        <div class="flex flex-wrap ml-8 items-start gap-24 max-1180:gap-6 max-1060:hidden">
            @if ($customization?->options)
                @foreach ($customization->options as $footerLinkSection)
                    <ul class="grid gap-5 text-sm">
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <li>
                                <a href="{{ $link['url'] }}">
                                    {{ $link['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>

        <!-- For Mobile view -->
        <x-shop::accordion
            :is-active="false"
            class="hidden !w-full rounded-xl !border-2 !border-[#e9decc] max-1060:block max-sm:rounded-lg"
        >
            <x-slot:header class="rounded-lg bg-[#F1EADF] text-[#DCDCDC] font-medium max-md:p-2.5 max-sm:px-3 max-sm:py-2 max-sm:text-sm">
                @lang('shop::app.components.layouts.footer.footer-content')
            </x-slot>

            <x-slot:content class="flex justify-between !bg-transparent !p-4">
                @if ($customization?->options)
                    @foreach ($customization->options as $footerLinkSection)
                        <ul class="grid gap-5 text-sm">
                            @php
                                usort($footerLinkSection, function ($a, $b) {
                                    return $a['sort_order'] - $b['sort_order'];
                                });
                            @endphp

                            @foreach ($footerLinkSection as $link)
                                <li>
                                    <a
                                        href="{{ $link['url'] }}"
                                        class="text-sm font-medium max-sm:text-xs">
                                        {{ $link['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                @endif
            </x-slot>
        </x-shop::accordion>
        
        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}
        <div class="">
            <ul class="grid gap-5 text-sm">
                <li>
                    <p
                        class="max-w-[350px] text-xl italic text-navyBlue max-md:text-2xl max-sm:text-lg"
                        role="heading"
                        aria-level="2"
                        >
                        CONTACT
                    </p>
                    <p class="text-sm mt-2">
                        <b>Blizzard Audio Club</b><br>
                        Joux-Pélichet 3<br>
                        2400 Le Locle<br>
                        Suisse (CH)
                    </p>
                </li>
            </ul>
        </div>
        <!-- News Letter subscription -->
        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
            <div class="">
                <p
                    class="max-w-[350px] text-xl italic text-navyBlue max-md:text-2xl max-sm:text-lg"
                    role="heading"
                    aria-level="2"
                >
                    NEWSLETTER
                </p>

                <p class="text-xs">
                    
                </p>

                <div>
                    <x-shop::form
                        :action="route('shop.subscription.store')"
                        class="mt-2.5 rounded max-sm:mt-0 mr-8 max-1060:mr-0"
                    >
                        <div class="relative w-[300px]">
                            <x-shop::form.control-group.control
                                type="email"
                                id="formNewsletter"
                                class="block w-[300px] max-w-full rounded-lg border-2 border-[#e9decc] px-3 py-2 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-0 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="email"
                                rules="required|email"
                                label="Email"
                                :aria-label="trans('shop::app.components.layouts.footer.email')"
                                placeholder="email@example.com"
                            />
    
                            <x-shop::form.control-group.error control-name="email" />
    
                            <button
                                type="submit"
                                class="absolute top-1.5 flex w-max items-center rounded-lg bg-white px-2 py-1.5 font-medium hover:bg-zinc-100 max-md:top-1 max-md:px-5 max-md:text-xs max-sm:mt-0 max-sm:rounded-lg max-sm:px-4 max-sm:py-2 ltr:right-2 rtl:left-2"
                            >
                                @lang('shop::app.components.layouts.footer.subscribe')
                            </button>
                        </div>
                    </x-shop::form>
                </div>
            </div>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
    </div>

    <div class="flex justify-center px-[60px] py-2.5 max-md:justify-center max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}  
        <p class="text-sm text-zinc-600 max-md:text-center">
            © 2019 - {{date('Y')}} Blizzard Audio Club. Tous droits réservés.
        </p>

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
