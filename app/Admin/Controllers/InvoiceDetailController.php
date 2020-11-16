<?php

namespace App\Admin\Controllers;

use App\InvoiceEnum;
use App\Models\KiotVietInvoiceDetail;
use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class InvoiceDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Điều phối đơn hàng';

    public function index(Content $content)
    {
        $shippers = User::query()->get();
        $qr = request('q', '');
        $shipper = request('shipper', '');
        $status = request('status', '');
        $delivery = request('delivery', '');

        $invoices = KiotVietInvoiceDetail::query()->with(['product', 'invoice', 'shipper'])->whereHas('invoice', function ($q) {
            return $q->whereNotIn('status',[2]);
        });
        if (!empty($q)) {
            $invoices = $invoices->whereHas('invoice', function ($q) use ($qr) {
                return $q->where('code', $qr);
            });
        }
        if (!empty($shipper)) {
            $invoices = $invoices->where('opsShipper', $shipper);
        }
        if (!empty($status)) {
            $invoices = $invoices->where('opsStatus', $status);
        }
        if (!empty($delivery)) {
            $invoices = $invoices->whereHas('invoice', function ($q) use ($delivery) {
                return $q->whereDate('expectedDelivery',$delivery);
            });
        }
        $invoices = $invoices->simplePaginate(20);
        return $content
            ->title('Điều phối')
            ->view('v2.ops', compact('invoices', 'shippers'));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        //totalPayment can update thuong xuyen
        $grid = new Grid(new KiotVietInvoiceDetail());

        $grid->model()->with(['invoice' => function ($qr) {
            return $qr->select('_id', 'code', 'invoiceDelivery', 'customerName', 'customerCode', 'total', 'totalPayment');
        }, 'product' => function ($qr) {
            return $qr->select('_id', 'code', 'images');
        }]);

        $grid->column('product.images', __('Images'))->image('', 50, 50);
        $grid->column('productCode', __('ProductCode'));
        $grid->column('productName', __('ProductName'))->width(150);
        $grid->column('invoice.code', __('InvoiceCode'));
        $grid->column('quantity', __('Quantity'));
        $grid->column('invoice.total', __('Total'));
        $grid->column('invoice.totalPayment', __('TotalPayment'));
        $grid->column('opsStatus', __('Status'))->using([
            0 => 'Chưa làm',
            1 => 'Đã nhận',
            2 => 'Làm xong',
            3 => 'Đã gửi ảnh',
            4 => 'Khách ok',
            5 => 'Đang giao',
            6 => 'Giao thành công',
        ])->filter([
            0 => 'Chưa làm',
            1 => 'Đã nhận',
            2 => 'Làm xong',
            3 => 'Đã gửi ảnh',
            4 => 'Khách ok',
            5 => 'Đang giao',
            6 => 'Giao thành công',
        ])->editable('select', [
            0 => 'Chưa làm',
            1 => 'Đã nhận',
            2 => 'Làm xong',
            3 => 'Đã gửi ảnh',
            4 => 'Khách ok',
            5 => 'Đang giao',
            6 => 'Giao thành công',
        ]);

        $grid->column('invoice.invoiceDelivery', __('Delivery'))->view('delivery');
//        $grid->column('opsNote', __('Note'))->editable();
        $listShippers = Administrator::query()->whereHas('roles', function ($query) {
            $query->whereIn('name', ['shipper']);
        })->pluck('name', 'id')->toArray();
        $grid->column('opsShipper', __('Shipper'))->editable('select', $listShippers);

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->disableExport();
        $grid->disableColumnSelector();

        $grid->filter(function ($filter) {

            // Remove the default id filter
            $filter->disableIdFilter();
            $filter->expand();
            $filter->scope('today', 'Hôm nay')->whereDate('created_at', date('Y-m-d'))
                ->orWhere('updated_at', date('Y-m-d'));
            $filter->scope('tomorrow', 'Ngày mai')->where('opsStatus', 1);
            $filter->scope('me', 'Của tôi')->where('opsStatus', 2);
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


        $form->select('opsStatus', __('Status'))->options(InvoiceEnum::getStatus());
        $form->select('opsShipper', __('Shipper'))->options(User::query()->pluck('name', 'id')->toArray());
        $form->multipleImage('opsImages', __('Images'));


        return $form;
    }
}
