<?php
use App\Models\{
    ItemStock,
    ItemStockAdjustment,
    Contact,
    Item,
    Master,
    User
};

use App\Transformers\FormTransformer;
use Spatie\Fractalistic\ArraySerializer;

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

if(!function_exists('generateTransactionCode')){
    function generateTransactionCode($prefix) {
        $datePart = sprintf(
            "%02d%02d%02d%02d%02d",
            date('s'),
            date('i'),  
            date('m'),
            date('d'),
            date('H')
        );
        $randomPart = str_pad(rand(0, 9999), 2, '0', STR_PAD_LEFT); // Tambahkan 4 digit acak
        return strtoupper($prefix."-" . $datePart . $randomPart);
    }
}

if(!function_exists('getContactToSelect')){
    function getContactToSelect($type = 'pelanggan'){
        $data = Contact::where('status', true)->where('type',$type)->get();
        $contacts = [];

        foreach ($data as $key => $c) {
            $contacts[$c->id] = $c->nama.' - '.$c->email;
        }
        return $contacts;
    }
}

if(!function_exists('getItemToSelect')){
    function getItemToSelect(){
        $data = Item::where('status', true)->get();
        $items = [];

        foreach ($data as $key => $c) {
            $items[$c->id] = $c->nama;
        }
        return $items;
    }
}

if(!function_exists('getUnitToSelect')){
    function getUnitToSelect($type = 'UNIT'){
        $data = Master::where('status', true)->where('type',$type)->get();
        $items = [];

        foreach ($data as $key => $c) {
            $items[$c->id] = $c->nama;
        }
        return $items;
    }
}

if(!function_exists('getUserToSelect')){
    function getUserToSelect(array $except = []){
        $data = User::where('active', true)->whereNotIn('id',$except)->get();
        $users = [];

        foreach ($data as $key => $c) {
            $users[$c->id] = $c->name.' - '.$c->email;
        }
        return $users;
    }
}

if(!function_exists('serializeform')){
    function serializeform($form){
        $forms = [];
        $dialog = [
            'width' => 2
        ];

        foreach ($form as $key => $f) {
            if($key == 'dialog'){
                $dialog = $f;
                continue;
            }
            $nf = $f;
            $nf['forms'] = fractal($f['forms'], new FormTransformer(), ArraySerializer::class);
            $forms[$key] = $nf;
        }

        return [
            'dialog' => $dialog,
            'sections' => $forms
        ];
    }
}