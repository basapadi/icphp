<script lang="ts">
import { computed } from "vue"
import type { PropType } from "vue"
import { cn } from "@/lib/utils"

interface CriteriaNode {
    id: number | string
    name: string
    parent_id: number | string
    level: number
    order: number
    weight: number
}

export default {
    name: "CriteriaTree",
    props: {
        items: {
            type: Array as PropType<CriteriaNode[]>,
            required: true,
        },
        class: {
            type: [String, Array, Object],
            default: "",
        },
    },
    setup(props) {
        const wrapperClass = computed(() =>
            cn("flex flex-col gap-2 pt-2", props.class)
        )

        return {
            wrapperClass,
        }
    },
}
</script>

<template>
    <div :class="wrapperClass">
        <div
            v-for="item in items"
            :key="item.id"
            class="flex items-center gap-2 p-1 border border-blue-500/20 rounded-sm bg-blue-50"
            :style="{ marginLeft: (item.level - 1) * 50 + 'px' }"
        >
            <!-- Level Indicator -->
            <span class="text-xs font-bold text-muted-foreground">
                L{{ item.level }}
            </span>

            <!-- Label -->
            <div class="flex-1">
                <span class="text-sm">
                    {{ item.name }}
                </span>
            </div>

            <!-- Weight -->
            <span class="text-xs text-muted-foreground">Bobot :</span>
            <span class="w-12 text-xs font-mono text-right">
                {{ item.weight }}%
            </span>
        </div>
    </div>
</template>
