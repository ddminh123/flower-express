<?php

namespace App\Admin\Controllers;

use App\Admin\Widgets\Invoice\Me;
use App\Admin\Widgets\Invoice\Normal;
use App\Admin\Widgets\Invoice\Today;
use App\Admin\Widgets\Invoice\Tomorrow;
use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class InvoiceController extends AdminController
{

    public function florist()
    {
        $time = request('time', 'all');
        $status = request('status', 'all');
        $q = request('q', '');

        $invoices = KiotVietInvoice::query()->select('_id','status', 'expectedDelivery')
            ->where('status', '!=', 2);
        if (in_array($time, ['today', 'tomorrow', 'me'])) {
            $invoices = $invoices->scopes($time);
        }
//        if (in_array($status, ['1', '2', '3'])) {
//            $invoices = $invoices->where('opsStatus', $status);
//        }
//        if (!empty($q)) {
//            $invoices = $invoices->where('productCode', $q)
//                ->orWhere('productName', 'like', '%' . $q . '%')
//                ->orWhereHas('invoice', function ($qr) use ($q) {
//                    return $qr->where('code', $q);
//                });
//        }
        $invoices = $invoices->orderByDesc('expectedDelivery')->simplePaginate(10);

        return view('v2.index', compact('invoices'));
    }

    public function shipper()
    {
        $time = request('time', 'all');
        $status = request('status', 'all');
        $q = request('q', '');

        $invoices = KiotVietInvoiceDetail::query()->where('opsShipper', Admin::user()->id)->with(['invoice', 'product']);
        if (in_array($time, ['today', 'tomorrow', 'me'])) {
            $invoices = $invoices->scopes($time);
        }
        if (in_array($status, ['1', '2', '3'])) {
            $invoices = $invoices->where('opsStatus', $status);
        }
        if (!empty($q)) {
            $invoices = $invoices->where('productCode', $q)
                ->orWhere('productName', 'like', '%' . $q . '%')
                ->orWhereHas('invoice', function ($qr) use ($q) {
                    return $qr->where('code', $q);
                });
        }
        $invoices = $invoices->paginate(10);
        return view('v2.appointments', compact('invoices'));
    }

    public function update($invoiceId)
    {

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new KiotVietInvoice());
        $grid->model()->orderByDesc('id');
//        $grid->column('_id', __(' id'));
//        $grid->column('id', __('Id'));
        $grid->column('code', __('Code'))->modal(function ($model) {

            $comments = $model->items->map(function ($comment) {
                return $comment->only(['productCode', 'productName', 'quantity', 'price', 'created_at']);
            });

            return new Table(['productCode', 'productName', 'quantity', 'price', 'created_at'], $comments->toArray());
        });
//        $grid->column('createdDate', __('CreatedDate'));
        $grid->filter(function ($filter) {
            $filter->between('createdDate')->datetime();
        });
        $grid->column('purchaseDate', __('PurchaseDate'));
//        $grid->column('branchId', __('BranchId'));
//        $grid->column('branchName', __('BranchName'));
//        $grid->column('soldById', __('SoldById'));
//        $grid->column('soldByName', __('SoldByName'));
//        $grid->column('customerId', __('CustomerId'));
//        $grid->column('customerCode', __('CustomerCode'));
        $grid->column('customerName', __('CustomerName'));
//        $grid->column('orderCode', __('OrderCode'));
//        $grid->column('total', __('Total'));
//        $grid->column('totalPayment', __('TotalPayment'));
//        $grid->column('status', __('Status'));
        $grid->column('statusValue', __('StatusValue'));
        $grid->column('description', __('Description'));
//        $grid->column('usingCod', __('UsingCod'));
//        $grid->column('modifiedDate', __('ModifiedDate'));
//        $grid->column('discount', __('Discount'));
//        $grid->column('saleChannelId', __('SaleChannelId'));
//        $grid->column('orderId', __('OrderId'));
//        $grid->column('payments', __('Payments'));
//        $grid->column('invoiceOrderSurcharges', __('InvoiceOrderSurcharges'));
//        $grid->column('invoiceDetails', __('InvoiceDetails'));
//        $grid->column('SaleChannel', __('SaleChannel'));
//        $grid->column('invoiceDelivery', __('InvoiceDelivery'));
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));

        $grid->disableActions();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(KiotVietInvoice::findOrFail($id));

        $show->field('_id', __(' id'));
        $show->field('id', __('Id'));
        $show->field('uuid', __('Uuid'));
        $show->field('code', __('Code'));
        $show->field('purchaseDate', __('PurchaseDate'));
        $show->field('branchId', __('BranchId'));
        $show->field('branchName', __('BranchName'));
        $show->field('soldById', __('SoldById'));
        $show->field('soldByName', __('SoldByName'));
        $show->field('customerId', __('CustomerId'));
        $show->field('customerCode', __('CustomerCode'));
        $show->field('customerName', __('CustomerName'));
        $show->field('orderCode', __('OrderCode'));
        $show->field('total', __('Total'));
        $show->field('totalPayment', __('TotalPayment'));
        $show->field('status', __('Status'));
        $show->field('statusValue', __('StatusValue'));
        $show->field('description', __('Description'));
        $show->field('usingCod', __('UsingCod'));
        $show->field('createdDate', __('CreatedDate'));
        $show->field('modifiedDate', __('ModifiedDate'));
        $show->field('discount', __('Discount'));
        $show->field('saleChannelId', __('SaleChannelId'));
        $show->field('orderId', __('OrderId'));
        $show->field('payments', __('Payments'));
        $show->field('invoiceOrderSurcharges', __('InvoiceOrderSurcharges'));
        $show->field('invoiceDetails', __('InvoiceDetails'));
        $show->field('SaleChannel', __('SaleChannel'));
        $show->field('invoiceDelivery', __('InvoiceDelivery'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('discountRatio', __('DiscountRatio'));
        $show->field('status_send', __('Status send'));
        $show->field('created_date', __('Created date'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new KiotVietInvoice());

        $form->text('id', __('Id'));
        $form->text('uuid', __('Uuid'));
        $form->text('code', __('Code'));
        $form->text('purchaseDate', __('PurchaseDate'));
        $form->text('branchId', __('BranchId'));
        $form->text('branchName', __('BranchName'));
        $form->text('soldById', __('SoldById'));
        $form->text('soldByName', __('SoldByName'));
        $form->text('customerId', __('CustomerId'));
        $form->text('customerCode', __('CustomerCode'));
        $form->text('customerName', __('CustomerName'));
        $form->text('orderCode', __('OrderCode'));
        $form->text('total', __('Total'));
        $form->text('totalPayment', __('TotalPayment'));
        $form->text('status', __('Status'));
        $form->text('statusValue', __('StatusValue'));
        $form->textarea('description', __('Description'));
        $form->text('usingCod', __('UsingCod'));
        $form->text('createdDate', __('CreatedDate'));
        $form->text('modifiedDate', __('ModifiedDate'));
        $form->text('discount', __('Discount'));
        $form->text('saleChannelId', __('SaleChannelId'));
        $form->text('orderId', __('OrderId'));
        $form->textarea('payments', __('Payments'));
        $form->textarea('invoiceOrderSurcharges', __('InvoiceOrderSurcharges'));
        $form->textarea('invoiceDetails', __('InvoiceDetails'));
        $form->textarea('SaleChannel', __('SaleChannel'));
        $form->textarea('invoiceDelivery', __('InvoiceDelivery'));
        $form->text('discountRatio', __('DiscountRatio'));
        $form->switch('status_send', __('Status send'));
        $form->text('created_date', __('Created date'));

        return $form;
    }
}
