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
                price: 1
            }
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
                } else if (typeof aValue === 'string') {
                    aValue = aValue.toLowerCase();
                    bValue = bValue.toLowerCase();
                }
                
                return (aValue > bValue ? 1 : -1) * this.sortOrders[key];
            });
        }
    },
    computed: {
        sortedProducts() {
            return this.products;
        }
    }
};

</script>


<template>
   <table class="border-2 border-black w-full">
    <thead>
            <tr>
                <th @click="sortBy('name')" :class="{ active: sortKey === 'name' }">
                    Nom
                    <span class="arrow" :class="sortOrders.name > 0 ? 'asc' : 'dsc'"></span>
                </th>
                <th @click="sortBy('sku')" :class="{ active: sortKey === 'sku' }">
                    SKU
                    <span class="arrow" :class="sortOrders.sku > 0 ? 'asc' : 'dsc'"></span>
                </th>
                <th @click="sortBy('qty')" :class="{ active: sortKey === 'qty' }">
                    Quantité
                    <span class="arrow" :class="sortOrders.qty > 0 ? 'asc' : 'dsc'"></span>
                </th>
                <th @click="sortBy('price')" :class="{ active: sortKey === 'price' }">
                    Prix
                    <span class="arrow" :class="sortOrders.price > 0 ? 'asc' : 'dsc'"></span>
                </th>
            </tr>
        </thead>
        <tbody>   
            <tr v-for="product in sortedProducts">
                <td>{{ product.name }}</td>
                <td>{{ product.sku }}</td>
                <td>{{ product.qty }}</td>
                <td>{{ Math.round(product.price) }}</td>
            </tr>
        </tbody>
  </table>
</template>

<style>
th {
    cursor: pointer;
    background-color: grey;
}
th, td, tr {
    border: 1px solid black;
    padding: 3px;
}
table {
    border: 2px solid black;
}
.arrow {
    display: inline-block;
    width: 0;
    height: 0;
    margin-left: 5px;
    vertical-align: middle;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
}
.arrow.asc {
    border-bottom: 4px solid #000000;
}
.arrow.dsc {
    border-top: 4px solid #000000;
}
</style>