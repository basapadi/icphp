<template>
  <ul class="list-decimal px-6 pt-2">
    <ScoreNode
      v-for="(node, i) in tree"
      :key="i"
      :node="node"
      :level="1"
    />
  </ul>
  <div class="text-sm font-semibold text-gray-800">
    Nilai Akhir :
    <span class="ml-2 font-mono text-green-800">
    {{ format(finalScore) }}
    </span>
</div>
</template>

<script setup>
import ScoreNode from './ScoreNode.vue'

defineProps({
  tree: Array,
  finalScore: Number
})

function format(val) {
  return Number(val).toFixed(2)
}
</script>
