<template>
    <div class="flex flex-col gap-1.5">
        <Label :for="id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            {{ label }}
            <span v-if="required" class="text-red-800"> *</span>
        </Label>
        <select
            :id="id"
            :value="modelValue"
            :name="name"
            :hint="hint"
            :required="required"
            :class="computeClass"
            @input="$emit('update:modelValue', $event.target.value)"
        >
            <option v-for="(o,i) in options" :value="i" :key="i">{{o}}</option>
        </select>
    </div>
</template>
<script>
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";
export default {
    name: 'Select',
    components: {Label},
    props: {
        modelValue: {type: String,default: ''},
        id: {type: String,default: ''},
        name: {type: String,default: ''},
        label: {type: String,default: ''},
        hint: {type: String,default: ''},
        class: {type: String,default: ''},
        required: {type: Boolean,default: false},
        options:{type: Object,default: {}}
    },
    computed: {
        computeClass(){
            return cn('flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',this.class)
        }
    }
}
</script>