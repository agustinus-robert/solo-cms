<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Http\Middleware\IsPozMiddleware;
use Modules\Poz\Http\Middleware\IsSupplierMiddleware;

Route::middleware('auth', IsSupplierMiddleware::class)->group(function () {
    Route::get('/supplier/dashboard', 'SupplierPosDashboardController@index')->name('supplier.dashboard');

    Route::prefix('supplierz')->namespace('Supplierz')->name('supplierz.')->group(function () {
        Route::prefix('reporting')->namespace('Reporting')->name('reporting.')->group(function () {
            Route::resourcePermission('/supplier/product_supplier_reporting', 'SupplierShiftController', 'supplier')->parameters(['prodsupreportings' => 'prodsupreporting'])->only('index');
            Route::get('/supplier/datatable_supplier_product', 'SupplierShiftController@getStockProducts')->name('reporting.supplier.product.datatables');

            Route::resourcePermission('/supplier/product_reporting', 'ProductReportingController', 'product')->parameters(['productreportings' => 'productreporting'])->only('index');
            Route::get('/supplier/datatable_report_product', 'ProductReportingController@getReportProducts')->name('reporting.product.datatables');
        });

        Route::get('quotation-send/{quotation}', 'QuotationController@send')->name('quotation.send');
        Route::resourcePermission('quotation', 'QuotationController', 'quotation')->parameters(['quotations' => 'quotation']);
        //datatables
        Route::get('datatable_adjustment', 'AdjustmentController@adjustmentTable')->name('adjustment.datatables');
        Route::get('datatable_quotation', 'QuotationController@quotationTable')->name('quotation.datatables');

        //Transaction
        Route::resourcePermission('adjustment', 'AdjustmentController', 'adjustment')
        ->only(['index', 'create', 'store'])->parameters(['adjustments' => 'adjustment']);
    });
});

