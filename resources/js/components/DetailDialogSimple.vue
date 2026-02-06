<template>
    <!-- Overlay -->
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click="close"
    >
        <!-- Dialog -->
        <div class="dialog-scale-animation" data-state="open" @click.stop>
            <Card
                class="w-auto max-w-[90%] min-w-[50vw] max-h-[80vh] mx-auto"
                :class="widthClass"
            >
                <CardHeader>
                    <CardTitle class="uppercase tracking-wider text-foreground">
                        {{ title }}
                    </CardTitle>
                </CardHeader>

                <!-- Slot: isi bebas -->
                <CardContent class="overflow-y-auto">
                    <slot />
                </CardContent>

                <CardFooter class="flex justify-end">
                    <Button variant="outline" @click="close"> Tutup </Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { computed } from "vue";

const props = defineProps<{
    open: boolean;
    title?: string;
    size?:
        | "sm"
        | "md"
        | "lg"
        | "xl"
        | "2xl"
        | "3xl"
        | "4xl"
        | "5xl"
        | "6xl"
        | "7xl"
        | string;
}>();

const emit = defineEmits<{ (e: "close"): void }>();

function close() {
    emit("close");
}

const widthClass = computed(() => {
    // default: md
    const s = props.size ?? "md";
    return `max-w-${s}`;
});
</script>

<style scoped>
.dialog-scale-animation[data-state="open"] {
    animation: scaleIn 0.15s ease-out;
}
@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.97);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
