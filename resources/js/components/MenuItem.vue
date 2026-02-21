<template>
    <li>
        <!-- Item utama -->
        <router-link
            :to="item.route || '#'"
            @click="handleClick(item)"
            :class="linkClasses"
            :style="{ paddingLeft }"
        >
            <div class="flex items-center w-full justify-between">
                <!-- Ikon + Label -->
                <div class="flex items-center">
                    <component
                        v-if="item.icon"
                        :is="item.icon"
                        class="w-4 h-4 text-primary"
                        :class="collapsed ? 'mx-auto' : 'mr-3'"
                    />
                    <!-- Label hanya tampil jika tidak collapsed -->
                    <span
                        v-if="!collapsed"
                        class="text-muted-foreground truncate"
                        >{{ item.label }}</span
                    >
                </div>

                <!-- Panah submenu -->
                <div
                    v-if="hasSubItems && !collapsed"
                    class="flex items-center mr-2"
                >
                    <ChevronDown v-if="isOpen" class="w-4 h-4" />
                    <ChevronRight v-else class="w-4 h-4" />
                </div>
            </div>
        </router-link>

        <!-- Submenu -->
        <transition name="fade">
            <ul
                v-if="hasSubItems && isOpen"
                class="border-l-2 mr-2 ml-6 text-left border-primary/20"
                v-show="!collapsed"
            >
                <MenuItem
                    v-for="(subItem, index) in item.sub_items"
                    :key="index"
                    :item="subItem"
                    :level="level + 1"
                    :open="isOpen"
                    :collapsed="collapsed"
                />
            </ul>
        </transition>
    </li>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useStore } from "vuex";
import { ChevronDown, ChevronRight } from "lucide-vue-next";

const store = useStore();

const props = defineProps({
    item: { type: Object, required: true },
    level: { type: Number, default: 0 },
    open: { type: Boolean, default: false },
    collapsed: { type: Boolean, default: false }, // <--- tambahan penting
});

const isOpen = ref(false);

watch(
    () => props.item.open,
    (val) => {
        isOpen.value = val;
    },
    { immediate: true }
);

const hasSubItems = computed(
    () => props.item.sub_items && props.item.sub_items.length > 0
);

const paddingLeft = computed(() => {
    // Jika collapsed, padding kecil supaya ikon tetap di tengah
    return props.collapsed ? "0.5rem" : `${0.75 + props.level * 0.5}rem`;
});

const linkClasses = computed(() => {
    return [
        "flex items-center justify-between mx-2 py-2 text-sm rounded-sm transition-all duration-200",
        props.item.active
            ? "text-foreground bg-primary/20 border border-primary/40"
            : "text-foreground hover:bg-primary/10",
        props.collapsed ? "justify-center" : "justify-between",
    ].join(" ");
});

const handleClick = (item) => {
    if (hasSubItems.value) {
        isOpen.value = !isOpen.value;
        store.commit("menu/setToggleMenu", item.id);
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
