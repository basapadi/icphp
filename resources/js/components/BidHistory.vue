<template>
    <div class="border-1 border-primary/20 rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Riwayat Penawaran</h3>
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <User class="size-3.5" />
                <span>{{ bids.length }} penawaran</span>
            </div>
        </div>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            <div
                v-if="bids.length < 1"
                class="flex items-start gap-3 pb-3 border-b border-border last:border-b-0"
            >
                <div class="flex items-center justify-between gap-2">
                    <div class="text-muted-foreground text-sm">
                        Belum ada penawaran
                    </div>
                </div>
            </div>
            <div
                v-for="(bid, index) in bids"
                :key="bids.id"
                class="flex items-start gap-3 pb-3 border-b border-border last:border-b-0"
            >
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-full bg-gradient-to-br"
                    :class="
                        index === 0
                            ? 'from-blue-400 to-blue-600'
                            : 'from-gray-400 to-gray-600'
                    "
                >
                    <span class="text-xs font-bold text-primary-foreground">
                        {{ getInitials(bid?.user?.name) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <div class="font-semibold text-foreground text-sm">
                                {{ bid?.user?.name }}
                            </div>
                            <div
                                v-if="index === 0"
                                class="text-xs text-muted-foreground"
                            >
                                Penawaran Terbaru
                            </div>
                        </div>
                        <div
                            class="text-xs text-muted-foreground whitespace-nowrap"
                        >
                            {{ bid.created_at_formatted }}
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-foreground mt-1">
                        {{bid?.auction_detail?.nama}} {{bid?.auction_detail ? ' : ' : ''}} <span class="font-mono">{{ bid.harga_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { User } from "lucide-vue-next";

export default {
    name: "BidHistory",
    components: {
        User,
    },
    props: {
        bids: {
            type: Array,
            default: () => [],
        },
    },

    methods: {
        getInitials(name) {
            if (!name) return "";
            return name
                .trim()
                .split(" ")
                .slice(0, 2) // ambil max 2 kata
                .map((n) => n[0]?.toUpperCase())
                .join("");
        },
    },
};
</script>
