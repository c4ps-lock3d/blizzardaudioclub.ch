<script>
export default {
    data() {
        return {
            sortOrder: 'asc',
        };
    },
    computed: {
        sortLabel() {
            return this.sortOrder === 'asc' ? 'De A à Z' : 'De Z à A';
        }
    },
    mounted() {
        window.artistesSortDropdown = this;
    },
    methods: {
        changeSortOrder(order) {
            this.sortOrder = order;
            if (window.artistesSortComponent) {
                window.artistesSortComponent.changeSortOrder(order);
            }
        },
    },
};
</script>

<template>
    <div class="relative">
        <v-dropdown position="bottom-left">
            <template v-slot:toggle>
                <button class="flex w-full max-w-[200px] cursor-pointer items-center justify-between gap-4 rounded-lg border border-black p-3.5 text-base transition-all hover:border-gray-400 focus:border-gray-400">
                    {{ sortLabel }}
                    <span class="icon-arrow-down text-2xl" role="presentation"></span>
                </button>
            </template>

            <template v-slot:menu>
                <ul class="py-4">
                    <li class="px-4 py-2 cursor-pointer transition-colors hover:bg-[#1d2124] hover:text-white" :class="{'bg-[#1d2124] text-white': sortOrder === 'asc'}" @click="changeSortOrder('asc')">
                        De A à Z
                    </li>
                    <li class="px-4 py-2 cursor-pointer transition-colors hover:bg-[#1d2124] hover:text-white" :class="{'bg-[#1d2124] text-white': sortOrder === 'desc'}" @click="changeSortOrder('desc')">
                        De Z à A
                    </li>
                </ul>
            </template>
        </v-dropdown>
    </div>
</template>
