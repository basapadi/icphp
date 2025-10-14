<?php

namespace App\Http\Controllers;

use App\Models\{
    PurchaseInvoice,
    PurchaseInvoiceDetail,
};
use App\Objects\ContextMenu;
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;

class PurchaseInvoiceController extends BaseController
{
    private $_form = null;
    public function __construct(){
       $this->setModel(PurchaseInvoice::class)
            ->select(['trx_purchase_invoices.*'])
            ->with(['details','details.item','details.unit','contact','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_purchase_invoices.contact_id')->orderBy('tanggal','desc');
        $this->setModule('transaction.invoice.purchase');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');

        $this->setInjectDataColumn([
            'status' => ihandCashierConfigToSelect('purchase_invoice_status'),
            'status_pembayaran' => ihandCashierConfigToSelect('payment_status')
        ]);
    }
}