<template>
	<div class="bg-white rounded-lg shadow-xs border border-gray-200 z-1">
        <div class="h-screen">
        	<div class="p-2">
        		<div class="px-1">
        			<div class="flex-1 w-full">
        				<div class="space-y-4 grid grid-cols-3 gap-2">
   							<div class="flex flex-col md:flex-row md:items-center gap-2">
	   							<Input
	                                key="name"
	                                label="Nama Laporan"
	                                v-model="form.name"
	                                name="name"
	                                id="name"
	                                hint="Masukkan nama laporan"
	                                :required="true"
	                            />
	                        </div>
   						</div>
				    	<div class="space-y-2 grid grid-cols-4 gap-2">
				    		<div class="pt-4 col-span-3">
							    <label
							      class="flex items-center gap-1 text-sm font-medium leading-none"
							    ><span class="text-gray-500 text-shadow-2xs">Query</span> </label>
		   						<div class="flex overflow-x-auto flex-wrap gap-2 mb-1 border border-dashed p-1 my-2" style="height: 300px; border: 1px solid #ddd;">
						            <MonacoEditor
								      v-model:value="form.query"
								      language="sql"
								      theme="vs-dark"
								      :options="editorOptions"
								    />
					         	</div>
					    	</div>
					    	<div class=" pt-4">
					    		<label
							      class="flex items-center gap-1 text-sm font-medium leading-none"
							    ><span class="text-gray-500 text-shadow-2xs">Schema</span> <span class="text-xs italic text-gray-400">Gunakan schema tabel dibawah ini</span> </label>
							    <div class="flex overflow-x-auto h-76 flex-wrap gap-2 mb-1 border border-dashed p-1 my-1">
								    <div class="p-4">
									    <ul class="list-disc pl-2 space-y-2 divide-y divide-gray-200">
									      <li v-for="(columns, tableName) in schemas" :key="tableName">
									        <div class="font-bold text-gray-800 text-sm">{{ tableName }}</div>
									        <ul class="list-disc pl-2 mt-1 space-y-1 text-xs text-gray-700 divide-y divide-gray-100 ml-4 border-gray-200">
									          <li v-for="col in columns" :key="col.name">
									            <span class="font-medium pr-2">{{ col.name }}</span>
									            <span class="text-gray-500">({{ col.type }})</span>

									            <span v-if="col.nullable" class="ml-1 text-green-600 font-semibold">
									              null
									            </span>
									            <span v-else class="ml-1 text-red-600 font-semibold" >
									              not null
									            </span>

									            <span v-if="col.default" class="ml-2 text-blue-600" >
									              default: {{ col.default }}
									            </span>
									          </li>
									        </ul>
									      </li>
								    	</ul>
								    </div>
							  	</div>
					    	</div>
				    	</div>
				    	<div class="space-y-2 grid grid-cols-1 gap-2">
				    		<div class="flex justify-start gap-2">
		                        <Button type="button" class="border-orange-200" variant="secondary" @click="run">Jalankan Query</Button>
		                        <Button class="border-orange-200" variant="secondary" type="submit">Simpan Query</Button>
		                    </div>
				    	</div>
				    	<div class="space-y-4 grid grid-cols-1 gap-2">
				    		<div class="pt-4">
							    <label
							      class="flex items-center gap-1 text-sm font-medium leading-none"
							    ><span class="text-gray-500 text-shadow-2xs">Preview</span> </label>
		   						<div class="flex overflow-x-auto flex-wrap gap-2 mb-1 border border-dashed p-1 my-2" style="height: 300px; border: 1px solid #ddd;">
					         	</div>
					    	</div>
				    	</div>
   					</div>
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
import { Input,Select,Radio,FileUpload,Textarea,Number,Phone,Password,Date } from "@/components/ui/form";
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
import MonacoEditor from "monaco-editor-vue3";

export default {
	components: {
        Badge,
        FilterHeader,
        Button,
        Input,
        Select,
        Radio,
        FileUpload,
        Textarea,
        Number,
        Phone,
        Password,
        Date,
        MonacoEditor
    },
    data() {
        return {
        	loading: false,
        	schemas: [],
        	editorOptions: { 
        		fontSize: 14, 
        		minimap: { 
        			enabled: true 
        		},
        		fontSize: 12,
			  	wordWrap: "on",
			  	automaticLayout: true
        	},
        	form: {
        		name: '',
        		query: `select *,
unit.nama as unit_name, 
items.nama as item_name 
from item_stocks 
left join masters as unit on unit.id = item_stocks.unit_id 
left join items on items.id = item_stocks.item_id`
        	}
        }
    },
    computed: {

    },
    methods: {
    	async load(){
    		console.log('LOADED')
    		this.loading = true
	        await this.$store
	          .dispatch('report/getSchema',{})
	          .then(({ data }) => {
	              data = data.data;
	              this.schemas = data;
	          })
	          .catch((err) => {
	              if (err.response) {
	                  if(err.response.status != 200){
	                      alert(err.response.data?.message)
	                  }
	              }
	          })
	          .finally((f) => {
	              this.loading = false
	          })
    	},
    	run(){

    	},
    	save(){

    	}
    },
    mounted() {
    	this.load()
    }
}

</script>