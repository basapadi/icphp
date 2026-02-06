<template>
    <div
        class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-primary-foreground"
    >
        <div class="flex flex-col gap-2">
            <!-- ROW ATAS -->
            <div class="text-sm opacity-90">
                Penawaran Tertinggi
            </div>

            <!-- ROW BAWAH -->
            <div class="flex items-start justify-between">
                <!-- ISI BID -->
                <div>
                    <!-- SINGLE BID -->
                    <div v-if="!Array.isArray(bid)">
                        <div class="text-4xl font-bold">
                            {{ bid?.harga_formatted }}
                        </div>
                        <p class="text-sm opacity-90">
                            {{ bid?.user?.name }}
                        </p>
                    </div>

                    <!-- MULTIPLE BID -->
                    <div v-else class="flex flex-col gap-2">
                        <div
                            v-for="b in bid"
                            :key="b.id"
                            class="flex items-center gap-2"
                        >
                            <p class="text-xs opacity-80 whitespace-nowrap"> {{ b?.user?.name }} </p> |
                            <p class="text-sm font-bold opacity-90 whitespace-nowrap"> {{ b?.auction_detail?.nama }} </p> |
                            <div class="font-bold font-mono"> {{ b?.harga_formatted }} </div>
                        </div>
                    </div>
                </div>

                <!-- ICON -->
                <div class="bg-muted/20 hover:bg-muted/30 p-2 rounded-lg transition">
                    <Trophy class="size-4" />
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { Trophy } from 'lucide-vue-next';

export default {
    name: "BidTop",
    components: {
        Trophy
    },
    props: {
        bid: {
            type: [Object, Array],
            default: () => [],
        },
    },
};
</script>
