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
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

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

if (!function_exists('injectDataColumn')) {
    function injectDataColumn(&$data, array $replacements)
    {
        $convertToList = function (array $assoc) {
            $isAssoc = array_keys($assoc) !== range(0, count($assoc) - 1);
            if ($isAssoc) {
                return array_map(
                    function ($k, $v) {
                        if (!is_array($v)) {
                            $v = ['label' => $v]; // kalau string, bungkus jadi label
                        }
                        return array_merge(['value' => $k], $v);
                    },
                    array_keys($assoc),
                    $assoc
                );
            }
            return $assoc;
        };

        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if (is_array($value)) {
                    injectDataColumn($value, $replacements);
                } elseif (is_string($value) && str_starts_with($value, ':')) {
                    $placeholder = substr($value, 1);
                    if (array_key_exists($placeholder, $replacements)) {
                        $rep   = $replacements[$placeholder];
                        $value = is_array($rep) ? $convertToList($rep) : $rep;
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
        $data = Contact::where('contact_status', true)->where('type',$type)->get();
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

if(!function_exists('getTaxToSelect')){
    function getTaxToSelect(){
        $data = Master::where('status', true)->where('type','TAX')->get();
        $items = [];

        foreach ($data as $key => $c) {
            $items[$c->attributes->value] = $c->nama." ({$c->attributes->value}%)";
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

if (! function_exists('smart_dispatch')) {
    /***
    *  auto detect dispatch tergantung type appnya
    *  apabila type web maka Job bisa dijalankan kalo tidak jangan pake job (sync)
    ***/
    function smart_dispatch($job)
    {
        if (config('ihandcashier.app_type') === 'desktop') {
            // Kalau job implements ShouldQueue, kita bypass queue
            if ($job instanceof ShouldQueue) {
                // Jalankan langsung tanpa antrian
                $job->handle();
            } else {
                // Kalau bukan job queue, langsung eksekusi
                if (method_exists($job, 'handle')) {
                    $job->handle();
                }
            }
        } else {
            // Mode web: gunakan queue normal
            dispatch($job);
        }
    }
}

if (! function_exists('currency')) {
    function currency($value = 0){
        return 'IDR '.number_format($value, 0, ',', '.');
    }
}
if( !function_exists('formattedDate')){
    function formattedDate($date, $format = 'l, d M Y H:i'){
        return $date ? Carbon::parse($date)->locale('id')->translatedFormat($format) : null;
    }
}
