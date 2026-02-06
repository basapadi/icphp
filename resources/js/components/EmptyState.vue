<template>
    <div
        class="flex flex-col items-center justify-center py-16 text-center border rounded-2xl bg-card"
    >
        <h3 class="text-lg font-semibold text-foreground">
            {{ title }}
        </h3>

        <p class="text-sm text-muted-foreground mt-1" v-if="description">
            {{ description }}
        </p>

        <slot />

        <button
            v-if="showRefresh"
            @click="$emit('refresh')"
            class="mt-4 px-4 py-2 text-sm border rounded-lg hover:bg-muted"
        >
            Muat ulang
        </button>
    </div>
</template>

<script>
export default {
    name: "EmptyState",
    props: {
        title: {
            type: String,
            default: "Tidak ada data",
        },
        description: {
            type: String,
            default: "",
        },
        showRefresh: {
            type: Boolean,
            default: false,
        },
    },
};
</script>
