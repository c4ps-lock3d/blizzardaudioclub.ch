<script>
    export default {
        data() {
            return {
                products: [],
                sortKey: '',
                sortOrders: {
                    name: 1,
                    sku: 1,
                    qty: 1,
                    price: 1,
                    format: 1,
                },
                newQty: null,
            };
        },
        mounted() {
            this.fetchPosts();
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
                this.newQty = parseInt(event.target.value);
            },
            async saveEdit(product) {
                try {
                    await axios.put(`/admin/zinventaire/products/edit/${product.id}`,{
                        qty: this.newQty,
                    });
                    //this.fetchPosts(); // Rafraîchir les données après la mise à jour
                } catch (error) {
                    console.error('Erreur:', error);
                }
            }
        },
        computed: {
            sortedProducts() {
                return this.products;
            },
            totalProducts() {
                return this.products.length;
            },
        }
    };
</script>


<template>
    <!-- En-tête avec total -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-medium">Inventaire</h1>
        <div class="text-gray-600">
            Total: {{ totalProducts }} produits
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
                            @input="(e) => handleQtyChange(e, product)"
                            :value="product.qty"
                            @change="saveEdit(product)"
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