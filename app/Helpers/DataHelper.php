<?php
use App\Models\{
    ItemStock,
    ItemStockAdjustment
};

if(!function_exists('injectData')){
    function injectData(&$data, array $replacements)
    {
        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if (is_array($value)) {
                    injectData($value, $replacements);
                } elseif (is_string($value) && str_starts_with($value, ':')) {
                    $placeholder = substr($value, 1);
                    if (array_key_exists($placeholder, $replacements)) {
                        $value = $replacements[$placeholder];
                    }
                }
            }
            unset($value);
        }
    }
}

if(!function_exists('calculateStockCreate')){
    function calculateStockCreate(ItemStock $stock,$type,$qty){
        $type = config("ihandcashier.adjustment_types.{$type}");
        switch ($type['action_create']) {
            case 'addition':
                // hapus adjustment → stok ditambah
                $stock->jumlah += $qty;
                break;
            case 'reduction':
                // hapus adjustment → stok dikurangi
                $stock->jumlah -= $qty;
                break;
        }

        return $stock;
    }
}