<template>
  <li>
    <router-link
      v-if="item.route"
      :to="item.route"
      @click="handleClick"
      :class="linkClasses"
      :style="{ paddingLeft }"
    >
      <div class="flex items-center">
        <component v-if="item.icon" :is="item.icon" class="w-4 h-4 mr-3" />
        {{ item.label }}
      </div>
      <ChevronDown v-if="hasSubItems && open" class="w-4 h-4" />
      <ChevronRight v-else-if="hasSubItems" class="w-4 h-4" />
    </router-link>
    
    <a
      v-else
      href="#"
      @click.prevent="handleClick"
      :class="linkClasses"
      :style="{ paddingLeft }"
    >
      <div class="flex items-center">
        <component v-if="item.icon" :is="item.icon" class="w-4 h-4 mr-3" />
        {{ item.label }}
      </div>
      <ChevronDown v-if="hasSubItems && isOpen" class="w-4 h-4" />
      <ChevronRight v-else-if="hasSubItems" class="w-4 h-4" />
    </a>

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
import { ref, computed } from 'vue'
import { ChevronDown, ChevronRight } from 'lucide-vue-next'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 0
  },
  open: {
    type: Boolean,
    default: false
  }
})
let isOpen = ref(false)
isOpen.value = props.open
if(props.item.open) isOpen.value = props.item.open
const hasSubItems = computed(() => {
  return props.item.sub_items && props.item.sub_items.length > 0
})

const paddingLeft = computed(() => 
  `${0.75 + props.level * 1}rem`
)

const linkClasses = computed(() => 
  `flex items-left justify-between mx-2 py-2 text-sm font-medium transition-all duration-200 rounded-sm mx-2   ${
    props.item.active 
      ? "bg-orange-500 outline-orange-400 text-white outline-1 my-2 font-1200 shadow-xl/30" 
      : "text-gray-700 hover:bg-gray-100 hover:text-gray-900 hover:inset-shadow-grey-800 hover:inset-shadow-sm"
  }`
)

const handleClick = () => {
  console.log(props.level)
  console.log(isOpen.value)
  if (hasSubItems.value) {
    isOpen.value = !isOpen.value
  }
  console.log(isOpen.value)

}
</script>
