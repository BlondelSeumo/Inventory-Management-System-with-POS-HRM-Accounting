@extends('layout.top-head') @section('content')
@if($errors->has('phone_number'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('phone_number') }}</div>
@endif 
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<!-- Side Navbar -->
<nav class="side-navbar shrink">
    <div class="side-navbar-wrapper">
      <!-- Sidebar Header    -->
      <!-- Sidebar Navigation Menus-->
      <div class="main-menu">
        <ul id="side-main-menu" class="side-menu list-unstyled">                  
          <li><a href="{{url('/')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>
          <?php
            $role = DB::table('roles')->find(Auth::user()->role_id);
            $index_permission = DB::table('permissions')->where('name', 'products-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                ['permission_id', $index_permission->id],
                ['role_id', $role->id]
            ])->first();

            $print_barcode = DB::table('permissions')->where('name', 'print_barcode')->first();
                  $print_barcode_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $print_barcode->id],
                      ['role_id', $role->id]
                  ])->first();

              $stock_count = DB::table('permissions')->where('name', 'stock_count')->first();
                  $stock_count_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $stock_count->id],
                      ['role_id', $role->id]
                  ])->first();

                $adjustment = DB::table('permissions')->where('name', 'adjustment')->first();
                $adjustment_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $adjustment->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          
          <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-list"></i><span>{{__('file.product')}}</span><span></a>
            <ul id="product" class="collapse list-unstyled ">
              <li id="category-menu"><a href="{{route('category.index')}}">{{__('file.category')}}</a></li>
              @if($index_permission_active)
              <li id="product-list-menu"><a href="{{route('products.index')}}">{{__('file.product_list')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'products-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="product-create-menu"><a href="{{route('products.create')}}">{{__('file.add_product')}}</a></li>
              @endif
              @endif
              @if($print_barcode_active)
              <li id="printBarcode-menu"><a href="{{route('product.printBarcode')}}">{{__('file.print_barcode')}}</a></li>
              @endif
              @if($adjustment_active)
                <li id="adjustment-list-menu"><a href="{{route('qty_adjustment.index')}}">{{trans('file.Adjustment List')}}</a></li>
                <li id="adjustment-create-menu"><a href="{{route('qty_adjustment.create')}}">{{trans('file.Add Adjustment')}}</a></li>
              @endif
              @if($stock_count_active)
                <li id="stock-count-menu"><a href="{{route('stock-count.index')}}">{{trans('file.Stock Count')}}</a></li>
              @endif
            </ul>
          </li>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'purchases-index')->first();
              $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          @if($index_permission_active)
          <li><a href="#purchase" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-card"></i><span>{{trans('file.Purchase')}}</span></a>
            <ul id="purchase" class="collapse list-unstyled ">
              <li id="purchase-list-menu"><a href="{{route('purchases.index')}}">{{trans('file.Purchase List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'purchases-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="purchase-create-menu"><a href="{{route('purchases.create')}}">{{trans('file.Add Purchase')}}</a></li>
              <li id="purchase-import-menu"><a href="{{url('purchases/purchase_by_csv')}}">{{trans('file.Import Purchase By CSV')}}</a></li>
              @endif
            </ul>
          </li>
          @endif
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'sales-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $gift_card_permission = DB::table('permissions')->where('name', 'gift_card')->first();
            $gift_card_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $gift_card_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $coupon_permission = DB::table('permissions')->where('name', 'coupon')->first();
            $coupon_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $coupon_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          
          <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-cart"></i><span>{{trans('file.Sale')}}</span></a>
            <ul id="sale" class="collapse list-unstyled ">
              @if($index_permission_active)
              <li id="sale-list-menu"><a href="{{route('sales.index')}}">{{trans('file.Sale List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'sales-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li><a href="{{route('sale.pos')}}">POS</a></li>
              <li id="sale-create-menu"><a href="{{route('sales.create')}}">{{trans('file.Add Sale')}}</a></li>
              <li id="sale-import-menu"><a href="{{url('sales/sale_by_csv')}}">{{trans('file.Import Sale By CSV')}}</a></li>
              @endif
              @endif
              @if($gift_card_permission_active)
              <li id="gift-card-menu"><a href="{{route('gift_cards.index')}}">{{trans('file.Gift Card List')}}</a> </li>
              @endif
              @if($coupon_permission_active)
              <li id="coupon-menu"><a href="{{route('coupons.index')}}">{{trans('file.Coupon List')}}</a> </li>
              @endif
              <li id="delivery-menu"><a href="{{route('delivery.index')}}">{{trans('file.Delivery List')}}</a></li>
            </ul>
          </li>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'expenses-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          @if($index_permission_active)
          <li><a href="#expense" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-wallet"></i><span>{{trans('file.Expense')}}</span></a>
            <ul id="expense" class="collapse list-unstyled ">
              <li id="exp-cat-menu"><a href="{{route('expense_categories.index')}}">{{trans('file.Expense Category')}}</a></li>
              <li id="exp-list-menu"><a href="{{route('expenses.index')}}">{{trans('file.Expense List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'expenses-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li><a id="add-expense" href=""> {{trans('file.Add Expense')}}</a></li>
              @endif
            </ul>
          </li>
          @endif
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'quotes-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          @if($index_permission_active)
          <li><a href="#quotation" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document"></i><span>{{trans('file.Quotation')}}</span><span></a>
            <ul id="quotation" class="collapse list-unstyled ">
              <li id="quotation-list-menu"><a href="{{route('quotations.index')}}">{{trans('file.Quotation List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'quotes-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="quotation-create-menu"><a href="{{route('quotations.create')}}">{{trans('file.Add Quotation')}}</a></li>
              @endif
            </ul>
          </li>
          @endif
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'transfers-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          @if($index_permission_active)
          <li><a href="#transfer" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-export"></i><span>{{trans('file.Transfer')}}</span></a>
            <ul id="transfer" class="collapse list-unstyled ">
              <li id="transfer-list-menu"><a href="{{route('transfers.index')}}">{{trans('file.Transfer List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'transfers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="transfer-create-menu"><a href="{{route('transfers.create')}}">{{trans('file.Add Transfer')}}</a></li>
              <li id="transfer-import-menu"><a href="{{url('transfers/transfer_by_csv')}}">{{trans('file.Import Transfer By CSV')}}</a></li>
              @endif
            </ul>
          </li>
          @endif
          
          <li><a href="#return" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-archive"></i><span>{{trans('file.return')}}</span></a>
            <ul id="return" class="collapse list-unstyled ">
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'returns-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li id="sale-return-menu"><a href="{{route('return-sale.index')}}">{{trans('file.Sale')}}</a></li>
              @endif
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'purchase-return-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li id="purchase-return-menu"><a href="{{route('return-purchase.index')}}">{{trans('file.Purchase')}}</a></li>
              @endif
            </ul>
          </li>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'account-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $money_transfer_permission = DB::table('permissions')->where('name', 'money-transfer')->first();
            $money_transfer_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $money_transfer_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $balance_sheet_permission = DB::table('permissions')->where('name', 'balance-sheet')->first();
            $balance_sheet_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $balance_sheet_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $account_statement_permission = DB::table('permissions')->where('name', 'account-statement')->first();
            $account_statement_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $account_statement_permission->id],
                    ['role_id', $role->id]
                ])->first();

          ?>
          @if($index_permission_active || $balance_sheet_permission_active || $account_statement_permission_active)
          <li class=""><a href="#account" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-briefcase"></i><span>{{trans('file.Accounting')}}</span></a>
            <ul id="account" class="collapse list-unstyled ">
              @if($index_permission_active)
              <li id="account-list-menu"><a href="{{route('accounts.index')}}">{{trans('file.Account List')}}</a></li>
              <li><a id="add-account" href="">{{trans('file.Add Account')}}</a></li>
              @endif
              @if($money_transfer_permission_active)
              <li id="money-transfer-menu"><a href="{{route('money-transfers.index')}}">{{trans('file.Money Transfer')}}</a></li>
              @endif
              @if($balance_sheet_permission_active)
              <li id="balance-sheet-menu"><a href="{{route('accounts.balancesheet')}}">{{trans('file.Balance Sheet')}}</a></li>
              @endif
              @if($account_statement_permission_active)
              <li id="account-statement-menu"><a id="account-statement" href="">{{trans('file.Account Statement')}}</a></li>
              @endif
            </ul>
          </li>
          @endif
          <?php 
            $department = DB::table('permissions')->where('name', 'department')->first();
            $department_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $department->id],
                    ['role_id', $role->id]
                ])->first();
            $index_employee = DB::table('permissions')->where('name', 'employees-index')->first();
            $index_employee_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_employee->id],
                    ['role_id', $role->id]
                ])->first();
            $attendance = DB::table('permissions')->where('name', 'attendance')->first();
            $attendance_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $attendance->id],
                    ['role_id', $role->id]
                ])->first();
            $payroll = DB::table('permissions')->where('name', 'payroll')->first();
            $payroll_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $payroll->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          
          <li class=""><a href="#hrm" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user-group"></i><span>HRM</span></a>
            <ul id="hrm" class="collapse list-unstyled ">
              @if($department_active)
              <li id="dept-menu"><a href="{{route('departments.index')}}">{{trans('file.Department')}}</a></li>
              @endif
              @if($index_employee_active)
              <li id="employee-menu"><a href="{{route('employees.index')}}">{{trans('file.Employee')}}</a></li>
              @endif
              @if($attendance_active)
              <li id="attendance-menu"><a href="{{route('attendance.index')}}">{{trans('file.Attendance')}}</a></li>
              @endif
              @if($payroll_active)
              <li id="payroll-menu"><a href="{{route('payroll.index')}}">{{trans('file.Payroll')}}</a></li>
              @endif
              <li id="holiday-menu"><a href="{{route('holidays.index')}}">{{trans('file.Holiday')}}</a></li>
            </ul>
          </li>
          
          <li><a href="#people" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user"></i><span>{{trans('file.People')}}</span></a>
            <ul id="people" class="collapse list-unstyled ">
              <?php $index_permission_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'users-index'],
                      ['role_id', $role->id] ])->first();
              ?>
              @if($index_permission_active)
              <li id="user-list-menu"><a href="{{route('user.index')}}">{{trans('file.User List')}}</a></li>
              <?php $add_permission_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'users-add'],
                      ['role_id', $role->id] ])->first();
              ?>
              @if($add_permission_active)
              <li id="user-create-menu"><a href="{{route('user.create')}}">{{trans('file.Add User')}}</a></li>
              @endif
              @endif
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'customers-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li id="customer-list-menu"><a href="{{route('customer.index')}}">{{trans('file.Customer List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'customers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="customer-create-menu"><a href="{{route('customer.create')}}">{{trans('file.Add Customer')}}</a></li>
              @endif
              @endif
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'billers-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li id="biller-list-menu"><a href="{{route('biller.index')}}">{{trans('file.Biller List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'billers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="biller-create-menu"><a href="{{route('biller.create')}}">{{trans('file.Add Biller')}}</a></li>
              @endif
              @endif
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'suppliers-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li id="supplier-list-menu"><a href="{{route('supplier.index')}}">{{trans('file.Supplier List')}}</a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'suppliers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              @if($add_permission_active)
              <li id="supplier-create-menu"><a href="{{route('supplier.create')}}">{{trans('file.Add Supplier')}}</a></li>
              @endif
              @endif
            </ul>
          </li>
          <li><a href="#report" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document-remove"></i><span>{{trans('file.Reports')}}</span></a>
            <?php
              $profit_loss_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'profit-loss'],
                      ['role_id', $role->id] ])->first();
              $best_seller_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'best-seller'],
                      ['role_id', $role->id] ])->first();
              $warehouse_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'warehouse-report'],
                      ['role_id', $role->id] ])->first();
              $warehouse_stock_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'warehouse-stock-report'],
                      ['role_id', $role->id] ])->first();
              $product_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'product-report'],
                      ['role_id', $role->id] ])->first();
              $daily_sale_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'daily-sale'],
                      ['role_id', $role->id] ])->first();
              $monthly_sale_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'monthly-sale'],
                      ['role_id', $role->id]])->first();
              $daily_purchase_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'daily-purchase'],
                      ['role_id', $role->id] ])->first();
              $monthly_purchase_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'monthly-purchase'],
                      ['role_id', $role->id] ])->first();
              $purchase_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'purchase-report'],
                      ['role_id', $role->id] ])->first();
              $sale_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'sale-report'],
                      ['role_id', $role->id] ])->first();
              $payment_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'payment-report'],
                      ['role_id', $role->id] ])->first();
              $product_qty_alert_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'product-qty-alert'],
                      ['role_id', $role->id] ])->first();
              $user_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'user-report'],
                      ['role_id', $role->id] ])->first();

              $customer_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'customer-report'],
                      ['role_id', $role->id] ])->first();
              $supplier_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'supplier-report'], 
                      ['role_id', $role->id] ])->first();
              $due_report_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'due-report'],
                      ['role_id', $role->id] ])->first();
            ?>
            <ul id="report" class="collapse list-unstyled ">
              @if($profit_loss_active)
              <li id="profit-loss-report-menu">
                {!! Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' => 'profitLoss-report-form']) !!}
                <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                <a id="profitLoss-link" href="">{{trans('file.Summary Report')}}</a>
                {!! Form::close() !!}
              </li>
              @endif
              @if($best_seller_active)
              <li id="best-seller-report-menu">
                <a href="{{url('report/best_seller')}}">{{trans('file.Best Seller')}}</a>
              </li>
              @endif
              @if($product_report_active)
              <li id="product-report-menu">
                {!! Form::open(['route' => 'report.product', 'method' => 'post', 'id' => 'product-report-form']) !!}
                <input type="hidden" name="start_date" value="1988-04-18" />
                <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                <input type="hidden" name="warehouse_id" value="0" />
                <a id="report-link" href="">{{trans('file.Product Report')}}</a>
                {!! Form::close() !!}
              </li>
              @endif
              @if($daily_sale_active)
              <li id="daily-sale-report-menu">
                <a href="{{url('report/daily_sale/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Sale')}}</a>
              </li>
              @endif
              @if($monthly_sale_active)
              <li id="monthly-sale-report-menu">
                <a href="{{url('report/monthly_sale/'.date('Y'))}}">{{trans('file.Monthly Sale')}}</a>
              </li>
              @endif
              @if($daily_purchase_active)
              <li id="daily-purchase-report-menu">
                <a href="{{url('report/daily_purchase/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Purchase')}}</a>
              </li>
              @endif
              @if($monthly_purchase_active)
              <li id="monthly-purchase-report-menu">
                <a href="{{url('report/monthly_purchase/'.date('Y'))}}">{{trans('file.Monthly Purchase')}}</a>
              </li>
              @endif
              @if($sale_report_active)
              <li id="sale-report-menu">
                {!! Form::open(['route' => 'report.sale', 'method' => 'post', 'id' => 'sale-report-form']) !!}
                <input type="hidden" name="start_date" value="1988-04-18" />
                <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                <input type="hidden" name="warehouse_id" value="0" />
                <a id="sale-report-link" href="">{{trans('file.Sale Report')}}</a>
                {!! Form::close() !!}
              </li>
              @endif
              @if($payment_report_active)
              <li id="payment-report-menu">
                {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' => 'payment-report-form']) !!}
                <input type="hidden" name="start_date" value="1988-04-18" />
                <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                <a id="payment-report-link" href="">{{trans('file.Payment Report')}}</a>
                {!! Form::close() !!}
              </li>
              @endif
              @if($purchase_report_active)
              <li id="purchase-report-menu">
                {!! Form::open(['route' => 'report.purchase', 'method' => 'post', 'id' => 'purchase-report-form']) !!}
                <input type="hidden" name="start_date" value="1988-04-18" />
                <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                <input type="hidden" name="warehouse_id" value="0" />
                <a id="purchase-report-link" href="">{{trans('file.Purchase Report')}}</a>
                {!! Form::close() !!}
              </li>
              @endif
              @if($warehouse_report_active)
              <li id="warehouse-report-menu">
                <a id="warehouse-report-link" href="">{{trans('file.Warehouse Report')}}</a>
              </li>
              @endif
              @if($warehouse_stock_report_active)
              <li id="warehouse-stock-report-menu">
                <a href="{{route('report.warehouseStock')}}">{{trans('file.Warehouse Stock Chart')}}</a>
              </li>
              @endif
              @if($product_qty_alert_active)
              <li id="qtyAlert-report-menu">
                <a href="{{route('report.qtyAlert')}}">{{trans('file.Product Quantity Alert')}}</a>
              </li>
              @endif
              @if($user_report_active)
              <li id="user-report-menu">
                <a id="user-report-link" href="">{{trans('file.User Report')}}</a>
              </li>
              @endif
              @if($customer_report_active)
              <li id="customer-report-menu">
                <a id="customer-report-link" href="">{{trans('file.Customer Report')}}</a>
              </li>
              @endif
              @if($supplier_report_active)
              <li id="supplier-report-menu">
                <a id="supplier-report-link" href="">{{trans('file.Supplier Report')}}</a>
              </li>
              @endif
              @if($due_report_active)
              <li id="due-report-menu">
                {!! Form::open(['route' => 'report.dueByDate', 'method' => 'post', 'id' => 'due-report-form']) !!}
                <input type="hidden" name="start_date" value="1988-04-18" />
                <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                <a id="due-report-link" href="">{{trans('file.Due Report')}}</a>
                {!! Form::close() !!}
              </li>
              @endif
            </ul>
          </li>
          <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
            <ul id="setting" class="collapse list-unstyled ">
              <?php

                  $warehouse_permission = DB::table('permissions')->where('name', 'warehouse')->first();
                  $warehouse_permission_active = DB::table('role_has_permissions')->where([
                              ['permission_id', $warehouse_permission->id],
                              ['role_id', $role->id]
                          ])->first();

                  $customer_group_permission = DB::table('permissions')->where('name', 'customer_group')->first();
                  $customer_group_permission_active = DB::table('role_has_permissions')->where([
                              ['permission_id', $customer_group_permission->id],
                              ['role_id', $role->id]
                          ])->first();

                  $brand_permission = DB::table('permissions')->where('name', 'brand')->first();
                  $brand_permission_active = DB::table('role_has_permissions')->where([
                              ['permission_id', $brand_permission->id],
                              ['role_id', $role->id]
                          ])->first();

                  $unit_permission = DB::table('permissions')->where('name', 'unit')->first();
                  $unit_permission_active = DB::table('role_has_permissions')->where([
                              ['permission_id', $unit_permission->id],
                              ['role_id', $role->id]
                          ])->first();

                  $tax_permission = DB::table('permissions')->where('name', 'tax')->first();
                  $tax_permission_active = DB::table('role_has_permissions')->where([
                              ['permission_id', $tax_permission->id],
                              ['role_id', $role->id]
                          ])->first();

                  $general_setting_permission = DB::table('permissions')->where('name', 'general_setting')->first();
                  $general_setting_permission_active = DB::table('role_has_permissions')->where([
                              ['permission_id', $general_setting_permission->id],
                              ['role_id', $role->id]
                          ])->first();

                  $mail_setting_permission = DB::table('permissions')->where('name', 'mail_setting')->first();
                  $mail_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $mail_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $sms_setting_permission = DB::table('permissions')->where('name', 'sms_setting')->first();
                  $sms_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $sms_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $create_sms_permission = DB::table('permissions')->where('name', 'create_sms')->first();
                  $create_sms_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $create_sms_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $pos_setting_permission = DB::table('permissions')->where('name', 'pos_setting')->first();
                  $pos_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $pos_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $hrm_setting_permission = DB::table('permissions')->where('name', 'hrm_setting')->first();
                  $hrm_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $hrm_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();
              ?>
              @if($role->id <= 2)
              <li id="role-menu"><a href="{{route('role.index')}}">{{trans('file.Role Permission')}}</a></li>
              @endif
              @if($warehouse_permission_active)
              <li id="warehouse-menu"><a href="{{route('warehouse.index')}}">{{trans('file.Warehouse')}}</a></li>
              @endif
              @if($customer_group_permission_active)
              <li id="customer-group-menu"><a href="{{route('customer_group.index')}}">{{trans('file.Customer Group')}}</a></li>
              @endif
              @if($brand_permission_active)
              <li id="brand-menu"><a href="{{route('brand.index')}}">{{trans('file.Brand')}}</a></li>
              @endif
              @if($unit_permission_active)
              <li id="unit-menu"><a href="{{route('unit.index')}}">{{trans('file.Unit')}}</a></li>
              @endif
              @if($tax_permission_active)
              <li id="tax-menu"><a href="{{route('tax.index')}}">{{trans('file.Tax')}}</a></li>
              @endif
              <li id="user-menu"><a href="{{route('user.profile', ['id' => Auth::id()])}}">{{trans('file.User Profile')}}</a></li>
              @if($create_sms_permission_active)
              <li id="create-sms-menu"><a href="{{route('setting.createSms')}}">{{trans('file.Create SMS')}}</a></li>
              @endif
              @if($general_setting_permission_active)
              <li id="general-setting-menu"><a href="{{route('setting.general')}}">{{trans('file.General Setting')}}</a></li>
              @endif
              @if($mail_setting_permission_active)
              <li id="mail-setting-menu"><a href="{{route('setting.mail')}}">{{trans('file.Mail Setting')}}</a></li>
              @endif
              @if($sms_setting_permission_active)
              <li id="sms-setting-menu"><a href="{{route('setting.sms')}}">{{trans('file.SMS Setting')}}</a></li>
              @endif
              @if($pos_setting_permission_active)
              <li id="pos-setting-menu"><a href="{{route('setting.pos')}}">POS {{trans('file.settings')}}</a></li>
              @endif
              @if($hrm_setting_permission_active)
              <li id="hrm-setting-menu"><a href="{{route('setting.hrm')}}"> {{trans('file.HRM Setting')}}</a></li>
              @endif
            </ul>
          </li>
        </ul>
      </div>
    </div>
</nav>
<section class="forms pos-section">
    <div class="container-fluid">
        <div class="row">
            <audio id="mysoundclip1" preload="auto">
                <source src="{{url('public/beep/beep-timber.mp3')}}"></source>
            </audio>
            <audio id="mysoundclip2" preload="auto">
                <source src="{{url('public/beep/beep-07.mp3')}}"></source>
            </audio>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        {!! Form::open(['route' => 'sales.store', 'method' => 'post', 'files' => true, 'class' => 'payment-form']) !!}
                        @php
                            if($lims_pos_setting_data)
                                $keybord_active = $lims_pos_setting_data->keybord_active;
                            else
                                $keybord_active = 0;

                            $customer_active = DB::table('permissions')
                              ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                              ->where([
                                ['permissions.name', 'customers-add'],
                                ['role_id', \Auth::user()->role_id] ])->first();

                            if($lims_sale_data->coupon_id)
                                $lims_coupon_data = DB::table('coupons')->find($lims_sale_data->coupon_id);
                        @endphp
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($lims_pos_setting_data)
                                            <input type="hidden" name="warehouse_id_hidden" value="{{$lims_sale_data->warehouse_id}}">
                                            @endif
                                            <select required id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                                                @foreach($lims_warehouse_list as $warehouse)
                                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($lims_pos_setting_data)
                                            <input type="hidden" name="biller_id_hidden" value="{{$lims_sale_data->biller_id}}">
                                            @endif
                                            <select required id="biller_id" name="biller_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                            @foreach($lims_biller_list as $biller)
                                            <option value="{{$biller->id}}">{{$biller->name . ' (' . $biller->company_name . ')'}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($lims_pos_setting_data)
                                            <input type="hidden" name="customer_id_hidden" value="{{$lims_sale_data->customer_id}}">
                                            @endif
                                            <div class="input-group pos">
                                                <select required name="customer_id" id="customer_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select customer...">
                                                @foreach($lims_customer_list as $customer)
                                                    @php $deposit[$customer->id] = $customer->deposit - $customer->expense; @endphp
                                                    <option value="{{$customer->id}}">{{$customer->name . ' (' . $customer->phone_number . ')'}}</option>
                                                @endforeach
                                                </select>
                                                @if($customer_active)
                                                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addCustomer"><i class="dripicons-plus"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="search-box form-group">
                                            <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Scan/Search product by name/code" class="form-control" autofocus />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="table-responsive">
                                        <table id="myTable" class="table table-hover table-striped order-list table-fixed">
                                            <thead>
                                                <tr>
                                                    <th class="col-sm-4">{{trans('file.product')}}</th>
                                                    <th class="col-sm-2">{{trans('file.Price')}}</th>
                                                    <th class="col-sm-3">{{trans('file.Quantity')}}</th>
                                                    <th class="col-sm-2">{{trans('file.Subtotal')}}</th>
                                                    <th class="col-sm-1"><i class="dripicons-trash"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                $temp_unit_name = [];
                                                $temp_unit_operator = [];
                                                $temp_unit_operation_value = [];
                                            ?>
                                                @foreach($lims_product_sale_data as $product_sale)
                                                <tr>
                                                <?php 
                                                    $product_data = DB::table('products')->find($product_sale->product_id);
                                                    if($product_sale->variant_id) {
                                                        $product_variant_data = \App\ProductVariant::select('id', 'item_code')->FindExactProduct($product_data->id, $product_sale->variant_id)->first();
                                                        $product_data->code = $product_variant_data->item_code;
                                                    }
                                                    
                                                    if($product_data->tax_method == 1){
                                                        $product_price = $product_sale->net_unit_price + ($product_sale->discount / $product_sale->qty);
                                                    }
                                                    elseif ($product_data->tax_method == 2) {
                                                        $product_price =($product_sale->total / $product_sale->qty) + ($product_sale->discount / $product_sale->qty);
                                                    }

                                                    $tax = DB::table('taxes')->where('rate',$product_sale->tax_rate)->first();
                                                    $unit_name = array();
                                                    $unit_operator = array();
                                                    $unit_operation_value = array();
                                                    if($product_data->type == 'standard'){
                                                        $units = DB::table('units')->where('base_unit', $product_data->unit_id)->orWhere('id', $product_data->unit_id)->get();
                            
                                                        foreach($units as $unit) {
                                                            if($product_sale->sale_unit_id == $unit->id) {
                                                                array_unshift($unit_name, $unit->unit_name);
                                                                array_unshift($unit_operator, $unit->operator);
                                                                array_unshift($unit_operation_value, $unit->operation_value);
                                                            }
                                                            else {
                                                                $unit_name[]  = $unit->unit_name;
                                                                $unit_operator[] = $unit->operator;
                                                                $unit_operation_value[] = $unit->operation_value;
                                                            }
                                                        }

                                                        if($unit_operator[0] == '*'){
                                                            $product_price = $product_price / $unit_operation_value[0];
                                                        }
                                                        elseif($unit_operator[0] == '/'){
                                                            $product_price = $product_price * $unit_operation_value[0];
                                                        }
                                                    }
                                                    else {
                                                        $unit_name[] = 'n/a'. ',';
                                                        $unit_operator[] = 'n/a'. ',';
                                                        $unit_operation_value[] = 'n/a'. ',';
                                                    }
                                                    $temp_unit_name = $unit_name = implode(",",$unit_name) . ',';

                                                    $temp_unit_operator = $unit_operator = implode(",",$unit_operator) .',';

                                                    $temp_unit_operation_value = $unit_operation_value =  implode(",",$unit_operation_value) . ',';
                                                ?>
                                                    <td class="col-sm-4 product-title"><strong>{{$product_data->name}}</strong> [{{$product_data->code}}] <button type="button" class="edit-product btn btn-link" data-toggle="modal" data-target="#editModal"> <i class="dripicons-document-edit"></i></button> </td>
                                                    <td class="col-sm-2 product-price">{{ number_format((float)($product_sale->total / $product_sale->qty), 2, '.', '') }}</td>
                                                    <td class="col-sm-3"><div class="input-group"><span class="input-group-btn"><button type="button" class="btn btn-default minus"><span class="dripicons-minus"></span></button></span><input type="text" name="qty[]" class="form-control qty numkey input-number" value="{{$product_sale->qty}}" step="any" required><span class="input-group-btn"><button type="button" class="btn btn-default plus"><span class="dripicons-plus"></span></button></span></div></td>
                                                    <td class="col-sm-2 sub-total">{{ number_format((float)$product_sale->total, 2, '.', '') }}</td>
                                                    <td class="col-sm-1"><button type="button" class="ibtnDel btn btn-danger btn-sm">X</button></td>
                                                    <input type="hidden" class="product-code" name="product_code[]" value="{{$product_data->code}}"/>
                                                    <input type="hidden" name="product_id[]" value="{{$product_data->id}}"/>
                                                    <input type="hidden" class="product_price" name="product_price[]" value="{{$product_price}}"/>
                                                    <input type="hidden" class="net_unit_price" name="net_unit_price[]" value="{{$product_sale->net_unit_price}}" />
                                                    <input type="hidden" class="discount-value" name="discount[]" value="{{$product_sale->discount}}" />
                                                    <input type="hidden" class="tax-rate" name="tax_rate[]" value="{{$product_sale->tax_rate}}"/>
                                                    @if($tax)
                                                    <input type="hidden" class="tax-name" value="{{$tax->name}}" />
                                                    @else
                                                    <input type="hidden" class="tax-name" value="No Tax" />
                                                    @endif
                                                    <input type="hidden" class="tax-method" value="{{$product_data->tax_method}}"/>
                                                    <input type="hidden" class="tax-value" name="tax[]" value="{{$product_sale->tax}}" />
                                                    <input type="hidden" class="total-discount" value="{{$product_sale->discount}}">
                                                    <input type="hidden" class="subtotal-value" name="subtotal[]" value="{{$product_sale->total}}" />
                                                    <input type="hidden" class="sale-unit" name="sale_unit[]" value="{{$unit_name}}"/>
                                                    <input type="hidden" class="sale-unit-operator" value="{{$unit_operator}}"/>
                                                    <input type="hidden" class="sale-unit-operation-value" value="{{$unit_operation_value}}"/>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="tfoot active">
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_qty" value="{{$lims_sale_data->total_qty}}" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_discount" value="{{$lims_sale_data->total_discount}}" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_tax" value="{{$lims_sale_data->total_tax}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_price" value="{{$lims_sale_data->total_price}}" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="item" value="{{$lims_sale_data->item}}" />
                                            <input type="hidden" name="order_tax" value="{{$lims_sale_data->order_tax}}" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="grand_total" value="{{$lims_sale_data->grand_total}}" />
                                            <input type="hidden" name="sale_status" value="1" />
                                            @if($lims_sale_data->coupon_id)
                                                @php  
                                                    $coupon = \App\Coupon::find($lims_sale_data->coupon_id)
                                                @endphp
                                                <input type="hidden" name="coupon_active" value="1">
                                            @else
                                                <input type="hidden" name="coupon_active">
                                            @endif
                                            <input type="hidden" name="coupon_id" value="{{$lims_sale_data->coupon_id}}">
                                            <input type="hidden" name="coupon_discount" value="{{$lims_sale_data->coupon_discount}}"/>
                                            <input type="hidden" name="pos" value="1" />
                                            <input type="hidden" name="sale_id" value="{{$lims_sale_data->id}}" />
                                            <input type="hidden" name="draft" value="1" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 totals" style="border-top: 2px solid #e4e6fc; padding-top: 10px;">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Items')}}</span><span id="item">{{$lims_sale_data->item}} ({{$lims_sale_data->total_qty}})</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Total')}}</span><span id="subtotal">{{number_format((float)$lims_sale_data->total_price, 2, '.', '')}}</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Discount')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-discount"> <i class="dripicons-document-edit"></i></button></span>
                                            @if($lims_sale_data->order_discount)
                                            <span id="discount">{{number_format((float)$lims_sale_data->order_discount, 2, '.', '')}}</span>
                                            @else
                                            <span id="discount">0.00</span>
                                            @endif
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Coupon')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#coupon-modal"><i class="dripicons-document-edit"></i></button></span>
                                            @if($lims_sale_data->coupon_discount)
                                              <span id="coupon-text">{{number_format((float)$lims_sale_data->coupon_discount, 2, '.', '')}}</span>
                                            @else
                                              <span id="coupon-text">0.00</span>
                                            @endif
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Tax')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-tax"><i class="dripicons-document-edit"></i></button></span>
                                            @if($lims_sale_data->order_tax)
                                              <span id="tax">{{number_format((float)$lims_sale_data->order_tax, 2, '.', '')}}</span>
                                            @else
                                              <span id="tax">0.00</span>
                                            @endif
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Shipping')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#shipping-cost-modal"><i class="dripicons-document-edit"></i></button></span>
                                            @if($lims_sale_data->shipping_cost)
                                              <span id="shipping-cost">{{number_format((float)$lims_sale_data->shipping_cost, 2, '.', '')}}</span>
                                            @else
                                              <span id="shipping-cost">0.00</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <table class="table table-bordered table-condensed totals">
                                        <tr>
                                            <td style="width:10%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.Items')}}</strong><br>
                                            <span id="item">{{$lims_sale_data->item}}( {{$lims_sale_data->total_qty}})</span>
                                            </td>
                                            <td style="width:15%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.Total')}}</strong><br>
                                            <span id="subtotal">{{number_format((float)($lims_sale_data->total_price), 2, '.', '')}}</span>
                                            </td>
                                            <td style="width:15%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.Discount')}}</strong>
                                                <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-discount"> <i class="dripicons-document-edit"></i></button><br>
                                                <span id="discount">{{number_format((float)$lims_sale_data->order_discount, 2, '.', '') }}</span>
                                            </td>
                                            <td style="width:15%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.Coupon')}}</strong>
                                                @if($lims_sale_data->coupon_id)
                                                <button type="button" class="btn btn-link btn-sm" disabled><i class="dripicons-document-edit"></i></button><br>
                                                @else
                                                <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#coupon-modal"><i class="dripicons-document-edit"></i></button><br>
                                                @endif
                                                <span id="coupon-text">{{number_format((float)$lims_sale_data->coupon_discount, 2, '.', '') }}</span>
                                            </td>
                                            <td style="width:15%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.Tax')}}</strong>
                                            <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-tax"><i class="dripicons-document-edit"></i></button><br>
                                            <span id="tax">{{number_format((float)$lims_sale_data->order_tax, 2, '.', '')}}</span>
                                            </td>
                                            <td style="width:15%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.Shipping')}}</strong>
                                                <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#shipping-cost-modal"><i class="dripicons-document-edit"></i></button><br>
                                                <span id="shipping-cost">{{number_format((float)($lims_sale_data->shipping_cost), 2, '.', '')}}</span>
                                            </td>
                                            <td style="width:15%; padding: 0 0 0 10px; color: #000;"><strong>{{trans('file.grand total')}}</strong><br>
                                            <span id="grand-total">{{number_format((float)($lims_sale_data->grand_total), 2, '.', '')}}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="column-5">
                                    <button style="background: #0066cc" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="credit-card-btn"><i class="fa fa-credit-card"></i> Card</button>   
                                </div>
                                <div class="column-5">
                                    <button style="background: #47d147" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cash-btn"><i class="fa fa-money"></i> Cash</button>
                                </div>
                                <div class="column-5">
                                    <button style="background-color: #6666ff" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="paypal-btn"><i class="fa fa-paypal"></i> Paypal</button>
                                </div>
                                <div class="column-5">
                                    <button style="background-color: #e28d02" type="button" class="btn btn-custom payment-btn" id="draft-btn"><i class="dripicons-flag"></i> Draft</button>
                                </div>
                                <div class="column-5">
                                    <button style="background-color: #163951" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cheque-btn"><i class="fa fa-money"></i> Cheque</button>
                                </div>
                                <div class="column-5">
                                    <button style="background-color: #800080" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="gift-card-btn"><i class="fa fa-credit-card"></i> GiftCard</button>
                                </div>
                                <div class="column-5">
                                    <button style="background-color: #7f4f01" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="deposit-btn"><i class="fa fa-university"></i> Deposit</button>
                                </div>
                                <div class="column-5">
                                    <button style="background-color: #cc0000;" type="button" class="btn btn-custom payment-btn" id="cancel-btn" onclick="return confirmCancel()"><i class="ion-android-cancel"></i> Cancel</button>
                                </div> -->
                            </div>
                        </div>                        
                    </div>
                    <div class="payment-amount">
                        <h2>{{trans('file.grand total')}} <span id="grand-total">{{number_format((float)$lims_sale_data->grand_total, 2, '.', '')}}</span></h2>
                    </div>
                    <div class="payment-options">
                        <div class="column-5">
                            <button style="background: #0984e3" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="credit-card-btn"><i class="fa fa-credit-card"></i> Card</button>   
                        </div>
                        <div class="column-5">
                            <button style="background: #00cec9" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cash-btn"><i class="fa fa-money"></i> Cash</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #213170" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="paypal-btn"><i class="fa fa-paypal"></i> Paypal</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #e28d02" type="button" class="btn btn-custom" id="draft-btn"><i class="dripicons-flag"></i> Draft</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #fd7272" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cheque-btn"><i class="fa fa-money"></i> Cheque</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #5f27cd" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="gift-card-btn"><i class="fa fa-credit-card-alt"></i> GiftCard</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #b33771" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="deposit-btn"><i class="fa fa-university"></i> Deposit</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #d63031;" type="button" class="btn btn-custom" id="cancel-btn" onclick="return confirmCancel()"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!-- order_discount modal -->
            <div id="order-discount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{trans('file.Order Discount')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="order_discount" class="form-control numkey" step="any" value="{{number_format((float)$lims_sale_data->order_discount, 2, '.', '')}}">
                            </div>
                            <button type="button" name="order_discount_btn" class="btn btn-primary" data-dismiss="modal">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- coupon modal -->
            <div id="coupon-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{trans('file.Coupon Code')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                @if($lims_sale_data->coupon_id)
                                    <input type="text" id="coupon-code" class="form-control" placeholder="Type Coupon Code..." value="{{$coupon->code}}" disabled>
                                @else
                                    <input type="text" id="coupon-code" class="form-control" placeholder="Type Coupon Code...">
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary coupon-check" data-dismiss="modal">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- order_tax modal -->
            <div id="order-tax" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{trans('file.Order Tax')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="order_tax_rate_hidden" value="{{$lims_sale_data->order_tax_rate}}">
                                <select class="form-control" name="order_tax_rate">
                                    <option value="0">No Tax</option>
                                    @foreach($lims_tax_list as $tax)
                                    <option value="{{$tax->rate}}">{{$tax->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" name="order_tax_btn" class="btn btn-primary" data-dismiss="modal">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- shipping_cost modal -->
            <div id="shipping-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{trans('file.Shipping Cost')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="shipping_cost" class="form-control numkey" value="{{number_format((float)($lims_sale_data->shipping_cost), 2, '.', '')}}" step="any">
                            </div>
                            <button type="button" name="shipping_cost_btn" class="btn btn-primary" data-dismiss="modal">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- payment modal -->
            <div id="add-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Finalize Sale')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>{{trans('file.Recieved Amount')}} *</label>
                                            <input type="text" name="paying_amount" class="form-control numkey"  step="any">
                                        </div>
                                        <div class="col-md-6">
                                            <label>{{trans('file.Paying Amount')}} *</label>
                                            <input type="text" name="paid_amount" class="form-control numkey"  step="any">
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <label>{{trans('file.Change')}} : </label>
                                            <p id="change" class="ml-2">0.00</p>
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <label>{{trans('file.Paid By')}}</label>
                                            <input type="hidden" name="paid_by_id">
                                            <select class="form-control payment-option">
                                                <option value="1">Cash</option>
                                                <option value="2">Gift Card</option>
                                                <option value="3">Credit Card</option>
                                                <option value="4">Cheque</option>
                                                <option value="5">Paypal</option>
                                                <option value="6">Deposit</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12 mt-3">
                                            <div class="card-element form-control">
                                            </div>
                                            <div class="card-errors" role="alert"></div>
                                        </div>
                                        <div class="form-group col-md-12" id="gift-card">
                                            <label> {{trans('file.Gift Card')}} *</label>
                                            <input type="hidden" name="gift_card_id">
                                            <select id="gift_card_id_select" name="gift_card_id_select" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Gift Card..."></select>
                                        </div>
                                        <div class="form-group col-md-12" id="cheque">
                                            <div class="form-group">
                                                <label>{{trans('file.Cheque Number')}} *</label>
                                                <input type="text" name="cheque_no" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>{{trans('file.Payment Note')}}</label>
                                            <textarea id="payment_note" rows="2" class="form-control" name="payment_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-6 form-group">
                                            <label>{{trans('file.Sale Note')}}</label>
                                            <textarea rows="3" class="form-control" name="sale_note"></textarea>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>{{trans('file.Staff Note')}}</label>
                                            <textarea rows="3" class="form-control" name="staff_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button id="submit-btn" type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                                    </div>
                                </div>
                                <div class="col-md-2 qc" data-initial="1">
                                    <h4><strong>{{trans('file.Quick Cash')}}</strong></h4>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="10" type="button">10</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="20" type="button">20</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="50" type="button">50</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="100" type="button">100</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="500" type="button">500</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="1000" type="button">1000</button>
                                    <button class="btn btn-block btn-danger qc-btn sound-btn" data-amount="0" type="button">{{trans('file.Clear')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <!-- product list -->
            <div class="col-md-6">
                <!-- navbar-->
                <header class="header">
                    <nav class="navbar">
                      <div class="container-fluid">
                        <div class="navbar-holder d-flex align-items-center justify-content-between">
                          <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>
                          <div class="navbar-header">
                          
                          <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                            <li class="nav-item"><a id="btnFullscreen" title="Full Screen"><i class="dripicons-expand"></i></a></li> 
                            <?php 
                                $general_setting_permission = DB::table('permissions')->where('name', 'general_setting')->first();
                                $general_setting_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $general_setting_permission->id],
                                            ['role_id', Auth::user()->role_id]
                                        ])->first();

                                $pos_setting_permission = DB::table('permissions')->where('name', 'pos_setting')->first();

                                $pos_setting_permission_active = DB::table('role_has_permissions')->where([
                                    ['permission_id', $pos_setting_permission->id],
                                    ['role_id', Auth::user()->role_id]
                                ])->first();
                            ?>
                            @if($pos_setting_permission_active)
                            <li class="nav-item"><a class="dropdown-item" href="{{route('setting.pos')}}"><i class="dripicons-gear"></i>  <span>{{trans('file.POS Setting')}}</span></a> </li>
                            @endif
                            @if($alert_product > 0)
                            <li class="nav-item">
                                  <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-bell"></i><span class="badge badge-danger">{{$alert_product}}</span>
                                      <span class="caret"></span>
                                      <span class="sr-only">Toggle Dropdown</span>
                                  </a>
                                  <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications" user="menu">
                                      <li class="notifications">
                                        <a href="{{route('report.qtyAlert')}}" class="btn btn-link">{{$alert_product}} product exceeds alert quantity</a>
                                      </li>
                                  </ul>
                            </li>
                            @endif
                            <li class="nav-item"> 
                                <a class="dropdown-item" href="{{ url('read_me') }}" target="_blank"><i class="dripicons-information"></i> {{trans('file.Help')}}</a>
                            </li>&nbsp;
                            <li class="nav-item">
                                  <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span>{{ucfirst(Auth::user()->name)}}</span> <i class="fa fa-angle-down"></i>
                                  </a>
                                  <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                      <li> 
                                        <a href="{{route('user.profile', ['id' => Auth::id()])}}"><i class="dripicons-user"></i> {{trans('file.profile')}}</a>
                                      </li>
                                      @if($general_setting_permission_active)
                                      <li> 
                                        <a href="{{route('setting.general')}}"><i class="dripicons-gear"></i> {{trans('file.settings')}}</a>
                                      </li>
                                      @endif
                                      <li> 
                                        <a href="{{url('my-transactions/'.date('Y').'/'.date('m'))}}"><i class="dripicons-swap"></i> {{trans('file.My Transaction')}}</a>
                                      </li>
                                      <li> 
                                        <a href="{{url('holidays/my-holiday/'.date('Y').'/'.date('m'))}}"><i class="dripicons-vibrate"></i> {{trans('file.My Holiday')}}</a>
                                      </li>
                                      <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();"><i class="dripicons-power"></i>
                                            {{trans('file.logout')}}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                      </li>
                                  </ul>
                            </li> 
                          </ul>
                        </div>
                      </div>
                    </nav>
                </header>
                <div class="filter-window">
                    <div class="category mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose category</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($lims_category_list as $category)
                            <div class="col-md-3 category-img text-center" data-category="{{$category->id}}">
                                @if($category->image)
                                    <img  src="{{url('public/images/category', $category->image)}}" />
                                @else
                                    <img  src="{{url('public/images/product/zummXD2dvAtI.png')}}" />
                                @endif
                                <p class="text-center">{{$category->name}}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="brand mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose brand</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($lims_brand_list as $brand)
                            @if($brand->image)
                                <div class="col-md-3 brand-img text-center" data-brand="{{$brand->id}}">
                                    <img  src="{{url('public/images/brand',$brand->image)}}" />
                                    <p class="text-center">{{$brand->title}}</p>
                                </div>
                            @else
                                <div class="col-md-3 brand-img" data-brand="{{$brand->id}}">
                                    <img  src="{{url('public/images/product/zummXD2dvAtI.png')}}" />
                                    <p class="text-center">{{$brand->title}}</p>
                                </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <button class="btn btn-block btn-primary" id="category-filter">{{trans('file.category')}}</button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-block btn-info" id="brand-filter">{{trans('file.Brand')}}</button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-block btn-danger" id="featured-filter">{{trans('file.Featured')}}</button>
                    </div>
                    <div class="col-md-12 mt-1 table-container">
                        <table id="product-table" class="table product-list">
                            <thead class="d-none">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @for ($i=0; $i < ceil($product_number/5); $i++)
                                <tr>
                                    <td class="product-img sound-btn" title="{{$lims_product_list[0+$i*5]->name}}" data-product ="{{$lims_product_list[0+$i*5]->code . ' (' . $lims_product_list[0+$i*5]->name . ')'}}"><img  src="{{url('public/images/product',$lims_product_list[0+$i*5]->base_image)}}" width="100%" />
                                        <p>{{$lims_product_list[0+$i*5]->name}}</p>
                                    </td>
                                    @if(!empty($lims_product_list[1+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[1+$i*5]->name}}" data-product ="{{$lims_product_list[1+$i*5]->code . ' (' . $lims_product_list[1+$i*5]->name . ')'}}"><img  src="{{url('public/images/product',$lims_product_list[1+$i*5]->base_image)}}" width="100%" />
                                        <p>{{$lims_product_list[1+$i*5]->name}}</p>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                    @if(!empty($lims_product_list[2+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[2+$i*5]->name}}" data-product ="{{$lims_product_list[2+$i*5]->code . ' (' . $lims_product_list[2+$i*5]->name . ')'}}"><img  src="{{url('public/images/product',$lims_product_list[2+$i*5]->base_image)}}" width="100%" />
                                        <p>{{$lims_product_list[2+$i*5]->name}}</p>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                    @if(!empty($lims_product_list[3+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[3+$i*5]->name}}" data-product ="{{$lims_product_list[3+$i*5]->code . ' (' . $lims_product_list[3+$i*5]->name . ')'}}"><img  src="{{url('public/images/product',$lims_product_list[3+$i*5]->base_image)}}" width="100%" />
                                        <p>{{$lims_product_list[3+$i*5]->name}}</p>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                    @if(!empty($lims_product_list[4+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[4+$i*5]->name}}" data-product ="{{$lims_product_list[4+$i*5]->code . ' (' . $lims_product_list[4+$i*5]->name . ')'}}"><img  src="{{url('public/images/product',$lims_product_list[4+$i*5]->base_image)}}" width="100%" />
                                        <p>{{$lims_product_list[4+$i*5]->name}}</p>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- product edit modal -->
            <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="modal_header" class="modal-title"></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label>{{trans('file.Quantity')}}</label>
                                    <input type="text" name="edit_qty" class="form-control numkey">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Unit Discount')}}</label>
                                    <input type="text" name="edit_discount" class="form-control numkey">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Unit Price')}}</label>
                                    <input type="text" name="edit_unit_price" class="form-control numkey" step="any">
                                </div>
                                <?php
                        $tax_name_all[] = 'No Tax';
                        $tax_rate_all[] = 0;
                        foreach($lims_tax_list as $tax) {
                            $tax_name_all[] = $tax->name;
                            $tax_rate_all[] = $tax->rate;
                        }
                    ?>
                                    <div class="form-group">
                                        <label>{{trans('file.Tax Rate')}}</label>
                                        <select name="edit_tax_rate" class="form-control selectpicker">
                                            @foreach($tax_name_all as $key => $name)
                                            <option value="{{$key}}">{{$name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="edit_unit" class="form-group">
                                        <label>{{trans('file.Product Unit')}}</label>
                                        <select name="edit_unit" class="form-control selectpicker">
                                        </select>
                                    </div>
                                    <button type="button" name="update_btn" class="btn btn-primary">{{trans('file.update')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- add customer modal -->
            <div id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    {!! Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]) !!}
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Customer')}}</h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="form-group">
                            <label>{{trans('file.Customer Group')}} *</strong> </label>
                            <select required class="form-control selectpicker" name="customer_group_id">
                                @foreach($lims_customer_group_all as $customer_group)
                                    <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.name')}} *</strong> </label>
                            <input type="text" name="name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.Email')}}</label>
                            <input type="text" name="email" placeholder="example@example.com" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.Phone Number')}} *</label>
                            <input type="text" name="phone_number" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.Address')}} *</label>
                            <input type="text" name="address" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.City')}} *</label>
                            <input type="text" name="city" required class="form-control">
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="pos" value="1">      
                          <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                    {{ Form::close() }}
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $("ul#sale").siblings('a').attr('aria-expanded','true');
    $("ul#sale").addClass("show");
    $("ul#sale li").eq(1).addClass("active");

    var public_key = <?php echo json_encode($lims_pos_setting_data->stripe_public_key) ?>;
    var valid;

// array data depend on warehouse
var lims_product_array = [];
var product_code = [];
var product_name = [];
var product_qty = [];
var product_type = [];
var product_id = [];
var product_list = [];
var qty_list = [];

// array data with selection
var product_price = [];
var product_discount = [];
var tax_rate = [];
var tax_name = [];
var tax_method = [];
var unit_name = [];
var unit_operator = [];
var unit_operation_value = [];
var gift_card_amount = [];
var gift_card_expense = [];

// temporary array
var temp_unit_name = [];
var temp_unit_operator = [];
var temp_unit_operation_value = [];

var deposit = <?php echo json_encode($deposit) ?>;
var product_row_number = <?php echo json_encode($lims_pos_setting_data->product_number) ?>;
var rowindex;
var customer_group_rate;
var row_product_price;
var pos;
var keyboard_active = <?php echo json_encode($keybord_active); ?>;
var role_id = <?php echo json_encode(\Auth::user()->role_id) ?>;
var coupon_list = <?php echo json_encode($lims_coupon_list) ?>;
var currency = <?php echo json_encode($currency) ?>;

if(role_id > 2) {
    $('#biller_id').addClass('d-none');
    $('#warehouse_id').addClass('d-none');
}

var rownumber = $('table.order-list tbody tr:last').index();
for(rowindex  =0; rowindex <= rownumber; rowindex++){
    product_price.push(parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product_price').val()));
    var total_discount = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.total-discount').val());
    var quantity = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val());
    product_discount.push((total_discount / quantity).toFixed(2));
    tax_rate.push(parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val()));
    tax_name.push($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-name').val());
    tax_method.push($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-method').val());
    temp_unit_name = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit').val().split(',');
    unit_name.push($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit').val());
    unit_operator.push($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit-operator').val());
    unit_operation_value.push($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit-operation-value').val());
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit').val(temp_unit_name[0]);
}

$('.selectpicker').selectpicker({
    style: 'btn-link',
});

if(keyboard_active==1){

    $("input.numkey:text").keyboard({
        usePreview: false,
        layout: 'custom',
        display: {
        'accept'  : '&#10004;',
        'cancel'  : '&#10006;'
        },
        customLayout : {
          'normal' : ['1 2 3', '4 5 6', '7 8 9','0 {dec} {bksp}','{clear} {cancel} {accept}']
        },
        restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
        preventPaste : true,  // prevent ctrl-v and right click
        autoAccept : true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active'
        },
    });

    $('input[type="text"]').keyboard({
        usePreview: false,
        autoAccept: true,
        autoAcceptOnEsc: true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active',
            // used when disabling the decimal button {dec}
            // when a decimal exists in the input area
            buttonDisabled: 'disabled'
        },
        change: function(e, keyboard) {
                keyboard.$el.val(keyboard.$preview.val())
                keyboard.$el.trigger('propertychange')        
              }
    });

    $('textarea').keyboard({
        usePreview: false,
        autoAccept: true,
        autoAcceptOnEsc: true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active',
            // used when disabling the decimal button {dec}
            // when a decimal exists in the input area
            buttonDisabled: 'disabled'
        },
        change: function(e, keyboard) {
                keyboard.$el.val(keyboard.$preview.val())
                keyboard.$el.trigger('propertychange')        
              }
    });

    $('#lims_productcodeSearch').keyboard().autocomplete().addAutocomplete({
        // add autocomplete window positioning
        // options here (using position utility)
        position: {
          of: '#lims_productcodeSearch',
          my: 'top+18px',
          at: 'center',
          collision: 'flip'
        }
    });
}

$('select[name=customer_id]').val($("input[name='customer_id_hidden']").val());
$('select[name=warehouse_id]').val($("input[name='warehouse_id_hidden']").val());
$('select[name=biller_id]').val($("input[name='biller_id_hidden']").val());
$('select[name=order_tax_rate]').val($("input[name='order_tax_rate_hidden']").val());
$('.selectpicker').selectpicker('refresh');

var id = $('select[name="customer_id"]').val();
$.get('../getcustomergroup/' + id, function(data) {
    customer_group_rate = (data / 100);
});

var id = $('select[name="warehouse_id"]').val();
$.get('../getproduct/' + id, function(data) {
    lims_product_array = [];
    product_code = data[0];
    product_name = data[1];
    product_qty = data[2];
    product_type = data[3];
    product_id = data[4];
    product_list = data[5];
    qty_list = data[6];
    product_warehouse_price = data[7];

    $.each(product_code, function(index) {
        lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
    });
});

if(keyboard_active==1){
    $('#lims_productcodeSearch').bind('keyboardChange', function (e, keyboard, el) {
        var customer_id = $('#customer_id').val();
        var warehouse_id = $('select[name="warehouse_id"]').val();
        temp_data = $('#lims_productcodeSearch').val();
        if(!customer_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Customer!');
        }
        else if(!warehouse_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Warehouse!');
        }
    });
}
else{
    $('#lims_productcodeSearch').on('input', function(){
        var customer_id = $('#customer_id').val();
        var warehouse_id = $('#warehouse_id').val();
        temp_data = $('#lims_productcodeSearch').val();
        if(!customer_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Customer!');
        }
        else if(!warehouse_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Warehouse!');
        }

    });
}

$('body').on('click', function(e){
    $('.filter-window').hide('slide', {direction: 'right'}, 'fast');
});

$('#category-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.category').show();
    $('.brand').hide();
});

$('.category-img').on('click', function(){
    var category_id = $(this).data('category');
    var brand_id = 0;

    $(".table-container").children().remove();
    $.get('../getproduct/' + category_id + '/' + brand_id, function(data) {
        var tableData = '<table id="product-table" class="table product-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';
        if (Object.keys(data).length != 0) {
            $.each(data['name'], function(index) {
                var product_info = data['code'][index]+' (' + data['name'][index] + ')';
                if(index % 5 == 0 && index != 0){
                    tableData += '</tr><tr><td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="../../public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p></td>';
                }
                else
                    tableData += '<td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="../../public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p></td>';
            });

            if(data['name'].length % 5){
                var number = 5 - (data['name'].length % 5);
                while(number > 0)
                {
                    tableData += '<td style="border:none;"></td>';
                    number--;
                }
            }

            tableData += '</tr></tbody></table>';
            $(".table-container").html(tableData);
            $('#product-table').DataTable( {
            "order": [],
            'pageLength': product_row_number,
             'language': {
                'paginate': {
                    'previous': '<i class="fa fa-angle-left"></i>',
                    'next': '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: 'tp'
            });
            $('table.product-list').hide();
            $('table.product-list').show(500);
        }
        else{
            tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
            $(".table-container").html(tableData);
        }
    });
});

$('#brand-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.brand').show();
    $('.category').hide();
});

$('.brand-img').on('click', function(){
    var brand_id = $(this).data('brand');
    var category_id = 0;

    $(".table-container").children().remove();
    $.get('../getproduct/' + category_id + '/' + brand_id, function(data) {
        var tableData = '<table id="product-table" class="table product-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';
        if (Object.keys(data).length != 0) {
            $.each(data['name'], function(index) {
                var product_info = data['code'][index]+' (' + data['name'][index] + ')';
                if(index % 5 == 0 && index != 0){
                    tableData += '</tr><tr><td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="../../public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p></td>';
                }
                else
                    tableData += '<td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="../../public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p></td>';
            });

            if(data['name'].length % 5){
                var number = 5 - (data['name'].length % 5);
                while(number > 0)
                {
                    tableData += '<td style="border:none;"></td>';
                    number--;
                }
            }

            tableData += '</tr></tbody></table>';
            $(".table-container").html(tableData);
            $('#product-table').DataTable( {
            "order": [],
            'pageLength': product_row_number,
             'language': {
                'paginate': {
                    'previous': '<i class="fa fa-angle-left"></i>',
                    'next': '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: 'tp'
            });
            $('table.product-list').hide();
            $('table.product-list').show(500);
        }
        else{
            tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
            $(".table-container").html(tableData);
        }
    });
});

$('#featured-filter').on('click', function(){
    $(".table-container").children().remove();
    $.get('../getfeatured', function(data) {
        var tableData = '<table id="product-table" class="table product-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';
        if (Object.keys(data).length != 0) {
            $.each(data['name'], function(index) {
                var product_info = data['code'][index]+' (' + data['name'][index] + ')';
                if(index % 5 == 0 && index != 0){
                    tableData += '</tr><tr><td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="../../public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p></td>';
                }
                else
                    tableData += '<td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="../../public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p></td>';
            });

            if(data['name'].length % 5){
                var number = 5 - (data['name'].length % 5);
                while(number > 0)
                {
                    tableData += '<td style="border:none;"></td>';
                    number--;
                }
            }

            tableData += '</tr></tbody></table>';
            $(".table-container").html(tableData);
            $('#product-table').DataTable( {
            "order": [],
            'pageLength': product_row_number,
             'language': {
                'paginate': {
                    'previous': '<i class="fa fa-angle-left"></i>',
                    'next': '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: 'tp'
            });
            $('table.product-list').hide();
            $('table.product-list').show(500);
        }
        else{
            tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
            $(".table-container").html(tableData);
        }
    });
});

$('select[name="customer_id"]').on('change', function() {
    var id = $(this).val();
    $.get('../getcustomergroup/' + id, function(data) {
        customer_group_rate = (data / 100);
    });
});

$('select[name="warehouse_id"]').on('change', function() {
    var id = $(this).val();
    $.get('../getproduct/' + id, function(data) {
        lims_product_array = [];
        product_code = data[0];
        product_name = data[1];
        product_qty = data[2];
        product_type = data[3];
        product_id = data[4];
        product_list = data[5];
        qty_list = data[6];
        product_warehouse_price = data[7];

        $.each(product_code, function(index) {
            lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
        });
    });
});

var lims_productcodeSearch = $('#lims_productcodeSearch');

lims_productcodeSearch.autocomplete({
    source: function(request, response) {
        var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
        response($.grep(lims_product_array, function(item) {
            return matcher.test(item);
        }));
    },
    response: function(event, ui) {
        if (ui.content.length == 1) {
            var data = ui.content[0].value;
            $(this).autocomplete( "close" );
            productSearch(data);
        }
    },
    select: function(event, ui) {
        var data = ui.item.value;
        productSearch(data);
    }
});

$('#myTable').keyboard({
        accepted : function(event, keyboard, el) {
            checkQuantity(el.value, true);
      }
    });

$("#myTable").on('click', '.plus', function() {
    rowindex = $(this).closest('tr').index();
    var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) + 1;
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
    checkQuantity(String(qty), true);
});

$("#myTable").on('click', '.minus', function() {
    rowindex = $(this).closest('tr').index();
    var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) - 1;
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
    checkQuantity(String(qty), true);
});

//Change quantity
$("#myTable").on('input', '.qty', function() {
    rowindex = $(this).closest('tr').index();
    checkQuantity($(this).val(), true);
});

$("#myTable").on('click', '.qty', function() {
    rowindex = $(this).closest('tr').index();
});

$(document).on('click', '.sound-btn', function() {
    var audio = $("#mysoundclip1")[0];
    audio.play();
});

$(document).on('click', '.product-img', function(){
    var customer_id = $('#customer_id').val();
    var warehouse_id = $('select[name="warehouse_id"]').val();
    if(!customer_id)
        alert('Please select Customer!');
    else if(!warehouse_id)
        alert('Please select Warehouse!');
    else{
        var data = $(this).data('product');
        data = data.split(" ");
        pos = product_code.indexOf(data[0]);
        if(pos < 0)
            alert('Product is not avaialable in the selected warehouse');
        else{
           productSearch(data[0]);
        }
    }
});

//Delete product
$("table.order-list tbody").on("click", ".ibtnDel", function(event) {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    rowindex = $(this).closest('tr').index();
    product_price.splice(rowindex, 1);
    product_discount.splice(rowindex, 1);
    tax_rate.splice(rowindex, 1);
    tax_name.splice(rowindex, 1);
    tax_method.splice(rowindex, 1);
    unit_name.splice(rowindex, 1);
    unit_operator.splice(rowindex, 1);
    unit_operation_value.splice(rowindex, 1);
    $(this).closest("tr").remove();
    calculateTotal();
});

//Edit product
$("table.order-list").on("click", ".edit-product", function() {
    rowindex = $(this).closest('tr').index();
    edit();
});

//Update product
$('button[name="update_btn"]').on("click", function() {
    var edit_discount = $('input[name="edit_discount"]').val();
    var edit_qty = $('input[name="edit_qty"]').val();
    var edit_unit_price = $('input[name="edit_unit_price"]').val();

    if (parseFloat(edit_discount) > parseFloat(edit_unit_price)) {
        alert('Invalid Discount Input!');
        return;
    }

    var tax_rate_all = <?php echo json_encode($tax_rate_all) ?>;

    tax_rate[rowindex] = parseFloat(tax_rate_all[$('select[name="edit_tax_rate"]').val()]);
    tax_name[rowindex] = $('select[name="edit_tax_rate"] option:selected').text();

    product_discount[rowindex] = $('input[name="edit_discount"]').val();
    if(product_type[pos] == 'standard'){
        var row_unit_operator = unit_operator[rowindex].slice(0, unit_operator[rowindex].indexOf(","));
        var row_unit_operation_value = unit_operation_value[rowindex].slice(0, unit_operation_value[rowindex].indexOf(","));
        if (row_unit_operator == '*') {
            product_price[rowindex] = $('input[name="edit_unit_price"]').val() / row_unit_operation_value;
        } else {
            product_price[rowindex] = $('input[name="edit_unit_price"]').val() * row_unit_operation_value;
        }
        var position = $('select[name="edit_unit"]').val();
        var temp_operator = temp_unit_operator[position];
        var temp_operation_value = temp_unit_operation_value[position];
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit').val(temp_unit_name[position]);
        temp_unit_name.splice(position, 1);
        temp_unit_operator.splice(position, 1);
        temp_unit_operation_value.splice(position, 1);

        temp_unit_name.unshift($('select[name="edit_unit"] option:selected').text());
        temp_unit_operator.unshift(temp_operator);
        temp_unit_operation_value.unshift(temp_operation_value);

        unit_name[rowindex] = temp_unit_name.toString() + ',';
        unit_operator[rowindex] = temp_unit_operator.toString() + ',';
        unit_operation_value[rowindex] = temp_unit_operation_value.toString() + ',';
    }
    checkQuantity(edit_qty, false);
});

$('button[name="order_discount_btn"]').on("click", function() {
    calculateGrandTotal();
});

$('button[name="shipping_cost_btn"]').on("click", function() {
    calculateGrandTotal();
});

$('button[name="order_tax_btn"]').on("click", function() {
    calculateGrandTotal();
});

$(".coupon-check").on("click",function() {
    couponDiscount();
});

$(".payment-btn").on("click", function() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    $('.qc').data('initial', 1);
});

$("#draft-btn").on("click",function(){
    var audio = $("#mysoundclip2")[0];
    audio.play();
    $('input[name="sale_status"]').val(3);
    $('input[name="paying_amount"]').prop('required',false);
    $('input[name="paid_amount"]').prop('required',false);
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!")
    }
    else
        $('.payment-form').submit();
});

$("#gift-card-btn").on("click",function(){
    $('input[name="paid_by_id"]').val(2);
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    giftCard();
});

$("#credit-card-btn").on("click",function(){
    $('input[name="paid_by_id"]').val(3);
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    creditCard();
});

$("#cheque-btn").on("click",function(){
    $('input[name="paid_by_id"]').val(4);
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    cheque();
});

$("#cash-btn").on("click",function(){
    $('input[name="paid_by_id"]').val(1);
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    hide();
});

$("#paypal-btn").on("click",function(){
    $('input[name="paid_by_id"]').val(5);
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    hide();
});

$("#deposit-btn").on("click",function(){
    $('input[name="paid_by_id"]').val(6);
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    hide();
    deposits();
});

$('.payment-option').on("change", function() {       
    var id = $(this).val();
    $('input[name="paid_by_id"]').val(id);
    $(".payment-form").off("submit");
    if(id == 2) {
        giftCard();
    }
    else if (id == 3) {
        creditCard();
    } else if (id == 4) {
        cheque();
    } else {
        hide();
        if (id == 6){
            deposits();
        }
    }
});


$('#add-payment input[name="paying_amount"]').on("input", function() {
    change($(this).val(), $('input[name="paid_amount"]').val());
});

$('#add-payment select[name="gift_card_id_select"]').on("change", function() {
    var balance = gift_card_amount[$(this).val()] - gift_card_expense[$(this).val()];
    $('#add-payment input[name="gift_card_id"]').val($(this).val());
    if($('input[name="paid_amount"]').val() > balance){
        alert('Amount exceeds card balance! Gift Card balance: '+ balance);
    }
});

$('input[name="paid_amount"]').on("input", function() {
    if( $(this).val() > parseFloat($('input[name="paying_amount"]').val()) ) {
        alert('Paying amount cannot be bigger than recieved amount');
        $(this).val('');
    }
    else if( $(this).val() > parseFloat($('#grand-total').text()) ){
        alert('Paying amount cannot be bigger than grand total');
        $(this).val('');
    }

    change( $('input[name="paying_amount"]').val(), $(this).val() );
    var id = $('input[name="paid_by_id"]').val();
    if(id == 2){
        var balance = gift_card_amount[$("#gift_card_id_select").val()] - gift_card_expense[$("#gift_card_id_select").val()];
        if($(this).val() > balance)
            alert('Amount exceeds card balance! Gift Card balance: '+ balance);
    }
    else if(id == 6){
        if( $('input[name="paid_amount"]').val() > deposit[$('#customer_id').val()] )
            alert('Amount exceeds customer deposit! Customer deposit : '+ deposit[$('#customer_id').val()]);
    }
});

$('.transaction-btn-plus').on("click", function() {
    $(this).addClass('d-none');
    $('.transaction-btn-close').removeClass('d-none');
});

$('.transaction-btn-close').on("click", function() {
    $(this).addClass('d-none');
    $('.transaction-btn-plus').removeClass('d-none');
});

$(document).on('click', '.qc-btn', function(e) {
    if($(this).data('amount')) {
        if($('.qc').data('initial')) {
            $('input[name="paying_amount"]').val( $(this).data('amount').toFixed(2) );
            $('.qc').data('initial', 0);
        }
        else {
            $('input[name="paying_amount"]').val( (parseFloat($('input[name="paying_amount"]').val()) + $(this).data('amount')).toFixed(2) );
        }
    }
    else
        $('input[name="paying_amount"]').val('0.00');
    change( $('input[name="paying_amount"]').val(), $('input[name="paid_amount"]').val() );
});

function change(paying_amount, paid_amount) {
    $("#change").text( parseFloat(paying_amount - paid_amount).toFixed(2) );
}

function confirmDelete() {
    if (confirm("Are you sure want to delete?")) {
        return true;
    }
    return false;
}

function productSearch(data){
    $.ajax({
        type: 'GET',
        url: '../lims_product_search',
        data: {
            data: data
        },
        success: function(data) {
            var flag = 1;
            $(".product-code").each(function(i) {
                if ($(this).val() == data[1]) {
                    rowindex = i;
                var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) + 1;
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
                flag = 0;
                checkQuantity(String(qty), true);
                    flag = 0;
                }
            });
            $("input[name='product_code_name']").val('');
            if(flag){
                addNewProduct(data);
            }
        }
   });
}

function addNewProduct(data){
    var newRow = $("<tr>");
    var cols = '';
    temp_unit_name = (data[6]).split(',');
    cols += '<td class="col-sm-4 product-title"><strong>' + data[0] + '</strong> [' + data[1] + ']<button type="button" class="edit-product btn btn-link" data-toggle="modal" data-target="#editModal"> <i class="dripicons-document-edit"></i></button></td>';
    cols += '<td class="col-sm-2 product-price"></td>';
    cols += '<td class="col-sm-3"><div class="input-group"><span class="input-group-btn"><button type="button" class="btn btn-default minus"><span class="dripicons-minus"></span></button></span><input type="text" name="qty[]" class="form-control qty numkey input-number" value="1" step="any" required><span class="input-group-btn"><button type="button" class="btn btn-default plus"><span class="dripicons-plus"></span></button></span></div></td>';
    cols += '<td class="col-sm-2 sub-total"></td>';
    cols += '<td class="col-sm-1"><button type="button" class="ibtnDel btn btn-danger btn-sm">X</button></td>';
    cols += '<input type="hidden" class="product-code" name="product_code[]" value="' + data[1] + '"/>';
    cols += '<input type="hidden" class="product-id" name="product_id[]" value="' + data[9] + '"/>';
    cols += '<input type="hidden" class="sale-unit" name="sale_unit[]" value="' + temp_unit_name[0] + '"/>';
    cols += '<input type="hidden" class="net_unit_price" name="net_unit_price[]" />';
    cols += '<input type="hidden" class="discount-value" name="discount[]" />';
    cols += '<input type="hidden" class="tax-rate" name="tax_rate[]" value="' + data[3] + '"/>';
    cols += '<input type="hidden" class="tax-value" name="tax[]" />';
    cols += '<input type="hidden" class="subtotal-value" name="subtotal[]" />';

    newRow.append(cols);
    if(keyboard_active==1){
        $("table.order-list tbody").append(newRow).find('.qty').keyboard({usePreview: false, layout: 'custom', display: { 'accept'  : '&#10004;', 'cancel'  : '&#10006;' }, customLayout : {
          'normal' : ['1 2 3', '4 5 6', '7 8 9','0 {dec} {bksp}','{clear} {cancel} {accept}']}, restrictInput : true, preventPaste : true, autoAccept : true, css: { container: 'center-block dropdown-menu', buttonDefault: 'btn btn-default', buttonHover: 'btn-primary',buttonAction: 'active', buttonDisabled: 'disabled'},});
    }
    else
        $("table.order-list tbody").append(newRow);

    pos = product_code.indexOf(data[1]);
    if(!data[11] && product_warehouse_price[pos]) {
        product_price.push(parseFloat(product_warehouse_price[pos] * currency['exchange_rate']) + parseFloat(product_warehouse_price[pos] * currency['exchange_rate'] * customer_group_rate));
    }
    else {
        product_price.push(parseFloat(data[2] * currency['exchange_rate']) + parseFloat(data[2] * currency['exchange_rate'] * customer_group_rate));
    }

    product_discount.push('0.00');
    tax_rate.push(parseFloat(data[3]));
    tax_name.push(data[4]);
    tax_method.push(data[5]);
    unit_name.push(data[6]);
    unit_operator.push(data[7]);
    unit_operation_value.push(data[8]);
    rowindex = newRow.index();
    checkQuantity(1, true);
}

function edit(){
    var row_product_name_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(1)').text();
    $('#modal_header').text(row_product_name_code);

    var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
    $('input[name="edit_qty"]').val(qty);
    $('input[name="edit_discount"]').val(parseFloat(product_discount[rowindex]).toFixed(2));

    var tax_name_all = <?php echo json_encode($tax_name_all) ?>;
    pos = tax_name_all.indexOf(tax_name[rowindex]);
    $('select[name="edit_tax_rate"]').val(pos);

    var row_product_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-code').val();
    pos = product_code.indexOf(row_product_code);
    if(product_type[pos] == 'standard'){
        unitConversion();
        temp_unit_name = (unit_name[rowindex]).split(',');
        temp_unit_name.pop();
        temp_unit_operator = (unit_operator[rowindex]).split(',');
        temp_unit_operator.pop();
        temp_unit_operation_value = (unit_operation_value[rowindex]).split(',');
        temp_unit_operation_value.pop();
        $('select[name="edit_unit"]').empty();
        $.each(temp_unit_name, function(key, value) {
            $('select[name="edit_unit"]').append('<option value="' + key + '">' + value + '</option>');
        });
        $("#edit_unit").show();
    }
    else{
        row_product_price = product_price[rowindex];
        $("#edit_unit").hide();
    }

    $('input[name="edit_unit_price"]').val(row_product_price.toFixed(2));
    $('.selectpicker').selectpicker('refresh');
}

function couponDiscount() {
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!")
    }
    else if($("#coupon-code").val() != ''){
        valid = 0;
        $.each(coupon_list, function(key, value) {
            if($("#coupon-code").val() == value['code']){
                valid = 1;
                todyDate = <?php echo json_encode(date('Y-m-d'))?>;
                if(parseFloat(value['quantity']) <= parseFloat(value['used']))
                    alert('This Coupon is no longer available');
                else if(todyDate > value['expired_date'])
                    alert('This Coupon has expired!');
                else if(value['type'] == 'fixed'){
                    if(parseFloat($('input[name="grand_total"]').val()) >= value['minimum_amount']) {
                        $('input[name="grand_total"]').val($('input[name="grand_total"]').val() - value['amount']);
                        $('#grand-total').text(parseFloat($('input[name="grand_total"]').val()).toFixed(2));
                        if(!$('input[name="coupon_active"]').val())
                            alert('Congratulation! You got '+value['amount']+' '+currency['code']+' discount');
                        $(".coupon-check").prop("disabled",true);
                        $("#coupon-code").prop("disabled",true);
                        $('input[name="coupon_active"]').val(1);
                        $('input[name="coupon_id"]').val(value['id']);
                        $('input[name="coupon_discount"]').val(value['amount']);
                        $('#coupon-text').text(parseFloat(value['amount']).toFixed(2));
                        $('#coupon-modal').modal('hide');
                    }
                    else
                        alert('Grand Total is not sufficient for discount! Required '+value['minimum_amount']+' '+currency);
                }
                else{
                    var grand_total = $('input[name="grand_total"]').val();
                    var coupon_discount = grand_total * (value['amount'] / 100);
                    grand_total = grand_total - coupon_discount;
                    $('input[name="grand_total"]').val(grand_total);
                    $('#grand-total').text(parseFloat(grand_total).toFixed(2));
                    if(!$('input[name="coupon_active"]').val())
                            alert('Congratulation! You got '+value['amount']+'% discount');
                    $(".coupon-check").prop("disabled",true);
                    $("#coupon-code").prop("disabled",true);
                    $('input[name="coupon_active"]').val(1);
                    $('input[name="coupon_id"]').val(value['id']);
                    $('input[name="coupon_discount"]').val(coupon_discount);
                    $('#coupon-text').text(parseFloat(coupon_discount).toFixed(2));
                    $('#coupon-modal').modal('hide');
                }
            }
        });
        if(!valid)
            alert('Invalid coupon code!');
    }
}

function checkQuantity(sale_qty, flag) {
    var row_product_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-code').val();
    pos = product_code.indexOf(row_product_code);
    if(product_type[pos] == 'standard'){
        var operator = unit_operator[rowindex].split(',');
        var operation_value = unit_operation_value[rowindex].split(',');
        if(operator[0] == '*')
            total_qty = sale_qty * operation_value[0];
        else if(operator[0] == '/')
            total_qty = sale_qty / operation_value[0];
        if (total_qty > parseFloat(product_qty[pos])) {
            alert('Quantity exceeds stock quantity!');
            if (flag) {
                sale_qty = sale_qty.substring(0, sale_qty.length - 1);
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
                checkQuantity(sale_qty, true);
            } else {
                edit();
                return;
            }
        }
    }
    else if(product_type[pos] == 'combo'){
        child_id = product_list[pos].split(',');
        child_qty = qty_list[pos].split(',');
        $(child_id).each(function(index) {
            var position = product_id.indexOf(parseInt(child_id[index]));
            if( parseFloat(sale_qty * child_qty[index]) > product_qty[position] ) {
                alert('Quantity exceeds stock quantity!');
                if (flag) {
                    sale_qty = sale_qty.substring(0, sale_qty.length - 1);
                    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
                }
                else {
                    edit();
                    flag = true;
                    return false;
                }
            }
        });
    }

    if(!flag){
        $('#editModal').modal('hide');
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
    }
    calculateRowProductData(sale_qty);

}

function calculateRowProductData(quantity) {
    if(product_type[pos] == 'standard')
        unitConversion();
    else
        row_product_price = product_price[rowindex];

    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.discount-value').val((product_discount[rowindex] * quantity).toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val(tax_rate[rowindex].toFixed(2));

    if (tax_method[rowindex] == 1) {
        var net_unit_price = row_product_price - product_discount[rowindex];
        var tax = net_unit_price * quantity * (tax_rate[rowindex] / 100);
        var sub_total = (net_unit_price * quantity) + tax;
        if(parseFloat(quantity))
            var sub_total_unit = sub_total / quantity;
        else
            var sub_total_unit = sub_total;

        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.net_unit_price').val(net_unit_price.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-value').val(tax.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(2)').text(sub_total_unit.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(4)').text(sub_total.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.subtotal-value').val(sub_total.toFixed(2));
    } else {
        var sub_total_unit = row_product_price - product_discount[rowindex];
        var net_unit_price = (100 / (100 + tax_rate[rowindex])) * sub_total_unit;
        var tax = (sub_total_unit - net_unit_price) * quantity;
        var sub_total = sub_total_unit * quantity;

        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.net_unit_price').val(net_unit_price.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-value').val(tax.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(2)').text(sub_total_unit.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(4)').text(sub_total.toFixed(2));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.subtotal-value').val(sub_total.toFixed(2));
    }

    calculateTotal();
}

function unitConversion() {
    var row_unit_operator = unit_operator[rowindex].slice(0, unit_operator[rowindex].indexOf(","));
    var row_unit_operation_value = unit_operation_value[rowindex].slice(0, unit_operation_value[rowindex].indexOf(","));

    if (row_unit_operator == '*') {
        row_product_price = product_price[rowindex] * row_unit_operation_value;
    } else {
        row_product_price = product_price[rowindex] / row_unit_operation_value;
    }
}

function calculateTotal() {
    //Sum of quantity
    var total_qty = 0;
    $("table.order-list tbody .qty").each(function(index) {
        if ($(this).val() == '') {
            total_qty += 0;
        } else {
            total_qty += parseFloat($(this).val());
        }
    });
    $('input[name="total_qty"]').val(total_qty);

    //Sum of discount
    var total_discount = 0;
    $("table.order-list tbody .discount-value").each(function() {
        total_discount += parseFloat($(this).val());
    });

    $('input[name="total_discount"]').val(total_discount.toFixed(2));

    //Sum of tax
    var total_tax = 0;
    $(".tax-value").each(function() {
        total_tax += parseFloat($(this).val());
    });

    $('input[name="total_tax"]').val(total_tax.toFixed(2));

    //Sum of subtotal
    var total = 0;
    $(".sub-total").each(function() {
        total += parseFloat($(this).text());
    });
    $('input[name="total_price"]').val(total.toFixed(2));

    calculateGrandTotal();
}

function calculateGrandTotal() {

    var item = $('table.order-list tbody tr:last').index();

    var total_qty = parseFloat($('input[name="total_qty"]').val());
    var subtotal = parseFloat($('input[name="total_price"]').val());
    var order_tax = parseFloat($('select[name="order_tax_rate"]').val());
   var order_discount = parseFloat($('input[name="order_discount"]').val());
   if (!order_discount)
        order_discount = 0.00;
   $("#discount").text(order_discount.toFixed(2));

   var shipping_cost = parseFloat($('input[name="shipping_cost"]').val());
    if (!shipping_cost)
        shipping_cost = 0.00;

    item = ++item + '(' + total_qty + ')';
    order_tax = (subtotal - order_discount) * (order_tax / 100);
    var grand_total = (subtotal + order_tax + shipping_cost) - order_discount;
    $('input[name="grand_total"]').val(grand_total.toFixed(2));

    couponDiscount();
    var coupon_discount = parseFloat($('input[name="coupon_discount"]').val());
    if (!coupon_discount)
        coupon_discount = 0.00;
    grand_total -= coupon_discount;

    $('#item').text(item);
    $('input[name="item"]').val($('table.order-list tbody tr:last').index() + 1);
    $('#subtotal').text(subtotal.toFixed(2));
    $('#tax').text(order_tax.toFixed(2));
    $('input[name="order_tax"]').val(order_tax.toFixed(2));
    $('#shipping-cost').text(shipping_cost.toFixed(2));
    $('#grand-total').text(grand_total.toFixed(2));
    $('input[name="grand_total"]').val(grand_total.toFixed(2));
}

function hide() {
    $(".card-element").hide();
    $(".card-errors").hide();
    $("#cheque").hide();
    $("#gift-card").hide();
    $('input[name="cheque_no"]').attr('required', false);
}

function giftCard() {
    $("#gift-card").show();
    $.ajax({
        url: '../get_gift_card',
        type: "GET",
        dataType: "json",
        success:function(data) {
            $('#add-payment select[name="gift_card_id_select"]').empty();
            $.each(data, function(index) {
                gift_card_amount[data[index]['id']] = data[index]['amount'];
                gift_card_expense[data[index]['id']] = data[index]['expense'];
                $('#add-payment select[name="gift_card_id_select"]').append('<option value="'+ data[index]['id'] +'">'+ data[index]['card_no'] +'</option>');
            });
            $('.selectpicker').selectpicker('refresh');
            $('.selectpicker').selectpicker();
        }
    });
    $(".card-element").hide();
    $(".card-errors").hide();
    $("#cheque").hide();
    $('input[name="cheque_no"]').attr('required', false);
}

function cheque() {
    $("#cheque").show();
    $(".card-element").hide();
    $(".card-errors").hide();
    $("#gift-card").hide();
    $('input[name="cheque_no"]').attr('required', true);
}

function creditCard() {
    $.getScript( "../../public/vendor/stripe/checkout.js" );
    $(".card-element").show();
    $(".card-errors").show();
    $("#cheque").hide();
    $("#gift-card").hide();
    $('input[name="cheque_no"]').attr('required', false);
}

function deposits() {
    if($('input[name="paid_amount"]').val() > deposit[$('#customer_id').val()]){
        alert('Amount exceeds customer deposit! Customer deposit : '+ deposit[$('#customer_id').val()]);
    }
    $('input[name="cheque_no"]').attr('required', false);
}

function cancel(rownumber) {
    while(rownumber >= 0) {
        product_price.pop();
        product_discount.pop();
        tax_rate.pop();
        tax_name.pop();
        tax_method.pop();
        unit_name.pop();
        unit_operator.pop();
        unit_operation_value.pop();
        $('table.order-list tbody tr:last').remove();
        rownumber--;
    }
    $('input[name="shipping_cost"]').val('');
    $('input[name="order_discount"]').val('');
    $('select[name="order_tax_rate"]').val(0);
    calculateTotal();
}

function confirmCancel() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    if (confirm("Are you sure want to cancel?")){
        cancel($('table.order-list tbody tr:last').index());
    }
    return false;
}

$(document).on('submit', '.payment-form', function(e) {
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!");
        e.preventDefault();
    }
    else if( parseFloat( $('input[name="paying_amount"]').val() ) < parseFloat( $('input[name="paid_amount"]').val()) ){
        alert('Paying amount cannot be bigger than recieved amount');
        e.preventDefault();
    }
});

$('#product-table').DataTable( {
    "order": [],
    'pageLength': product_row_number,
     'language': {
        'paginate': {
            'previous': '<i class="fa fa-angle-left"></i>',
            'next': '<i class="fa fa-angle-right"></i>'
        }
    },
    dom: 'tp'
});

</script>
@endsection
@section('scripts')
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>

@endsection

