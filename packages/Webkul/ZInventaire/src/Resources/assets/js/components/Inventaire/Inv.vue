<script>
    export default {
        data() {
            return {
                products: [],
                sortKey: 'sku',
                sortOrders: {
                    name: 1,
                    sku: 1,
                    qty: 1,
                    price: 1,
                    format: 1,
                },
                modifiedProducts: {}, // Track les produits modifiés avec leurs nouvelles quantités
                isSaving: false,
            };
        },
        mounted() {
            this.fetchPosts().then(() => {
            this.sortBy('sku'); // Tri initial par SKU
        });
        },
        methods: {
            fetchPosts() {
                axios
                    .get("/admin/zinventaire/products/inv")
                    .then((response) => (this.products = response.data.data))
                    .catch((error) => console.log(error));
            },
            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] *= -1;
                
                this.products.sort((a, b) => {
                    let aValue = a[key];
                    let bValue = b[key];
                    
                    if (key === 'price') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                    } else if (key === 'format') {
                        aValue = String(aValue || '').toLowerCase();
                        bValue = String(bValue || '').toLowerCase();
                    } else if (typeof aValue === 'string') {
                        aValue = aValue.toLowerCase();
                        bValue = bValue.toLowerCase();
                    }
                    
                    return (aValue > bValue ? 1 : -1) * this.sortOrders[key];
                });
            },
            handleQtyChange(event, product) {
                const newQty = parseInt(event.target.value) || 0;
                if (product && product.id) {
                    // Vue 3 : assignation directe sans this.$set()
                    this.modifiedProducts[String(product.id)] = newQty;
                    console.log('Modified:', this.modifiedProducts);
                }
            },
            async saveAllInventories() {
                if (Object.keys(this.modifiedProducts).length === 0) {
                    alert('Aucune modification à enregistrer');
                    return;
                }

                this.isSaving = true;
                try {
                    // Construire le payload avec tous les changements
                    const updatedInventories = Object.keys(this.modifiedProducts).map(productId => ({
                        id: parseInt(productId),
                        qty: this.modifiedProducts[productId],
                    }));

                    await axios.put('/admin/zinventaire/products/edit', {
                        inventories: updatedInventories,
                    });

                    // Mettre à jour les quantités locales après succès
                    updatedInventories.forEach(item => {
                        const product = this.products.find(p => String(p.id) === String(item.id));
                        if (product) {
                            product.qty = item.qty;
                        }
                    });

                    // Vider le tracker des modifications
                    this.modifiedProducts = {};
                    alert('Inventaire enregistré avec succès');
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'enregistrement');
                } finally {
                    this.isSaving = false;
                }
            },
            hasChanges() {
                return Object.keys(this.modifiedProducts).length > 0;
            }
        },
        computed: {
            sortedProducts() {
                return this.products;
            },
            totalProducts() {
                return this.products.length;
            },
            changedCount() {
                return Object.keys(this.modifiedProducts).length;
            },
        }
    };
</script>


<template>
    <!-- En-tête avec total et bouton -->
    <div class="sticky top-0 z-10 bg-white flex justify-between items-center mb-4 px-6 py-4 shadow-md rounded-t-lg">
        <h1 class="text-xl font-medium">Inventaire</h1>
        <div class="flex items-center gap-4">
            <div class="text-gray-600">
                Total: {{ totalProducts }} produits
                <span v-if="changedCount > 0" class="ml-3 text-blue-600 font-medium">
                    {{ changedCount }} modifié(s)
                </span>
            </div>
            <button 
                @click="saveAllInventories"
                :disabled="!hasChanges() || isSaving"
                :class="[
                    'px-4 py-2 rounded font-medium transition',
                    hasChanges() && !isSaving 
                        ? 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer' 
                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]">
                {{ isSaving ? 'Enregistrement...' : 'Enregistrer tous les changements' }}
            </button>
        </div>
    </div>
    <!-- Tableau stylisé -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Image
                    </th>
                    <th @click="sortBy('name')" 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Nom
                        <span class="arrow ml-2" :class="sortOrders.name > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                    <th @click="sortBy('sku')" 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        SKU
                        <span class="arrow ml-2" :class="sortOrders.sku > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                    <th @click="sortBy('qty')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Quantité
                        <span class="arrow ml-2" :class="sortOrders.qty > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                    <th @click="sortBy('price')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Prix (CHF)
                        <span class="arrow ml-2" :class="sortOrders.price > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                    <th @click="sortBy('format')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Format
                        <span class="arrow ml-2" :class="sortOrders.format > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in sortedProducts" class="hover:bg-gray-60">
                    <td class="px-6 py-2">
                        <img :src="product.base_image.small_image_url" width="55" class="rounded-sm">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.sku }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input
                            type="number"
                            @input="handleQtyChange($event, product)"
                            :value="modifiedProducts[String(product.id)] !== undefined ? modifiedProducts[String(product.id)] : product.qty"
                            class="border px-2 py-1 rounded w-20"
                            min="0"
                        />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Math.round(product.price) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.format }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>