<?php

namespace App\Transformers;
use League\Fractal\TransformerAbstract;
class FormTransformer extends TransformerAbstract {

    public function transform($resp) {
        return [
            'name' => @$resp['name'],
            'id' => @$resp['id']??strtolower($resp['name']),
            'required' => @$resp['required'] ?? false,
            'label' => ucwords(implode(' ',explode('_',ucfirst($resp['label'])))),
            'type' => @$resp['type']??'text',
            'options' => @$resp['options'] ?? [],
            'show' => @$resp['show'] ?? true,
            'hint' => @$resp['hint']??'',
            'value' => @$resp['value']??'',
            'api'   => $resp['api']??null,
            'extension' => @$resp['extension']??'',
            'multiple' => @$resp['multiple']??false,
            'maxsize' => @$resp['maxsize']??0,
            'maxfile' => @$resp['maxfile']??0,
            'min' => @$resp['min']??0,
            'max' => @$resp['max']??null,
            'step' => @$resp['step']??1,
            'format' => @$resp['format']??'',
            'direction' => @$resp['direction']??'',
            'disabled' => @$resp['disabled']??false,
            'readonly' => @$resp['readonly']??false,
            'class'    => @$resp['class']??'',
            'editable' => @$resp['editable']??true
        ];
    }
}