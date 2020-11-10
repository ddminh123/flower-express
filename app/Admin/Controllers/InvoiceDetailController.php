<?php

namespace App\Admin\Controllers;

use App\Models\KiotVietInvoiceDetail;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class InvoiceDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Điều phối đơn hàng';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new KiotVietInvoiceDetail());

        $grid->model()->with(['invoice' => function($qr) {
            return $qr->select('_id','code','invoiceDelivery', 'customerName', 'customerCode');
        },'product' => function($qr) {
            return $qr->select('_id', 'code', 'images');
        }]);

        $grid->column('product.images', __('Images'))->image('',50,50);
        $grid->column('productCode', __('ProductCode'));
        $grid->column('productName', __('ProductName'))->width(150);
        $grid->column('invoice.code', __('InvoiceCode'));
        $grid->column('quantity', __('Quantity'));
        $grid->column('price', __('Price'));
        $grid->column('opsStatus', __('Status'))->using([
            0 => '<label class="label label-danger">Chưa làm</label>',
            1 => '<label class="label label-warning">Đang làm</label>',
            2 => '<label class="label label-success">Đã xong</label>',
        ])->filter([
            0 => 'Chưa làm',
            1 => 'Đang làm',
            2 => 'Đã xong',
        ]);

        $grid->column('invoice.invoiceDelivery', __('Delivery'))->view('delivery');
//        $grid->column('opsNote', __('Note'))->editable();
        $listShippers = User::query()->whereHas('roles',  function ($query) {
            $query->whereIn('name', ['shipper']);
        })->pluck('name','id')->toArray();
        $grid->column('opsShipper', __('Shipper'))->editable('select', $listShippers);

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->disableExport();
        $grid->disableColumnSelector();

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
            $filter->expand();
            $filter->scope('today','Hôm nay')->whereDate('created_at', date('Y-m-d'))
                ->orWhere('updated_at', date('Y-m-d'));
            $filter->scope('tomorrow','Ngày mai')->where('opsStatus', 1);
            $filter->scope('me','Của tôi')->where('opsStatus', 2);
        });


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
        $show = new Show(KiotVietInvoiceDetail::findOrFail($id));

        $show->field('_id', __(' id'));
        $show->field('_invoiceId', __(' invoiceId'));
        $show->field('invoiceId', __('InvoiceId'));
        $show->field('branch_id', __('Branch id'));
        $show->field('branch_name', __('Branch name'));
        $show->field('productId', __('ProductId'));
        $show->field('productCode', __('ProductCode'));
        $show->field('productName', __('ProductName'));
        $show->field('category_id', __('Category id'));
        $show->field('category_name', __('Category name'));
        $show->field('master_code', __('Master code'));
        $show->field('trade_mark_name', __('Trade mark name'));
        $show->field('quantity', __('Quantity'));
        $show->field('price', __('Price'));
        $show->field('discount', __('Discount'));
        $show->field('subTotal', __('SubTotal'));
        $show->field('opsNote', __('Note'));
        $show->field('opsStatus', __('Status'));
        $show->field('opsShipper', __('Shipper'));
        $show->field('serialNumbers', __('SerialNumbers'));
        $show->field('returnQuantity', __('ReturnQuantity'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('discountRatio', __('DiscountRatio'));
        $show->field('usePoint', __('UsePoint'));
        $show->field('ProductFormulaHistoryId', __('ProductFormulaHistoryId'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new KiotVietInvoiceDetail());

        $form->number('_invoiceId', __(' invoiceId'));
        $form->number('invoiceId', __('InvoiceId'));
        $form->text('branch_id', __('Branch id'));
        $form->text('branch_name', __('Branch name'));
        $form->text('productId', __('ProductId'));
        $form->text('productCode', __('ProductCode'));
        $form->text('productName', __('ProductName'));
        $form->text('category_id', __('Category id'));
        $form->text('category_name', __('Category name'));
        $form->text('master_code', __('Master code'));
        $form->text('trade_mark_name', __('Trade mark name'));
        $form->text('quantity', __('Quantity'));
        $form->text('price', __('Price'));
        $form->text('discount', __('Discount'));
        $form->text('subTotal', __('SubTotal'));
        $form->text('opsNote', __('Note'));
        $form->select('opsStatus', __('Status'));
        $form->select('opsShipper', __('Shipper'));
        $form->text('serialNumbers', __('SerialNumbers'));
        $form->text('returnQuantity', __('ReturnQuantity'));
        $form->text('discountRatio', __('DiscountRatio'));
        $form->text('usePoint', __('UsePoint'));
        $form->text('ProductFormulaHistoryId', __('ProductFormulaHistoryId'));

        return $form;
    }
}
