<template>
    <div class="flex justify-between bg-white rounded-2xl p-6 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-sm font-medium text-blue-600"
                    >Lelang Berjalan</span
                >
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                {{ auction?.judul || "" }}
            </h1>
            <p class="text-gray-600 mb-6">
                {{ auction?.keterangan || "" }}
            </p>
        </div>

        <BidCountdownTimer :to_date="auction?.to_date" />
    </div>
</template>

<script>
import BidCountdownTimer from "./BidCountdownTimer.vue";

export default {
    name: "BidHeader",
    props: {
        auction: {},
    },
    components: {
        BidCountdownTimer,
    },
};
</script>
