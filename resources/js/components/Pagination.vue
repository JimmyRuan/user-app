<template>
    <div v-if="pagination.total > pagination.per_page" class="mt-8 flex justify-center items-center space-x-2">
        <!-- Previous Button -->
        <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="mx-2 px-4 py-2 bg-primary text-white rounded disabled:opacity-50 disabled:bg-gray-400"
        >
            Previous
        </button>

        <!-- First Page Button -->
        <button
            @click="changePage(1)"
            :class="pagination.current_page === 1 ? 'bg-primary-selected' : 'bg-primary'"
            class="px-4 py-2 text-white rounded disabled:opacity-50"
        >
            1
        </button>

        <!-- Ellipsis if current page is too far from first page -->
        <span v-if="pagination.current_page > adjacentPages + 2" class="px-2">...</span>

        <!-- Pages around the current page -->
        <button
            v-for="page in pagesToShow"
            :key="page"
            @click="changePage(page)"
            :class="pagination.current_page === page ? 'bg-primary-selected' : 'bg-primary'"
            class="px-4 py-2 text-white rounded"
        >
            {{ page }}
        </button>

        <!-- Ellipsis if current page is too far from last page -->
        <span v-if="pagination.current_page < pagination.last_page - adjacentPages - 1" class="px-2">...</span>

        <!-- Last Page Button -->
        <button
            @click="changePage(pagination.last_page)"
            :class="pagination.current_page === pagination.last_page ? 'bg-primary-selected' : 'bg-primary'"
            class="px-4 py-2 text-white rounded disabled:opacity-50"
        >
            {{ pagination.last_page }}
        </button>

        <!-- Next Button -->
        <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="mx-2 px-4 py-2 bg-primary text-white rounded disabled:opacity-50 disabled:bg-gray-400"
        >
            Next
        </button>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        pagination: {
            type: Object,
            required: true,
            default: () => ({
                total: 0,
                per_page: 10,
                current_page: 1,
                last_page: 1,
            }),
        },
        adjacentPages: {
            type: Number,
            default: 2, // Number of adjacent pages to show around the current page
        },
    },
    computed: {
        pagesToShow() {
            const pages = [];
            const { current_page, last_page } = this.pagination;
            const start = Math.max(current_page - this.adjacentPages, 2);
            const end = Math.min(current_page + this.adjacentPages, last_page - 1);

            for (let i = start; i < end; i++) {
                pages.push(i);
            }
            return pages;
        },
    },
    methods: {
        changePage(page) {
            if (page !== this.pagination.current_page && page > 0 && page <= this.pagination.last_page) {
                this.$emit('page-changed', page);
            }
        },
    },
};
</script>

<style scoped>
.bg-primary {
    background-color: #1f2937; /* Primary color */
}

.bg-primary-selected {
    background-color: lightgray;
    color: black;
}

button:hover {
    background-color: #272f46;
    color: white;
}

button:focus {
    outline: 2px solid #1f2937;
}

button {
    transition: background-color 0.3s ease;
}
</style>
