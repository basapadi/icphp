<?php

if (!function_exists('paymentStatusToOptions')) {
    function paymentStatusToOptions() {
        $configs = config('ihandcashier.payment_status');
        $result=[];
        foreach ($configs as $key => $c) {
            array_push($result, [
                'value' => $key,
                'label' => $c['label']
            ]);
        }

        return $result;
    }
}