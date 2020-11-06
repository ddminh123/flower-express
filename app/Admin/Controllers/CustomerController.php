<?php

namespace App\Admin\Controllers;

use App\Models\KiotVietCustomer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'KiotVietCustomer';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new KiotVietCustomer());

        $grid->column('_id', __(' id'));
        $grid->column('id', __('Id'));
        $grid->column('code', __('Code'));
        $grid->column('name', __('Name'));
        $grid->column('avatar', __('Avatar'));
        $grid->column('contactNumber', __('ContactNumber'));
        $grid->column('retailerId', __('RetailerId'));
        $grid->column('branchId', __('BranchId'));
        $grid->column('locationName', __('LocationName'));
        $grid->column('wardName', __('WardName'));
        $grid->column('email', __('Email'));
        $grid->column('modifiedDate', __('ModifiedDate'));
        $grid->column('createdDate', __('CreatedDate'));
        $grid->column('type', __('Type'));
        $grid->column('organization', __('Organization'));
        $grid->column('groups', __('Groups'));
        $grid->column('debt', __('Debt'));
        $grid->column('gender', __('Gender'));
        $grid->column('birthDate', __('BirthDate'));
        $grid->column('address', __('Address'));
        $grid->column('taxCode', __('TaxCode'));
        $grid->column('comments', __('Comments'));
        $grid->column('rewardPoint', __('RewardPoint'));
        $grid->column('totalInvoiced', __('TotalInvoiced'));
        $grid->column('totalPoint', __('TotalPoint'));
        $grid->column('totalRevenue', __('TotalRevenue'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(KiotVietCustomer::findOrFail($id));

        $show->field('_id', __(' id'));
        $show->field('id', __('Id'));
        $show->field('code', __('Code'));
        $show->field('name', __('Name'));
        $show->field('avatar', __('Avatar'));
        $show->field('contactNumber', __('ContactNumber'));
        $show->field('retailerId', __('RetailerId'));
        $show->field('branchId', __('BranchId'));
        $show->field('locationName', __('LocationName'));
        $show->field('wardName', __('WardName'));
        $show->field('email', __('Email'));
        $show->field('modifiedDate', __('ModifiedDate'));
        $show->field('createdDate', __('CreatedDate'));
        $show->field('type', __('Type'));
        $show->field('organization', __('Organization'));
        $show->field('groups', __('Groups'));
        $show->field('debt', __('Debt'));
        $show->field('gender', __('Gender'));
        $show->field('birthDate', __('BirthDate'));
        $show->field('address', __('Address'));
        $show->field('taxCode', __('TaxCode'));
        $show->field('comments', __('Comments'));
        $show->field('rewardPoint', __('RewardPoint'));
        $show->field('totalInvoiced', __('TotalInvoiced'));
        $show->field('totalPoint', __('TotalPoint'));
        $show->field('totalRevenue', __('TotalRevenue'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new KiotVietCustomer());

        $form->text('id', __('Id'));
        $form->text('code', __('Code'));
        $form->text('name', __('Name'));
        $form->image('avatar', __('Avatar'));
        $form->text('contactNumber', __('ContactNumber'));
        $form->text('retailerId', __('RetailerId'));
        $form->text('branchId', __('BranchId'));
        $form->text('locationName', __('LocationName'));
        $form->text('wardName', __('WardName'));
        $form->email('email', __('Email'));
        $form->text('modifiedDate', __('ModifiedDate'));
        $form->text('createdDate', __('CreatedDate'));
        $form->text('type', __('Type'));
        $form->text('organization', __('Organization'));
        $form->text('groups', __('Groups'));
        $form->text('debt', __('Debt'));
        $form->text('gender', __('Gender'));
        $form->text('birthDate', __('BirthDate'));
        $form->text('address', __('Address'));
        $form->text('taxCode', __('TaxCode'));
        $form->textarea('comments', __('Comments'));
        $form->text('rewardPoint', __('RewardPoint'));
        $form->text('totalInvoiced', __('TotalInvoiced'));
        $form->text('totalPoint', __('TotalPoint'));
        $form->text('totalRevenue', __('TotalRevenue'));

        return $form;
    }
}