Route::middleware('auth', 'append.outlet', IsPozMiddleware::class)->group(function () {
    //<!---------------------- Datatable ------------------------------>
    Route::prefix('transaction')->namespace('Transaction')->name('transaction.')->group(function () {
        Route::get('datatable_product', 'ProductController@productTable')->name('product.datatables');
        Route::get('datatable_sale', 'SaleController@saleTable')->name('sale.datatables');
        Route::get('purchaseTable', 'PurchaseController@purchaseTable')->name('purchase.datatables');
        Route::get('datatable_transfer', 'TransferController@transferTable')->name('transfer.datatables');
        Route::get('datatable_return', 'ReturnController@returnTable')->name('return.datatables');
        Route::get('datatable_adjustment', 'AdjustmentController@adjustmentTable')->name('adjustment.datatables');
        Route::get('datatable_quotation', 'QutationController@quotationTable')->name('quotation-transaction.datatables');
        Route::get('datatable_tiers', 'TierVariantController@tierTable')->name('tier.datatables');
        Route::get('datatable_promotions', 'PromotionController@promotionTable')->name('promotion.datatables');
    });

    Route::prefix('master')->namespace('Master')->name('master.')->group(function () {
        Route::get('datatable_brand', 'BrandController@brandTable')->name('brand.datatables');
        Route::get('datatable_category', 'CategoryController@categoryTable')->name('category.datatables');
        Route::get('datatable_tax', 'TaxRateController@taxTable')->name('tax.datatables');
        Route::get('datatable_unit', 'UnitController@unitTable')->name('unit.datatables');
        Route::get('datatable_warehouse', 'WarehouseController@unitTable')->name('warehouse.datatables');
        Route::get('datatable_outlet', 'OutletController@outletTable')->name('outlet.datatables');
        Route::get('datatable_casier', 'CasierController@casierTable')->name('casier.datatables');
        Route::get('datatable_supplier', 'SupplierController@supplierTable')->name('supplier.datatables');
        Route::get('datatable_tier', 'TierController@tierTable')->name('tier.datatables');
    });

    Route::prefix('reporting')->namespace('Reporting')->name('reporting.')->group(function () {
        Route::get('datatable_reporting_product', 'ProductReportingController@productReportTable')->name('reporting.product.datatables');
        Route::get('datatable_supplier_product', 'SupplierShiftController@getStockProducts')->name('reporting.supplier.product.datatables');
    });

    Route::prefix('schedule')->namespace('Schedule')->name('schedule.')->group(function(){
        Route::get('datatable_schedule_supplier', 'SupplierScheduleController@supplierScheduleTable')->name('schedule_supplier.datatables');
    });

    //<!---------------------- End Datatable ------------------------------>

    Route::get('/dashboard', 'DashboardPosController@index')->name('dashboard');
    Route::get('/processing', 'ProcessingPosAdminController@processing')->name('processing');

    Route::prefix('reporting')->namespace('Reporting')->name('reporting.')->group(function () {
        Route::resourcePermission('product_reporting', 'ProductReportingController', 'product')->parameters(['prodreportings' => 'prodreporting'])->only('index');
        Route::resourcePermission('product_supplier_reporting', 'SupplierShiftController', 'supplier')->parameters(['prodsupreportings' => 'prodsupreporting'])->only('index');
    });

    Route::prefix('master')->namespace('Master')->name('master.')->group(function () {
        Route::resourcePermission('brand', 'BrandController', 'brand')->parameters(['brands' => 'brand']);
        Route::resourcePermission('category', 'CategoryController', 'category')->parameters(['categorys' => 'category']);
        Route::resourcePermission('tax', 'TaxRateController', 'taxrate')->parameters(['taxs' => 'tax']);
        Route::resourcePermission('unit', 'UnitController', 'unit')->parameters(['units' => 'unit']);
        Route::resourcePermission('warehouse', 'WarehouseController', 'warehouse')->parameters(['warehouses' => 'warehouse']);
        Route::resourcePermission('outlet', 'OutletController', 'outlet')->parameters(['outlets' => 'outlet']);
        Route::resourcePermission('casier', 'CasierController', 'casier')->parameters(['casiers' => 'casier']);
        Route::resourcePermission('supplier', 'SupplierController', 'supplier')->parameters(['suppliers' => 'supplier']);
        Route::resourcePermission('tier', 'TierController', 'tier')->parameters(['tiers' => 'tier']);
    });

    Route::prefix('schedule')->namespace('Schedule')->name('schedule.')->group(function(){
        Route::resourcePermission('supplier_schedule', 'SupplierScheduleController', 'supplier')->parameters(['suppliers_schedules' => 'suppliers_schedules']);
    });

    Route::prefix('transaction')->namespace('Transaction')->name('transaction.')->group(function () {
        Route::resourcePermission('product', 'ProductController', 'product')->parameters(['products' => 'product']);
        Route::resourcePermission('product-promotion', 'PromotionController', 'promotion')->parameters(['product-promotions' => 'product-promotion'])
        ->except(['show']);
        Route::resourcePermission('product-variant', 'ProductVariantController', 'product')->parameters(['product-variants' => 'product-variant'])->only([
            'show', 'store'
        ]);

        Route::resourcePermission('sale', 'SaleController', 'sale')->parameters(['sales' => 'sale']);
        Route::resourcePermission('qutation', 'QutationController', 'quotation')
            ->only(['index', 'show', 'update'])
            ->parameters(['qutations' => 'qutation']);

        Route::resourcePermission('tier-variant', 'TierVariantController', 'tier')->parameters(['tier-variants' => 'tier-variant']);
        Route::resourcePermission('purchase', 'PurchaseController', 'purchase')->parameters(['purchases' => 'purchase']);
        Route::resourcePermission('return', 'ReturnController', 'return')->parameters(['returns' => 'return']);
        Route::resourcePermission('adjustment', 'AdjustmentController', 'adjustment')->parameters(['adjustments' => 'adjustment']);
        Route::get('sale-pos-invoice/{sale_id}', 'SaleController@invoice')->name('sale.pos-invoice');
        Route::get('purchase_status/{purchase_id}/', 'PurchaseController@change_status')->name('purchase.purchase_status');
        Route::get('purchase-invoice/{purchase_id}', 'PurchaseController@invoice')->name('purchase.invoice');
        Route::resourcePermission('pos-sale', 'PosSaleController', 'sale')->parameters(['pos-sales' => 'pos-sale']);
        Route::resourcePermission('transfer', 'TransferController', 'transfer')->parameters(['transfers' => 'transfer']);
        Route::get('transfer_status/{transfer_id}/', 'TransferController@change_status')->name('transfer.purchase_status');
        Route::get('transfer-invoice/{transfer_id}', 'TransferController@invoice')->name('transfer.invoice');
        Route::post('cash-registers/update', 'CashRegisterController@update')->name('cash-registers.update');
        Route::post('cash-registers/open', 'CashRegisterController@open')->name('cash-registers.open');
        Route::post('cash-registers/close',  'CashRegisterController@close')->name('cash-registers.close');
    });
});
