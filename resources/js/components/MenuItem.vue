<template>
    <li>
        <router-link
            v-if="item.route && item.parent_id == null"
            :to="item.route"
            @click="handleClick"
            :class="linkClasses"
            :style="{ paddingLeft }"
        >
            <div class="flex items-center ">
                <component v-if="item.icon" :is="item.icon" class="w-4 h-4 mr-3 text-gray-700" />
                <span class="text-gray-600">{{ item.label }}</span>
            </div>
            <div class="flex items-center">
                <ChevronDown v-if="hasSubItems && isOpen" class="w-4 h-4" />
                <ChevronRight v-else-if="hasSubItems" class="w-4 h-4" />
            </div>
        </router-link>
        <router-link
            v-else
            :to="item.route"
            @click="handleClick"
            :class="linkClasses"
            :style="{ paddingLeft }"
        >
            <div class="flex items-center" v-if="hasSubItems">
                <GripVertical class="w-3 h-3 text-orange-400"/><span class=" text-gray-600">{{ item.label }}</span>
            </div>
            <div class="flex items-center" v-else>
                <component v-if="item.icon" :is="item.icon" class="w-4 h-4 mr-2" />
                <EllipsisVertical class="w-3 h-3 text-orange-400"/><span class=" text-gray-600">{{ item.label }}</span>
            </div>
            <div class="flex items-center">
                <ChevronDown v-if="hasSubItems && isOpen" class="w-4 h-4" />
                <ChevronRight v-else-if="hasSubItems" class="w-4 h-4" />
            </div>
        </router-link>

        <ul v-if="hasSubItems && isOpen" class="border-l-2 ml-4 text-left">
            <MenuItem
                v-for="(subItem, index) in item.sub_items"
                :key="index"
                :item="subItem"
                :level="level + 1"
                :open="isOpen"
            />
        </ul>
    </li>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    level: {
        type: Number,
        default: 0,
    },
    open: {
        type: Boolean,
        default: false,
    },
});
let isOpen = ref(false);
isOpen.value = props.open;
if (props.item.open) isOpen.value = props.item.open;
const hasSubItems = computed(() => {
    return props.item.sub_items && props.item.sub_items.length > 0;
});

const paddingLeft = computed(() => `${0.75}rem`);

const linkClasses = computed(
    () =>
        `flex items-left justify-between mx-2 py-2 text-sm transition delay-50 duration-100 ease-in-out hover:translate-x-1 hover:scale-105 rounded-sm ${
            props.item.active
                ? "text-gray-700 border-1 bg-gray-100 border-gray-100 shadow-sm border-gray-300 border-dashed my-2"
                : "text-gray-700 hover:bg-gray-100 hover:shadow-sm hover:rounded-sm"
        }`
);

const handleClick = () => {
    if (hasSubItems.value) {
        isOpen.value = !isOpen.value;
    }
};
</script>
