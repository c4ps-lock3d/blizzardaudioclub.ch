{!! RecaptchaV3::initJs() !!}
<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <div class="container mt-8 max-1180:px-5 max-md:mt-6 max-md:px-4">
        <!-- Form Container -->
		<div id="backgroundCard" class="m-auto w-full max-w-[870px] rounded-xl border border-black p-16 px-[90px] max-md:px-8 max-md:py-8 max-sm:border-none">
			<h1 class="text-4xl max-md:text-3xl max-sm:text-xl">
                @lang('shop::app.home.contact.title')
            </h1>

			<p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                @lang('shop::app.home.contact.about')
            </p>

            <div class="mt-10 rounded max-sm:mt-8">
                <!-- Contact Form -->
                <x-shop::form :action="route('shop.home.contact_us.send_mail')">
                    <x-shop::form.control-group>
                        {!! RecaptchaV3::field('register') !!}
                        <x-shop::form.control-group.control
                            type="submit"
                            value="Register"
                            rules="required|recaptchav3:register,1"
                        />
                    </x-shop::form.control-group>
                    <!-- Name -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.home.contact.name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="name"
                            rules="required"
                            :value="old('name')"
                            :label="trans('shop::app.home.contact.name')"
                            :placeholder="trans('shop::app.home.contact.name')"
                            :aria-label="trans('shop::app.home.contact.name')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="name" />
                    </x-shop::form.control-group>

                    <!-- Email -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.home.contact.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="email"
                            rules="required|email"
                            :value="old('email')"
                            :label="trans('shop::app.home.contact.email')"
                            :placeholder="trans('shop::app.home.contact.email')"
                            :aria-label="trans('shop::app.home.contact.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    <!-- Message -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            Votre message
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="textarea"
                            class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="message"
                            rules="required"
                            :label="trans('shop::app.home.contact.message')"
                            placeholder="Votre message"
                            :aria-label="trans('shop::app.home.contact.message')"
                            aria-required="true"
                            rows="10"
                        />

                        <x-shop::form.control-group.error control-name="message" />
                    </x-shop::form.control-group>

                    <!-- Recaptcha -->
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <div class="mb-5 flex">
                            {!! Captcha::render() !!}
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="mt-8 flex flex-wrap items-center gap-9 max-sm:justify-center max-sm:text-center">
                        <button
                            class="primary-button m-0 mx-auto block w-full max-w-[374px] rounded-2xl px-11 py-4 text-center text-base max-md:max-w-full max-md:rounded-lg max-md:py-3 max-sm:py-1.5 ltr:ml-0 rtl:mr-0"
                            type="submit"
                        >
                            @lang('shop::app.home.contact.submit')
                        </button>
                    </div>
                </x-shop::form>
            </div>
		</div>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>