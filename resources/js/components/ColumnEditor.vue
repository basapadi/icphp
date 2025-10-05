<template>
  <!-- Overlay -->
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click="close()">
    <Card class="bg-white shadow-lg rounded-lg overflow-hidden" @click.stop>
      <CardContent class="max-h-[80vh] overflow-y-auto">
      <draggable v-model="editedColumns" :move="onMove" handle=".handle" item-key="name" class="space-y-2">
        <template #item="{ element, index }">
          <div
              class="flex items-center gap-3 p-2 border rounded-lg"
              :class="{
                'bg-gray-200 opacity-70 cursor-not-allowed': isLocked(element.name),
                'bg-gray-50': !isLocked(element.name)
              }"
            >
            <span class="handle cursor-move text-gray-400">☰</span>

            <!-- Label -->
            <input
              v-model="element.label"
              class="border p-1 rounded w-40 text-sm"
              placeholder="Label"
            />

            <!-- Type -->
            <select v-model="element.type" class="border p-1 rounded text-sm">
              <option value="text">text</option>
              <option value="date_range">range tanggal</option>
              <option value="select">select</option>
              <option value="badge">badge</option>
            </select>

            <!-- Show -->
            <label class="flex items-center gap-1">
              <input type="checkbox" v-model="element.show" />
              <span class="text-sm">grid</span>
            </label>

            <label class="flex items-center gap-1">
              <input type="checkbox" v-model="element.option_filter" />
              <span class="text-sm">filter</span>
            </label>
            <label class="flex items-center gap-1">
              <input type="checkbox" v-model="element.sortable" />
              <span class="text-sm">sort</span>
            </label>

            <!-- Align -->
            <select v-model="element.align" class="border p-1 rounded text-sm">
              <option value="left">Kiri</option>
              <option value="center">Tengah</option>
              <option value="right">Kanan</option>
            </select>
            <input
              v-model="element.class"
              class="border p-1 rounded w-40 text-sm"
              placeholder="tailwind class"
            />
            <input
              v-model="element.styles"
              class="border p-1 rounded w-40 text-sm"
              placeholder="style css"
            />
            <span class="text-gray-500 text-sm">{{ element.name }}</span>
          </div>
        </template>
      </draggable>
      </CardContent>
      <div class="flex pr-4 justify-end gap-2">
        <Button type="button" class="border-orange-200" variant="secondary" @click="close">Batal</Button>
        <Button class="border-orange-200" variant="secondary" type="button" @click="save()">
            <LoaderCircle v-if="loader" class="animate-spin"/>
            <span v-else>Simpan</span>
        </Button>
      </div>
    </Card>
  </div>
</template>
<script>
  import {
    Input, Select, Radio, Textarea, Number,Date
  } from "@/components/ui/form";
  import { Card, CardContent } from "@/components/ui/card";
  import { Button } from "./ui/button";
  import draggable from "vuedraggable";

  export default {
    name: "ConfirmDialog",
    setup(){
      const lockedNames = ['actions'];

      return {lockedNames}
    },
    watch:{
      'columns': {
            handler() {
              if (this.columns && this.columns.length > 0) {
                this.editedColumns = [...this.columns];
              }
            },
            immediate: true
        },
    },
    props: {
      open: {type: Boolean, default: false},
      columns: {type: Array, default: []},
      module: {type: String, default: ''}
    },
    components: {
      Input, Select, Radio, Number, Date, Card, CardContent, Button, draggable
    },
    data() {
      return {
        loader: false,
        editedColumns: []
      };
    },
    methods: {
      close() {
        this.$emit("close");
      },
      onMove(evt){
        const dragged = evt.draggedContext.element;
        const target = evt.relatedContext?.element;
        if (this.isLocked(dragged.name) || this.isLocked(target?.name)) return false;
        return true;
      },
      isLocked(name){
        return this.lockedNames.includes(name);
      },
      save(){
        this.$confirm(
        {
          message: `Apakah anda yakin menyimpan pengaturan kolom ini?`,
          button: {
              no: 'Tidak',
              yes: 'Ya'
          },
          callback: async confirm => {
              if (confirm) {
                  await this.$store.dispatch('common/saveColumns', {
                    columns: this.editedColumns,
                    module: this.module
                  })
                  .then(({ data }) => {
                      this.$emit("reload", true);
                      alert('Pengaturan kolom berhasil disimpan')
                  })
                  .catch((resp) => {
                      alert(resp.response?.data?.message)
                  })
                  .finally((f) => {
                          
                  })
              }
          }
        })
      }
    }
  }
</script>