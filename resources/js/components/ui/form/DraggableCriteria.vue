<script lang="ts">
import { ref, computed } from "vue";
import draggable from "vuedraggable";
import { useVModel } from "@vueuse/core";
import type { PropType } from "vue";
import { cn } from "@/lib/utils";
interface CriteriaNode {
    id: number | string;
    name: string;
    parent_id: number | string;
    level: number;
    order: number;
    weight: number;
}

export default {
    name: "CriteriaTreeDraggable",
    components: { draggable },
    props: {
        modelValue: {
            type: Array as PropType<CriteriaNode[]>,
            required: true,
        },
        maxLevel: { type: Number, default: 3 },
        class: { type: [String, Array, Object], default: "" },
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const items = useVModel(props, "modelValue", emit);
        const editingIndex = ref<number | null>(null);
        const tempName = ref("");

        const increaseLevel = (item: CriteriaNode, index: number) => {
            if (item.level >= props.maxLevel) return;

            const newLevel = item.level + 1;
            const newParentId = findParentIdByLevel(index, newLevel);

            if (!newParentId) return;

            item.level = newLevel;
            item.parent_id = newParentId;
        };

        const decreaseLevel = (item: CriteriaNode, index: number) => {
            if (item.level <= 1) return;
            const oldParent = getNodeById(item.parent_id);

            item.level--;

            if (!oldParent) {
                item.parent_id = null;
                return;
            }

            // parent baru = parent dari parent lama
            item.parent_id = oldParent.parent_id ?? null;
        };
        const wrapperClass = computed(() =>
            cn("flex flex-col gap-2 pt-2", props.class)
        );

        const addNodeAfter = (index: number) => {
            const parent = items.value[index];
            const newNode: CriteriaNode = {
                id: `${Date.now()}`,
                name: "Kriteria Baru",
                parent_id: parent.id,
                level: Math.min(parent.level + 1, props.maxLevel),
                weight: 0,
                order: 0,
            };

            items.value.splice(index + 1, 0, newNode);
        };

        const removeNode = (index: number) => {
            const level = items.value[index].level;
            let removeCount = 1;

            for (let i = index + 1; i < items.value.length; i++) {
                if (items.value[i].level > level) {
                    removeCount++;
                } else {
                    break;
                }
            }

            items.value.splice(index, removeCount);
        };

        const startEdit = (index: number) => {
            editingIndex.value = index;
            tempName.value = items.value[index].name;
        };

        const saveEdit = (index: number) => {
            if (!tempName.value.trim()) return;
            items.value[index].name = tempName.value.trim();
            editingIndex.value = null;
        };

        const cancelEdit = () => {
            editingIndex.value = null;
        };

        const findParentIdByLevel = (index: number, childLevel: number) => {
            for (let i = index - 1; i >= 0; i--) {
                if (items.value[i].level < childLevel) {
                    return items.value[i].id;
                }
            }
            return null;
        };

        const getNodeById = (id: number | string | null) => {
            return items.value.find(i => i.id === id) ?? null;
        };

        return {
            items,
            increaseLevel,
            decreaseLevel,
            wrapperClass,
            addNodeAfter,
            removeNode,
            editingIndex,
            tempName,
            startEdit,
            saveEdit,
            cancelEdit,
            findParentIdByLevel,
        };
    },
};
</script>

<template>
    <div :class="wrapperClass">
        <draggable v-model="items" item-key="id" handle=".drag-handle" animation="200"
            :disabled="editingIndex !== null">
            <template #item="{ element, index }">
                <div class="flex items-center gap-2 p-1 border border-blue-500/20 rounded-sm bg-blue-50"
                    :style="{ marginLeft: (element.level - 1) * 50 + 'px' }">
                    <!-- Drag -->
                    <span class="drag-handle cursor-move text-muted-foreground">
                        ☰
                    </span>
                    <span class="text-xs font-bold text-muted-foreground">
                        L{{ element.level }}
                    </span>
                    <!-- Label -->
                    <div class="flex-1">
                        <span v-if="editingIndex !== index" class="text-sm cursor-text" @dblclick="startEdit(index)">
                            {{ element.name }}
                        </span>

                        <input v-else type="text" required="true"
                            class="h-7 w-full text-sm border rounded px-2 bg-background" v-model="tempName" min="0"
                            max="100" @keyup.enter="saveEdit(index)" @blur="saveEdit(index)" @keyup.esc="cancelEdit"
                            autofocus />
                    </div>


                    <span class="text-xs text-muted-foreground">Bobot :</span> <input type="number" min="0" max="100"
                        class="w-15 font-mono h-7 text-xs border rounded pl-2 bg-background"
                        v-model.number="element.weight" /><span class="text-xs text-muted-foreground">%</span>

                    <!-- Level Controls -->
                    <div class="flex gap-1">
                        <button type="button" class="px-2 text-md border rounded text-green-800"
                            @click="addNodeAfter(index)">
                            ＋
                        </button>

                        <button type="button" class="px-2 text-xs border rounded text-destructive"
                            @click="removeNode(index)">
                            ✕
                        </button>
                        <button type="button" class="px-2 py-1 text-xs border rounded" @click="decreaseLevel(element)">
                            ◀
                        </button>
                        <button type="button" class="px-2 py-1 text-xs border rounded"
                            @click="increaseLevel(element, index)">
                            ▶
                        </button>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>
