@if ($product->type == 'bundle')
    {!! view_render_event('bagisto.shop.products.view.bundle-options.before', ['product' => $product]) !!}

    <v-product-bundle-options :errors="errors"></v-product-bundle-options>

    {!! view_render_event('bagisto.shop.products.view.bundle-options.after', ['product' => $product]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-product-bundle-options-template"
        >
            <div class="mt-8 max-sm:mt-0">
                <v-product-bundle-option-item
                    v-for="(option, index) in options"
                    :option="option"
                    :errors="errors"
                    :key="index"
                    @onProductSelected="productSelected(option, $event)"
                >
                </v-product-bundle-option-item>

                <div class="mb-2.5 mt-5 flex items-center justify-between">
                    <p class="text-sm">
                        @lang('shop::app.products.view.type.bundle.total-amount')
                    </p>

                    <p class="text-lg font-medium max-sm:text-sm">
                        @{{ formattedTotalPrice }}
                    </p>
                </div>

                <!--<ul class="grid gap-2.5 text-base max-sm:text-sm">
                    <li v-for="option in options">
                        <span class="mb-1.5 inline-block max-sm:mb-0">
                            @{{ option.label }}
                        </span>

                        <template v-for="product in option.products">
                            <div
                                class="text-zinc-500"
                                :key="product.id"
                                v-if="product.is_default"
                            >
                                @{{ product.qty + ' x ' + product.name }}
                            </div>
                        </template>
                    </li>
                </ul>-->
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-product-bundle-option-item-template"
        >
            <div class="mt-4 border-b border-zinc-400 pb-2 max-sm:mt-4 max-sm:pb-0">
                <x-shop::form.control-group>
                    <!-- Dropdown Options Container -->
                    <x-shop::form.control-group.label
                        class="!mt-0 max-sm:!mb-2.5"
                    >
                      <!--  @{{ option.label }}-->
                    </x-shop::form.control-group.label>

                    <template v-if="option.type == 'select'">
                        <x-shop::form.control-group.control
                            type="select"
                            ::name="'bundle_options[' + option.id + '][]'"
                            ::rules="{'required': Boolean(option.is_required)}"
                            v-model="selectedProduct"
                            ::label="option.label"
                        >
                            <option
                                value="0"
                                v-if="! Boolean(option.is_required)"
                            >
                                @lang('shop::app.products.view.type.bundle.none')
                            </option>

                            <option
                                v-for="product in option.products"
                                :value="product.id"
                            >
                                @{{ product.name + ' + ' + product.price.final.formatted_price }}
                            </option>
                        </x-shop::form.control-group.control>
                    </template>
                    
                    <template v-if="option.type == 'radio'">
                        <div class="grid gap-2 max-sm:gap-1">
                            <!-- None radio option if option is not required -->
                            <div
                                class="flex select-none gap-x-4"
                                v-if="! Boolean(option.is_required)"
                            >
                                <x-shop::form.control-group.control
                                    type="radio"
                                    ::name="'bundle_options[' + option.id + '][]'"
                                    ::for="'bundle_options[' + option.id + '][' + index + ']'"
                                    ::id="'bundle_options[' + option.id + '][' + index + ']'"
                                    value="0"
                                    v-model="selectedProduct"
                                    ::rules="{'required': Boolean(option.is_required)}"
                                    ::label="option.label"
                                />

                                <label
                                    class="cursor-pointer text-zinc-500 max-sm:text-sm"
                                    :for="'bundle_options[' + option.id + '][' + index + ']'"
                                >
                                    @lang('shop::app.products.view.type.bundle.none')
                                </label>
                            </div>

                            <!-- Options -->
                            <div
                                class="flex select-none items-center justify-between gap-x-4 max-sm:gap-x-1.5"
                                v-for="(product, index) in option.products"
                            >
                                <div class="flex select-none items-center gap-x-4 max-sm:gap-x-1.5">
                                    <x-shop::form.control-group.control
                                        type="radio"
                                        ::name="'bundle_options[' + option.id + '][]'"
                                        ::for="'bundle_options[' + option.id + '][' + index + ']'"
                                        ::id="'bundle_options[' + option.id + '][' + index + ']'"
                                        ::value="product.id"
                                        v-model="selectedProduct"
                                        ::rules="{'required': Boolean(option.is_required)}"
                                        ::label="option.label"
                                    />

                                    <label
                                        class="cursor-pointer text-zinc-500 max-sm:text-sm"
                                        :for="'bundle_options[' + option.id + '][' + index + ']'"
                                    >
                                        @{{ 'Format ' +product.format + ' -' }}
                                        <span v-if="product.format === 'vinyle'"></span>

                                        <span class="text-[#DCDCDC] text-xl">
                                            @{{ ' ' + product.price.final.formatted_price }}
                                        </span>
                                    </label>
                                </div>

                                <div
                                    class="flex select-none items-center justify-end"
                                    style="width: auto; min-height: 2.625rem;"
                                >
                                    <x-shop::quantity-changer
                                        v-show="selectedProduct == product.id && !isProductDownloadable"
                                        ::name="'bundle_option_qty[' + option?.id + ']'"
                                        ::value="productQty"
                                        class="gap-x-4 rounded-xl !border-zinc-200 px-4 py-1.5"
                                        @change="qtyUpdated($event)"
                                    />
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-if="option.type == 'multiselect'">
                        <x-shop::form.control-group.control
                            type="multiselect"
                            ::name="'bundle_options[' + option.id + '][]'"
                            ::rules="{'required': Boolean(option.is_required)}"
                            v-model="selectedProduct"
                            ::label="option.label"
                        >
                            <option
                                value="0"
                                v-if="! Boolean(option.is_required)"
                            >
                                @lang('shop::app.products.view.type.bundle.none')
                            </option>

                            <option
                                v-for="product in option.products"
                                :value="product.id"
                                :selected="value && value.includes(product.id)"
                            >
                                @{{ product.name + ' + ' + product.price.final.formatted_price }}
                            </option>
                        </x-shop::form.control-group.control>
                    </template>

                    <template v-if="option.type == 'checkbox'">
                        <div class="grid gap-2">
                        <!-- Options -->
                            <div
                                class="flex select-none items-center justify-between gap-x-4 max-sm:gap-x-1.5"
                                v-for="(product, index) in option.products"
                            >
                                <div class="flex select-none items-center gap-x-4 max-sm:gap-x-1.5">
                                    <x-shop::form.control-group.control
                                        type="checkbox"
                                        ::name="'bundle_options[' + option.id + '][]'"
                                        ::for="'bundle_options[' + option.id + '][' + index + ']'"
                                        ::id="'bundle_options[' + option.id + '][' + index + ']'"
                                        ::value="product.id"
                                        v-model="selectedProduct"
                                        ::rules="{'required': Boolean(option.is_required)}"
                                        ::label="option.label"
                                    />

                                    <label
                                        class="cursor-pointer text-zinc-500 max-sm:text-sm"
                                        :for="'bundle_options[' + option.id + '][' + index + ']'"
                                    >
                                        @{{ 'Format ' +product.format + ' -' }}

                                        <span class="text-[#FADA00]">
                                            @{{ ' ' + product.price.final.formatted_price }}
                                        </span>
                                    </label>
                                </div>

                                <div
                                    class="flex select-none items-center justify-end"
                                    style="width: auto; min-height: 2.625rem;"
                                >
                                    <x-shop::quantity-changer
                                        v-show="selectedProduct == product.id && ['select', 'checkbox'].includes(option.type) && !isProductDownloadable"
                                        ::name="'bundle_option_qty[' + option?.id + ']'"
                                        ::value="productQty"
                                        class="gap-x-4 rounded-xl !border-zinc-200 px-4 py-1.5"
                                        @change="qtyUpdated($event)"
                                    />
                                </div>
                            </div>
                        </div>
                    </template>

                    <x-shop::form.control-group.error ::name="'bundle_options[' + option.id + '][]'" />
                </x-shop::form.control-group>
             
            </div>
        </script>

        <script type="module">
            app.component('v-product-bundle-options', {
                template: '#v-product-bundle-options-template',

                props: ['errors'],

                data: function() {
                    return {
                        config: @json(app('Webkul\Product\Helpers\BundleOption')->getBundleConfig($product)),

                        options: [],

                    }
                },

                computed: {
                    formattedTotalPrice: function() {
                        var total = 0;

                        for (var key in this.options) {
                            for (var key1 in this.options[key].products) {
                                if (! this.options[key].products[key1].is_default)
                                    continue;

                                total += this.options[key].products[key1].qty * this.options[key].products[key1].price.final.price;
                            }
                        }

                        return this.$shop.formatPrice(total);
                    }
                },

                created: function() {
                    for (var key in this.config.options) {
                        this.options.push(this.config.options[key]);
                    }
                },

                methods: {
                    productSelected: function(option, value) {
                        var selectedProductIds = Array.isArray(value) ? value : [value];

                        for (var key in option.products) {
                            option.products[key].is_default = selectedProductIds.indexOf(option.products[key].id) > -1 ? 1 : 0;
                        }

                        // Emit event with images of selected products
                        var selectedImages = [];
                        selectedProductIds.forEach(selectedId => {
                            var product = option.products.find(p => p.id == selectedId);
                            if (product && product.images) {
                                selectedImages = selectedImages.concat(product.images);
                            }
                        });

                        this.$emitter.emit('bundle-product-selected', {
                            images: selectedImages,
                            option: option
                        });
                    }
                }
            });

            app.component('v-product-bundle-option-item', {
                template: '#v-product-bundle-option-item-template',

                props: ['option', 'errors'],

                data: function() {
                    return {
                        selectedProduct: (this.option.type == 'checkbox' || this.option.type == 'multiselect')  ? [] : null,
                    };
                },

                computed: {
                    productQty: function() {
                        let qty = 0;

                        this.option.products.forEach((product, key) => {
                            if (this.selectedProduct == product.id) {
                                qty =  this.option.products[key].qty;
                            }
                        });

                        return qty;
                    },

                    isProductDownloadable: function() {
                        // Check if ALL products in this option are downloadable
                        const allDownloadable = this.option.products.every(product => product.type === 'downloadable');
                        if (allDownloadable) {
                            return true;
                        }

                        // Handle both single values and arrays
                        const selectedIds = Array.isArray(this.selectedProduct) ? this.selectedProduct : [this.selectedProduct];
                        
                        // Filter out empty values
                        const validIds = selectedIds.filter(id => id && id !== '0');
                        
                        if (validIds.length === 0) {
                            return false;
                        }
                        
                        // Check if ANY of the selected products are downloadable
                        return validIds.some(selectedId => {
                            const product = this.option.products.find(data => data.id == selectedId);
                            return product && product.type === 'downloadable';
                        });
                    }
                },

                watch: {
                    selectedProduct: function (value) {
                        this.$emit('onProductSelected', value);
                    }
                },

                created: function() {
                    for (var key in this.option.products) {
                        if (! this.option.products[key].is_default)
                            continue;

                        if (this.option.type == 'checkbox' || this.option.type == 'multiselect') {
                            this.selectedProduct.push(this.option.products[key].id)
                        } else {
                            this.selectedProduct = this.option.products[key].id
                        }
                    }
                },

                methods: {
                    qtyUpdated: function(qty) {
                        if (! this.option.products.find(data => data.id == this.selectedProduct)) {
                            return;
                        }

                        this.option.products.find(data => data.id == this.selectedProduct).qty = qty;
                    }
                }
            });
        </script>
    @endPushOnce
@endif
