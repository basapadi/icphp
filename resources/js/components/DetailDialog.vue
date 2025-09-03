<template>
    <!-- Overlay -->
    <div v-if="open" class="h-screen fixed inset-0 flex items-center justify-center bg-black/50 z-50" >
        <Card class="w-full max-w-7xl">
            <CardContent class="max-h-[80vh] overflow-y-auto">
                <div v-for="(sub, i) in schema" :key="i">
                    <template v-if="i == 'main' && sub.type == 'object'">
                        <div class="bg-white shadow-sm rounded-md py-2 px-2 mb-2">
                            <div class="py-2 px-5"><span class="text-gray-500 uppercase tracking-wider">{{ sub.title }}</span></div>
                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                                    <tbody>
                                        <tr class="border-b" v-for="(f,k) in sub.fields" :key="k">
                                            <td class="px-4 py-2 border-r text-gray-500 text-sm" style="width:250px;">{{ f?.label }}</td>
                                            <td :class="`px-4 py-2 text-gray-500 text-sm text-${data['color_'+k]} ${f?.class}`">{{ $helpers.getSubObjectValue(data,k) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                    <template v-if="i != 'main' && sub.type == 'array'">
                        <div class="bg-white shadow-sm rounded-md py-2 px-2 mb-2">
                            <div class="py-2 px-5"><span class="text-gray-500 uppercase tracking-wider">{{ sub.title }}</span></div>
                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-200 rounded-md overflow-hidden">
                                    <thead class="bg-orange-100 text-gray-700">
                                        <tr>
                                            <th v-for="(f,k) in sub.fields" class="px-4 py-2 text-left text-sm border-b border-gray-200" :key="k" :class="f?.class" :style="f?.style">{{ f?.label }}</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template v-if="data[i].length > 0">
                                            <tr v-for="(row, r) in data[i]" class="hover:bg-gray-50" :key="r">
                                                <td class="px-4 py-2 text-gray-500 text-sm"  v-for="(col, c) in sub.fields" :key="c" :class="col?.class">
                                                    <span v-if="c == 'no'">{{ r+1 }}</span>
                                                    <span v-else>{{ $helpers.getSubObjectValue(row,c) }}</span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                </div>
            </CardContent>
            <div class="flex justify-end gap-2 pr-8">
                <Button variant="outline" @click="close">Tutup</Button>
            </div>
        </Card>
    </div>
</template>
<script>
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";

export default {
    name: "DetailDialog",
    props: {
        open: Boolean,
        title: { type: String, default: () => {''}},
        data: { type: Object, default: () => ({}) },
        schema: {type: Object, defautl: () => ({})}
    },
    components: {
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        Button
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
        submit() {
            this.close();
        },
    }
};
</script>
