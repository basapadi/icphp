<template>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
        <div class="z-2 border border-border rounded-lg shadow-sm">
            <div class="flex flex-row items-center justify-between p-2">
                <h3 class="text-sm font-semibold text-muted-foreground">
                    Jatuh Tempo Penerimaan
                </h3>
                <button class="p-2 hover:bg-accent rounded">
                    <MoreHorizontal class="h-4 w-4" />
                </button>
            </div>
            <div class="p-4 pt-2">
                <div class="space-y-2">
                    <div
                        v-for="(dt, index) in datas.receive_duedate"
                        :key="index"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-muted rounded-full flex items-center justify-center"
                            >
                                <span
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    {{ getInitials(dt.contact_name) }}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold font-mono text-foreground"
                                >
                                    {{ dt.kode_transaksi }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ dt.contact_name }}
                                </p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-muted-foreground">Syarat</p>
                            <p class="text-sm font-medium text-foreground">
                                {{ dt.syarat }}
                            </p>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-muted-foreground">
                                Jatuh Tempo
                            </p>
                            <p class="text-sm font-medium text-foreground">
                                {{ dt.tanggal_duedate }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="z-2 border border-border rounded-lg shadow-sm">
            <div class="flex flex-row items-center justify-between p-2">
                <h3 class="text-sm font-semibold text-muted-foreground">
                    Jatuh Tempo Penjualan
                </h3>
                <button class="p-2 hover:bg-accent rounded">
                    <MoreHorizontal class="h-4 w-4" />
                </button>
            </div>
            <div class="p-4 pt-2">
                <div class="space-y-2">
                    <div
                        v-for="(dt, index) in datas.sale_duedate"
                        :key="index"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-muted rounded-full flex items-center justify-center"
                            >
                                <span
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    {{ getInitials(dt.contact_name) }}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold font-mono text-foreground"
                                >
                                    {{ dt.kode_transaksi }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ dt.contact_name }}
                                </p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-muted-foreground">Syarat</p>
                            <p class="text-sm font-medium text-foreground">
                                {{ dt.syarat }}
                            </p>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-muted-foreground">
                                Jatuh Tempo
                            </p>
                            <p class="text-sm font-medium text-foreground">
                                {{ dt.tanggal_duedate }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="z-2 border border-border rounded-lg shadow-sm">
            <div class="flex flex-row items-center justify-between p-2">
                <h3 class="text-sm font-semibold text-muted-foreground">
                    Barang paling laku bulan ini
                </h3>
                <button class="p-2 hover:bg-accent rounded">
                    <MoreHorizontal class="h-4 w-4" />
                </button>
            </div>
            <div class="p-4 pt-2">
                <div class="space-y-4">
                    <div
                        v-for="(product, index) in datas.top_products"
                        :key="index"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-primary/20 rounded-lg flex items-center justify-center"
                            >
                                <span class="text-xs font-bold text-primary"
                                    >#{{ index + 1 }}</span
                                >
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground">
                                    Barang
                                </p>
                                <p class="text-sm font-medium text-foreground">
                                    {{ product.nama_barang }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-muted-foreground">
                                Total Penjualan
                            </p>
                            <p class="text-sm font-medium text-foreground">
                                IDR {{ product.revenue }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minimum Stock Products -->
        <div class="z-2 border border-border rounded-lg shadow-sm">
            <div class="flex flex-row items-center justify-between p-2">
                <h3 class="text-sm font-semibold text-muted-foreground">
                    Barang Hampir Habis
                </h3>
                <button class="p-2 hover:bg-accent rounded">
                    <MoreHorizontal class="h-4 w-4" />
                </button>
            </div>
            <div class="p-4 pt-2">
                <div class="space-y-4">
                    <div
                        v-for="(product, index) in datas.minimum_stocks"
                        :key="index"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-primary/20 rounded-lg flex items-center justify-center"
                            >
                                <span class="text-xs font-bold text-primary"
                                    >#{{ index + 1 }}</span
                                >
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground">
                                    Barang
                                </p>
                                <p class="text-sm font-medium text-foreground">
                                    {{ product.nama_barang }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-muted-foreground">
                                Jlh.Stok
                            </p>
                            <p
                                class="text-sm font-bold font-mono text-foreground"
                            >
                                {{ product.stock }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import { MoreHorizontal, ArrowUpRight } from "lucide-vue-next";

export default {
    name: "DashboardGrid",
    components: {
        MoreHorizontal,
        ArrowUpRight,
    },
    props: {
        datas: {
            type: Array,
            default: [],
        },
    },
    data() {
        return {
            data: {},
        };
    },
    methods: {
        getInitials(name) {
            return name
                .split(" ")
                .map((n) => n[0])
                .join("");
        },
    },
};
</script>
