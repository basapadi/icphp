<template>
    <span :class="c">{{ value }}</span>
</template>
<script>
export default {
    name: 'Badge',
    components: {},
    props: {
        value: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            c: 'inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 inset-ring inset-ring-gray-600/20'
        }
    },
    methods: {
        load() {
            switch (this.type) {
                case 'success':
                    this.c = 'inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 inset-ring inset-ring-green-600/20'
                    break;
                case 'error':
                    this.c = 'inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 inset-ring inset-ring-red-600/10'
                default:
                    break;
            }
        }
    },
    mounted() {
        this.load()
    }
}
</script>