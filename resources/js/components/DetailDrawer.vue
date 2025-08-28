<template>
    <Drawer v-if="open">
        <DrawerContent>
            <DrawerHeader class="text-left">
                <DrawerTitle>Detail {{ title }}</DrawerTitle>
            </DrawerHeader>
            <CardContent>
                <div>ini isi detail</div>
            </CardContent>
            <DrawerFooter class="pt-2">
                <DrawerClose as-child>
                    <Button variant="outline"> Cancel </Button>
                </DrawerClose>
            </DrawerFooter>
        </DrawerContent>
    </Drawer>
</template>
<script>
import { Input } from "@/components/ui";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import {
    Drawer,
    DrawerContent,
    DrawerHeader,
    DrawerTitle,
    DrawerFooter,
    DrawerClose,
} from "@/components/ui/drawer";

export default {
    name: "DetailDrawer",
    props: {
        open: Boolean, defaut: false,
        data: { type: Object, default: () => ({}) },
    },
    components: {
        Input,
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        Button,
        Drawer,
        DrawerContent,
        DrawerHeader,
        DrawerTitle,
        DrawerFooter,
        DrawerClose,
    },
    data() {
        return {
            form: {},
        };
    },
    methods: {
        load() {},
        close() {
            this.$emit("close");
        },
    },
    beforeMount() {
        
    },
};
</script>
