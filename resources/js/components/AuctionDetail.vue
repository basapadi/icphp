<template>
    <div class="bg-background rounded-2xl p-6">
        <h2 class="text-xl font-bold text-foreground mb-4">Detail Lelang</h2>
        <div class="grid grid-cols-2 space-y-4">
            <DetailItem label="kode" :value="auction?.kode" />
            <DetailItem label="tanggal" :value="auction?.tanggal_range_label" />
            <DetailItem label="harga awal" :value="auction?.harga_formatted" />
            <DetailItem
                label="Tipe Lelang"
                :value="auction?.auction_type_label"
            />
        </div>

        <div class="mt-4">
            <div class="text-sm text-muted-foreground mb-2">Daftar Item</div>
            <DataTableSimple :columns="column" :rows="auction?.details">
                <!-- ACTION -->
                <template #action="{ row }">
                    <div class="flex justify-end gap-2">
                        <button
                            class="px-2 py-1 rounded-md border text-xs hover:bg-muted"
                            @click="(showDetail = true), (selected = row), console.log('gambar', row.gambar)"
                        >
                            Lihat
                        </button>
                    </div>
                </template>
            </DataTableSimple>
        </div>
    </div>
    <Teleport to="body">
        <DetailDialogSimple
            :open="showDetail && selected"
            title="Detail Item"
            size="sm:max-w-[60%]"
            @close="showDetail = false"
        >
            <div class="space-y-4">
                <div class="grid grid-cols-4 gap-4">
                    <img
                        :src="selected.gambar"
                        class="h-28 w-auto rounded-md object-cover"
                    />
                    <div class="col-span-3 grid grid-cols-1 md:grid-cols-3 space-y-4">
                        <DetailItem label="kode" :value="selected.kode" />
                        <DetailItem label="nama" :value="selected.nama" />
                        <DetailItem label="merk" :value="selected.merk" />
                        <DetailItem
                            v-if="selected.no_id"
                            label="No ID"
                            :value="selected.no_id"
                        />
                        <DetailItem
                            label="kondisi"
                            :value="selected.condition_label"
                        />
                        <DetailItem
                            label="harga"
                            :value="selected.harga_formatted"
                        />
                    </div>
                </div>
                <DetailItem label="spefikasi" :value="selected.spesifikasi" />
                <DetailItem label="keterangan" :value="selected.keterangan" />
            </div>
        </DetailDialogSimple>
    </Teleport>
</template>

<script>
import DataTableSimple from "./DataTableSimple.vue";
import DetailDialogSimple from "./DetailDialogSimple.vue";
import DetailItem from "./DetailItem.vue";

export default {
    name: "AuctionDetail",
    props: {
        auction: {},
    },
    components: {
        DataTableSimple,
        DetailDialogSimple,
        DetailItem,
    },
    data() {
        return {
            column: [
                { key: "kode", label: "Kode" },
                { key: "nama", label: "Nama" },
                { key: "merk", label: "Merk" },
                { key: "harga_formatted", label: "Harga" },
            ],
            showDetail: false,
            selected: {},
        };
    },
    watch: {
        selected(newVal) {
            console.log("selected changed:", newVal);
        },
    },
};
</script>
