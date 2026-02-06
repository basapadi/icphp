<template>
    <!-- Overlay dengan transition -->
    <div v-if="open" class="h-screen flex fixed inset-0 items-center justify-center bg-black/50 z-50" @click="closePreview()">
        <!-- Container dialog dengan animasi -->
        <div class="dialog-scale-animation">
            <Card class="w-[90vw] h-[90vh] px-4">
                <iframe
                    :src="pdfUrl"
                    width="100%"
                    height="100%"
                ></iframe>
                <!-- Actions -->
                    <div class="flex justify-end gap-2">
                        <Button type="button" class="border-primary" variant="secondary"
                            @click="closePreview">Tutup</Button>
                    </div>
            </Card>
        </div>
    </div>
</template>

<script>
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { SquareChevronRight } from "lucide-vue-next";
import { Button } from "./ui/button";
export default {
    components: {
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        SquareChevronRight,
        Button
    },
    props: {
        open: {
            type: Boolean,
            default: false,
        },
        pdfUrl: {
            type: String,
            default: "",
        },
        pdfFilename: {
            type: String,
            default: "",
        },
    },
    computed: {
        dialogState() {
            return this.open ? "open" : "closed";
        },
    },
    methods: {
        closePreview() {
            this.$emit("close");
        },
    },
};
</script>