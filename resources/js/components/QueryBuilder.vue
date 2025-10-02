<template>
	<div class="bg-white rounded-lg shadow-xs border border-gray-200 z-1">
        <div class="h-screen">
        	<div class="p-2">
        		<div class="px-1">
        			Builder
        		</div>
        	</div>
        </div>
   </div>
</template>
<script>
import { mapGetters } from "vuex";
import { ref } from "vue"
import { Badge } from "@/components/ui";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from "@/components/ui/pagination";

export default {
	components: {
        Badge,
        FilterHeader,
        Button,
        Pagination,
        PaginationContent,
        PaginationEllipsis,
        PaginationFirst,
        PaginationItem,
        PaginationLast,
        PaginationNext,
        PaginationPrevious
    },
    props: {

    },
    data() {
        return {

        }
    },
    computed: {

    },
    methods: {

    },
    mounted() {

    }
}

</script>