<template>
    <div class="rounded-xl border bg-card">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b">
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="h-10 px-4 text-left font-medium text-muted-foreground"
                        >
                            {{ col.label }}
                        </th>

                        <th
                            class="h-10 px-4 text-right font-medium text-muted-foreground"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="[&_tr:last-child]:border-0">
                    <tr v-if="loading">
                        <td
                            :colspan="columns.length + 1"
                            class="h-16 text-center"
                        >
                            Memuat…
                        </td>
                    </tr>

                    <tr v-else-if="!rows.length">
                        <td
                            :colspan="columns.length + 1"
                            class="h-16 text-center"
                        >
                            {{ emptyText }}
                        </td>
                    </tr>

                    <tr
                        v-else
                        v-for="(row, idx) in rows"
                        :key="row.id ?? idx"
                        class="border-b hover:bg-muted/50"
                    >
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            class="p-4 align-middle"
                        >
                            <!-- 1) SLOT PER KOLOM (paling fleksibel) -->
                            <slot
                                :name="`cell-${col.key}`"
                                :value="row[col.key]"
                                :row="row"
                                :index="idx"
                            >
                                <!-- 2) FALLBACK: render() -->
                                <span v-if="typeof col.render === 'function'">
                                    {{ col.render(row[col.key], row) }}
                                </span>

                                <!-- 3) DEFAULT -->
                                <span v-else>
                                    {{ row[col.key] }}
                                </span>
                            </slot>
                        </td>

                        <td class="p-4 text-right">
                            <slot name="action" :row="row" :index="idx" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    emptyText: {
        type: String,
        default: "Data tidak tersedia",
    },
});
</script>
