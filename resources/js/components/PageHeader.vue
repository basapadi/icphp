<template>
    <div
        class="relative border-b border-border pl-6 pr-2 py-2 flex items-center justify-between"
    >
        <div>
            <span
                class="relative text-sm italic z-2 mr-1 font-bold text-foreground mb-0"
                >{{ title }} -</span
            >
            <span class="relative text-sm italic z-2 text-primary">{{
                description
            }}</span>
        </div>

        <button
            @click="toggleFullscreen"
            class="flex gap-1 px-2 py-1 text-xs hover:bg-accent border border-primary rounded-sm shadow-sm text-foreground"
            :title="isFullscreen ? 'Exit Fullscreen' : 'Enter Fullscreen'"
        >
            <Minimize v-if="isFullscreen" :size="12" />
            <Maximize v-else :size="12" />
            <!-- {{ isFullscreen ? 'Keluar' : 'Fullscreen' }} -->
        </button>
    </div>
</template>

<script>
import { Maximize, Minimize } from "lucide-vue-next";
export default {
    name: "PageHeader",
    components: {
        Maximize,
        Minimize,
    },
    props: {
        title: { type: String, default: "" },
        description: { type: String, default: "" },
    },
    data() {
        return {
            isFullscreen: false,
        };
    },
    methods: {
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                this.isFullscreen = true;
            } else {
                document.exitFullscreen();
                this.isFullscreen = false;
            }
        },
    },
};
</script>
