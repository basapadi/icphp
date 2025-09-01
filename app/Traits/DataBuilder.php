<?php

namespace App\Traits;

use App\Objects\MergeData;
use Illuminate\Database\Eloquent\Builder;
use stdClass;
use Illuminate\Support\Collection;

trait DataBuilder
{
    private Collection $_mergeData;

    protected function setMergeData(array|MergeData $mergeData){
        if (!isset($this->_mergeData)) {
            $this->_mergeData = collect();
        }

        if (is_array($mergeData)) {
            $this->_mergeData->push($mergeData);
        } else {
            $this->_mergeData = collect($mergeData);
        }
    }

    /**
     * Mengatur data di merge ke data rows
     *
     * @return array
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function mergeData($data){
        $merged = null;
        if (is_array($this->_mergeData)) {
            foreach ($this->_mergeData as $key => $v){
                if($key == 0) {
                    $keys = $data->pluck($v['key']);
                    $merged = app($v['class'])::with($v['relations'])->whereIn($v['key'],$keys);
                } else {
                    $merged = app($v['class'])->with($v['relations']);
                    if(!empty($v['whereNotIn'])) $merged->whereNotIn($v['whereNotIn']['key'],$v['whereNotIn']['values']);
                    if(!empty($v['whereIn'])) $merged->whereIn($v['whereIn']['key'],$v['whereIn']['values']);
                }
            }
        } else {
            $v = $this->_mergeData;
            $keys = $data->pluck($v['key']);
           
            $merged = app($v['class'])::with($v['relations'])->whereIn($v['key'],$keys);
            if(!empty($v['whereNotIn'])) $merged->whereNotIn($v['whereNotIn']->key,$v['whereNotIn']->values);
            if(!empty($v['whereIn'])) $merged->whereIn($v['whereIn']->key,$v['whereIn']->values);
        }
        
        $merged = $merged->get();
        return $data->map(function($row)use ($merged){
            $row->{$this->_mergeData['attribute']} = $merged->where($this->_mergeData['key'],$row->{$this->_mergeData['key']})->values();
            return $row;
        });

        
    }
}