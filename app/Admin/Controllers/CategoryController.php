<?php

namespace App\Admin\Controllers;

use App\Models\KiotVietCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'KiotVietCategory';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new KiotVietCategory());

        $grid->column('id', __('Id'));
        $grid->column('categoryId', __('CategoryId'));
        $grid->column('parentId', __('ParentId'));
        $grid->column('categoryName', __('CategoryName'));
        $grid->column('retailerId', __('RetailerId'));
        $grid->column('hasChild', __('HasChild'));
        $grid->column('modifiedDate', __('ModifiedDate'));
        $grid->column('createdDate', __('CreatedDate'));
        $grid->column('isActive', __('IsActive'));
        $grid->column('rank', __('Rank'));
        $grid->column('children', __('Children'));
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
        $show = new Show(KiotVietCategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('categoryId', __('CategoryId'));
        $show->field('parentId', __('ParentId'));
        $show->field('categoryName', __('CategoryName'));
        $show->field('retailerId', __('RetailerId'));
        $show->field('hasChild', __('HasChild'));
        $show->field('modifiedDate', __('ModifiedDate'));
        $show->field('createdDate', __('CreatedDate'));
        $show->field('isActive', __('IsActive'));
        $show->field('rank', __('Rank'));
        $show->field('children', __('Children'));
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
        $form = new Form(new KiotVietCategory());

        $form->text('categoryId', __('CategoryId'));
        $form->text('parentId', __('ParentId'));
        $form->text('categoryName', __('CategoryName'));
        $form->text('retailerId', __('RetailerId'));
        $form->text('hasChild', __('HasChild'));
        $form->text('modifiedDate', __('ModifiedDate'));
        $form->text('createdDate', __('CreatedDate'));
        $form->text('isActive', __('IsActive'));
        $form->text('rank', __('Rank'));
        $form->textarea('children', __('Children'));

        return $form;
    }
}
