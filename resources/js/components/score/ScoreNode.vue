<template>
  <li class="mb-2">
    <!-- Row -->
    <div class="flex justify-between items-start" :class="levelClass">
      <div class="flex items-center gap-2">
        <span class="font-medium">
          {{ node.label }}
        </span>

        <span v-if="node.bobot !== null" class="text-xs text-gray-500 bg-gray-100 px-2 font-mono rounded">
          {{ node.weight }}%
        </span>
      </div>

      <span class="font-semibold">
        {{ format(node.score) }}
      </span>
    </div>

    <!-- Horizontal divider -->
    <div class="border-b border-gray-200" :class="{
      'ml-0': level === 1,
      'ml-0': level === 2,
      'ml-0': level >= 3
    }"></div>

    <!-- Children -->
    <ul v-if="node.children?.length" class="ml-6 mt-2 list-disc" :class="childListClass">
      <ScoreNode v-for="(child, i) in node.children" :key="i" :node="child" :level="level + 1" />
    </ul>
  </li>
</template>


<script setup>
import { computed } from 'vue'
import ScoreNode from './ScoreNode.vue'

const props = defineProps({
  node: Object,
  level: {
    type: Number,
    default: 1
  }
})

const levelClass = computed(() => {
  if (props.level === 1) return 'text-xs'
  if (props.level === 2) return 'text-xs'
  return 'text-xs text-green-800'
})

const childListClass = computed(() => {
  if (props.level === 1) return 'list-disc'
  if (props.level === 2) return 'list-circle'
  return 'list-square'
})

const format = val =>
  typeof val === 'number' ? val.toFixed(2) : '-'
</script>
