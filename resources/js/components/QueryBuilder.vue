<template>
	<div class="bg-white rounded-lg shadow-xs border border-gray-200 z-1">
        <div class="h-screen">
        	<div class="p-2">
        		<div class="px-1">
        			<div class="flex-1 w-full">
        				<form @submit="save" enctype="multipart/form-data">
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
			   						<div class="flex overflow-x-auto shadow-md rounded-md flex-wrap gap-2 mb-1 border border-dashed p-1 my-2" style="height: 345px; border: 1px solid #ddd; background-color: #282C34;">
							            <SqlEditor
									 		v-model="form.query"
									 		:schemas="schemas"
										/>
						         	</div>
						    	</div>
						    	<div class=" pt-4">
								    <Input
		                                key="search"
		                                label="Skema Tabel"
		                                v-model="search"
		                                name="search"
		                                id="search"
		                                hint="Cari dan gunakan skema tabel dibawah ini untuk membangun query"
		                            />
								    <div class="flex overflow-x-auto h-76 flex-wrap gap-2 mb-1 border border-dashed p-1 my-1">
									    <div class="p-4">
										    <ul class="list-disc pl-2 space-y-2 divide-y divide-gray-200">
											  <li v-for="(columns, tableName) in filteredSchemas" :key="tableName">
											    <div class="font-bold text-gray-800 text-sm">{{ tableName }}</div>
											    <ul class="list-disc pl-2 mt-1 space-y-1 text-xs text-gray-700 divide-y divide-gray-100 ml-4 border-gray-200">
											      <li v-for="col in columns" :key="col.name">
											        <span class="font-medium pr-2">{{ col.name }}</span>
											        <span class="text-gray-500">({{ col.type }})</span>
											        <span v-if="col.nullable" class="ml-1 text-green-600 font-semibold">null</span>
											        <span v-else class="ml-1 text-red-600 font-semibold">not null</span>
											        <span v-if="col.default" class="ml-2 text-blue-600">default: {{ col.default }}</span>
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
			                        <div class="flex gap-2">
									  	<button 
										    type="button"
										    @click="tryQuery()"
										    class="px-4 py-1 text-sm rounded bg-green-700 hover:bg-green-800 shadow-xs">
										    <LoaderCircle v-if="exeloading" class="animate-spin"/>
                    						<span v-else><Play class="w-4 h-4 text-white"/></span>
										    
									  	</button>

									  	<button 
										    type="submit" 
										    class="px-4 py-1 text-sm rounded shadow-xs bg-orange-400 text-white hover:text-gray-500 hover:bg-orange-500">
										    <Save class="w-4 h-4 text-white"/> 
									  	</button>
									  	 <span class="text-gray-400 italic text-xs mt-2 pl-2">NOTE: Query hanya mengambil data sampel maksimal 20 data</span>
									</div>
			                    </div>
					    	</div>
					    </form>
				    	<div class="space-y-4 grid grid-cols-1 gap-2">
				    		<div class="pt-2">
		   						<div class="flex overflow-x-auto flex-wrap gap-2 mb-1 border rounded-md border-dashed p-1" style="height: 330px; border: 1px solid #ddd;">
					         		<PreviewDataTable title="Preview" :query="query" @loading="setloading"/>
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
import SqlEditor from "@/components/SqlEditor.vue";
// import MonacoEditor from "monaco-editor-vue3";
import PreviewDataTable from '@/components/PreviewDataTable.vue'

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
        SqlEditor,
        PreviewDataTable
    },
    computed: {
	  filteredSchemas() {
	    if (!this.search) return this.schemas;

	    return Object.fromEntries(
	      Object.entries(this.schemas).filter(([tableName]) =>
	        tableName.toLowerCase().includes(this.search.toLowerCase())
	      )
	    );
	  }
	},
    data() {
        return {
        	search: "",
        	loading: false,
        	exeloading: false,
        	schemas: [],
        	form: {
        		name: '',
        		query: `select 
  items.nama,
  items.kode_barang as SKU,
  items.barcode,
  items.status,
  items.kategori,
  unit.nama,
  jumlah,
  minimum_stock,
  tanggal_pembaruan
  
  from item_stocks
  left join masters as unit on unit.id = item_stocks.unit_id 
  left join items on items.id = item_stocks.item_id`
        	},
        	query: []
        }
    },
    methods: {
    	async load(){
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
    	setloading(dt){
    		this.exeloading = dt
    	},
    	tryQuery(){
    		this.query = {
    			name: '',
    			query: this.form.query
    		}
    	},
    	save(){

    	}
    },
    mounted() {
    	this.load()
    }
}

</script>