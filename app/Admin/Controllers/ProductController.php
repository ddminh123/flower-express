<?php

namespace App\Admin\Controllers;

use App\Models\KiotVietProduct;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'KiotVietProduct';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new KiotVietProduct());

        $grid->column('_id', __(' id'));
        $grid->column('id', __('Id'));
        $grid->column('code', __('Code'));
        $grid->column('fullName', __('FullName'));
        $grid->column('type', __('Type'));
        $grid->column('retailerId', __('RetailerId'));
        $grid->column('allowsSale', __('AllowsSale'));
        $grid->column('name', __('Name'));
        $grid->column('categoryId', __('CategoryId'));
        $grid->column('categoryName', __('CategoryName'));
        $grid->column('description', __('Description'));
        $grid->column('hasVariants', __('HasVariants'));
        $grid->column('attributes', __('Attributes'));
        $grid->column('unit', __('Unit'));
        $grid->column('masterUnitId', __('MasterUnitId'));
        $grid->column('masterProductId', __('MasterProductId'));
        $grid->column('conversionValue', __('ConversionValue'));
        $grid->column('units', __('Units'));
        $grid->column('images', __('Images'));
        $grid->column('inventories', __('Inventories'));
        $grid->column('priceBooks', __('PriceBooks'));
        $grid->column('productFormulas', __('ProductFormulas'));
        $grid->column('basePrice', __('BasePrice'));
        $grid->column('weight', __('Weight'));
        $grid->column('modifiedDate', __('ModifiedDate'));
        $grid->column('createdDate', __('CreatedDate'));
        $grid->column('orderTemplate', __('OrderTemplate'));
        $grid->column('minQuantity', __('MinQuantity'));
        $grid->column('maxQuantity', __('MaxQuantity'));
        $grid->column('isActive', __('IsActive'));
        $grid->column('isLotSerialControl', __('IsLotSerialControl'));
        $grid->column('isBatchExpireControl', __('IsBatchExpireControl'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('OnHand', __('OnHand'));

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
        $show = new Show(KiotVietProduct::findOrFail($id));

        $show->field('_id', __(' id'));
        $show->field('id', __('Id'));
        $show->field('code', __('Code'));
        $show->field('fullName', __('FullName'));
        $show->field('type', __('Type'));
        $show->field('retailerId', __('RetailerId'));
        $show->field('allowsSale', __('AllowsSale'));
        $show->field('name', __('Name'));
        $show->field('categoryId', __('CategoryId'));
        $show->field('categoryName', __('CategoryName'));
        $show->field('description', __('Description'));
        $show->field('hasVariants', __('HasVariants'));
        $show->field('attributes', __('Attributes'));
        $show->field('unit', __('Unit'));
        $show->field('masterUnitId', __('MasterUnitId'));
        $show->field('masterProductId', __('MasterProductId'));
        $show->field('conversionValue', __('ConversionValue'));
        $show->field('units', __('Units'));
        $show->field('images', __('Images'));
        $show->field('inventories', __('Inventories'));
        $show->field('priceBooks', __('PriceBooks'));
        $show->field('productFormulas', __('ProductFormulas'));
        $show->field('basePrice', __('BasePrice'));
        $show->field('weight', __('Weight'));
        $show->field('modifiedDate', __('ModifiedDate'));
        $show->field('createdDate', __('CreatedDate'));
        $show->field('orderTemplate', __('OrderTemplate'));
        $show->field('minQuantity', __('MinQuantity'));
        $show->field('maxQuantity', __('MaxQuantity'));
        $show->field('isActive', __('IsActive'));
        $show->field('isLotSerialControl', __('IsLotSerialControl'));
        $show->field('isBatchExpireControl', __('IsBatchExpireControl'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('OnHand', __('OnHand'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new KiotVietProduct());

        $form->text('id', __('Id'));
        $form->text('code', __('Code'));
        $form->text('fullName', __('FullName'));
        $form->text('type', __('Type'));
        $form->text('retailerId', __('RetailerId'));
        $form->text('allowsSale', __('AllowsSale'));
        $form->text('name', __('Name'));
        $form->text('categoryId', __('CategoryId'));
        $form->text('categoryName', __('CategoryName'));
        $form->textarea('description', __('Description'));
        $form->text('hasVariants', __('HasVariants'));
        $form->textarea('attributes', __('Attributes'));
        $form->text('unit', __('Unit'));
        $form->text('masterUnitId', __('MasterUnitId'));
        $form->text('masterProductId', __('MasterProductId'));
        $form->text('conversionValue', __('ConversionValue'));
        $form->textarea('units', __('Units'));
        $form->textarea('images', __('Images'));
        $form->textarea('inventories', __('Inventories'));
        $form->textarea('priceBooks', __('PriceBooks'));
        $form->textarea('productFormulas', __('ProductFormulas'));
        $form->text('basePrice', __('BasePrice'));
        $form->text('weight', __('Weight'));
        $form->text('modifiedDate', __('ModifiedDate'));
        $form->text('createdDate', __('CreatedDate'));
        $form->text('orderTemplate', __('OrderTemplate'));
        $form->text('minQuantity', __('MinQuantity'));
        $form->text('maxQuantity', __('MaxQuantity'));
        $form->text('isActive', __('IsActive'));
        $form->text('isLotSerialControl', __('IsLotSerialControl'));
        $form->text('isBatchExpireControl', __('IsBatchExpireControl'));
        $form->text('OnHand', __('OnHand'));

        return $form;
    }
}
