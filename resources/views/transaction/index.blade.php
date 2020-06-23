@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-credit-card"></i> {{__('page.transaction_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('transaction.index')}}">{{__('page.transaction_management')}}</a></li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>         
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class=" clearfix">
                    <form action="" class="col-md-12 form-inline top-search-form" method="POST" id="searchForm">
                        @csrf
                        <label for="pagesize" class="control-label mt-2">{{__('page.show')}} :</label>
                        <select class="form-control form-control-sm mx-md-2 mt-2" name="pagesize" id="pagesize">
                            <option value="15" @if($pagesize == '15') selected @endif>15</option>
                            <option value="50" @if($pagesize == '50') selected @endif>50</option>
                            <option value="100" @if($pagesize == '100') selected @endif>100</option>
                            <option value="200" @if($pagesize == '200') selected @endif>200</option>
                            <option value="500" @if($pagesize == '500') selected @endif>500</option>
                            <option value="" @if($pagesize == '1000000') selected @endif>All</option>
                        </select> 
                        <select class="form-control form-control-sm mr-md-2 mt-2" name="type" id="search_type">
                            <option value="" hidden>{{__('page.select_type')}}</option>
                            <option value="1" @if($type == 1) selected @endif>{{__('page.expense')}}</option>
                            <option value="2" @if($type == 2) selected @endif>{{__('page.incoming')}}</option>
                        </select>                    
                        <select class="form-control form-control-sm mr-md-2 mt-2" name="tcategory" id="search_tcategory">
                            <option value="" hidden>{{__('page.select_category')}}</option>
                            @foreach ($tcategories as $item)
                                <option value="{{$item->id}}" @if($tcategory == $item->id) selected @endif>{{$item->name}}</option>
                            @endforeach                        
                        </select>
                        <input type="text" class="form-control form-control-sm col-md-2 mt-2" name="keyword" id="search_keyword" value="{{$keyword}}" placeholder="{{__('page.search')}}...">
                        <input type="text" class="form-control form-control-sm col-md-2 mt-2 ml-md-2" style="min-width:200px" name="period" id="search_period" value="{{$period}}" autocomplete="off" placeholder="{{__('page.date')}}">
                        <button type="submit" class="btn btn-sm btn-primary ml-md-2 mt-2"><i class="fa fa-search"></i> {{__('page.search')}}</button>
                        <button type="button" class="btn btn-danger btn-sm mt-2 ml-2" id="btn-reset"><i class="fa fa-eraser"></i> {{__('page.reset')}}</button>
                        <a href="{{route('transaction.create')}}" class="btn btn-success btn-sm mt-2 ml-auto"><i class="fa fa-plus"></i> {{__('page.add_new')}}</a>
                    </form> 
                </div>
                <div class="table-responsive mt-2 pb-5">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>{{__('page.date')}}</th>
                                <th>{{__('page.category')}}</th>
                                <th>{{__('page.supplier')}} / {{__('page.customer')}}</th>
                                <th>{{__('page.amount')}}</th>
                                <th>{{__('page.type')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.note')}}</th>
                                <th style="width:120px;">{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @php
                                $footer_total_to_pay = $footer_paid = $footer_balance = 0;
                            @endphp                               
                            @foreach ($data as $item)
                                @php
                                    $supplier_customer = $item->supplier_customer;
                                    if($item->payment){
                                        $payment = $item->payment;
                                        if($item->type == 1){
                                            $proforma = $payment->proforma;
                                            $supplier_customer = $proforma->supplier->company ?? '';
                                        }else if($item->type == 2){
                                            $sale_proforma = $payment->sale_proforma;
                                            $supplier_customer = $sale_proforma->customer->company ?? '';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d', strtotime($item->timestamp))}}</td>
                                    <td class="tcategory" data-id="{{$item->tcategory_id}}">{{$item->tcategory->name ?? ''}}</td>
                                    <td class="supplier_customer">{{$supplier_customer}}</td>
                                    <td class="amount" data-value="{{$item->amount}}">
                                        @if ($item->type == 1)
                                            <span style="color:red">-{{ number_format($item->amount, 2) }}</span>
                                        @elseif($item->type == 2)
                                            <span style="color:green">{{ number_format($item->amount, 2) }}</span>
                                        @else
                                            {{ number_format($item->amount) }}
                                        @endif
                                    </td>
                                    <td class="type">
                                        @if($item->type == 1)
                                            <span class="badge badge-primary">{{__('page.expense')}}</span>
                                        @elseif($item->type == 2)
                                            <span class="badge badge-info">{{__('page.incoming')}}</span>
                                        @endif
                                    </td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="note" data-value="{{$item->note}}">
                                        {{$item->note}}
                                        @if(file_exists($item->attachment))
                                            @php
                                                $path_info = pathinfo($item->attachment);
                                                $attach_ext = $path_info['extension'];
                                            @endphp
                                            @if($attach_ext == 'pdf')
                                                <img class="ez_attach text-primary" src="{{asset('images/attachment.png')}}" height="25" href="{{asset($item->attachment)}}" />
                                            @else
                                                <img class="ez_attach text-primary" src="{{asset($item->attachment)}}" height="30" />
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" id="dropdown-align-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__('page.action')}}&nbsp;</button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-align-primary">
                                                <a class="dropdown-item btn-edit" href="#" data-id="{{$item->id}}">{{__('page.edit')}}</a>
                                                <a class="dropdown-item" href="{{route('transaction.delete', $item->id)}}" onclick="return window.confirm('{{__('page.are_you_sure')}}')">{{__('page.delete')}}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="10" class="text-center">
                                    <span class="mr-5">{{__('page.total_incoming')}} : {{number_format($total['incoming'], 2)}}</span>
                                    <span class="mr-5">{{__('page.total_expense')}} : {{number_format($total['expense'], 2)}}</span>
                                    <span class="">{{__('page.total_balance')}} : {{number_format($total['incoming'] - $total['expense'], 2)}}</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([
                                'keyword' => $keyword,
                                'tcategory' => $tcategory,
                                'period' => $period,
                                'total' => $total,
                                'pagesize' => $pagesize,
                            ])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.edit_transaction')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('transaction.update')}}" id="edit_form" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">                       
                        <div class="form-group">
                            <label class="control-label">{{__('page.reference_no')}}</label>
                            <input class="form-control reference_no" type="text" name="reference_no" required placeholder="{{__('page.reference_no')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.date')}}</label>
                            <input class="form-control date datetimepicker" type="text" name="date" value="{{date('Y-m-d H:i')}}" autocomplete="off" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.date')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.category')}}</label>
                            <select class="form-control tcategory" name="tcategory" required>
                                <option value="" hidden>{{__('page.select_tcategory')}}</option>
                                @foreach ($tcategories as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.supplier')}} / {{__('page.customer')}}</label>
                            <input class="form-control supplier_customer" type="text" name="supplier_customer" required placeholder="{{__('page.supplier')}} / {{__('page.customer')}}">
                        </div>                                                
                        <div class="form-group">
                            <label class="control-label">{{__('page.amount')}}</label>
                            <input class="form-control amount" type="number" name="amount" min="0" step="0.01" placeholder="{{__('page.amount')}}" required>
                        </div>                                               
                        <div class="form-group">
                            <label class="control-label">{{__('page.attachment')}}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" data-toggle="custom-file-input" name="attachment" accept="image/*,application/pdf">
                                <label class="custom-file-label" for="example-file-input-custom">Choose File</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.note')}}</label>
                            <textarea class="form-control note" type="text" name="note" placeholder="{{__('page.note')}}"></textarea>
                        </div> 
                    </div>    
                    <div class="modal-footer">
                        <button type="submit" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>
<script src="{{asset('master/plugins/ezview/EZView.js')}}"></script>
    <script>
        $(document).ready(function(){
            $("input.datetimepicker").datetimepicker({
                dateFormat: 'yy-mm-dd',
            });

            // $('#search_tcategory').each(function() {
            //     $(this).wrap('<div class="position-relative mt-3 mx-md-2" style="width: 230px;"></div>')
            //         .select2({
            //             width: '100%',
            //             placeholder: "{!! __('page.category') !!}"
            //         });                    
            // });

            // $('#edit_form .tcategory').each(function() {
            //     $(this).select2({
            //             width: '100%',
            //             placeholder: "{!! __('page.category') !!}"
            //         });                    
            // });

            $("#search_period").dateRangePicker();

            $("#btn-add").click(function(){
                $("#addModal").modal();
            });
            
            $(".btn-edit").click(function(){
                let id = $(this).data('id');
                let reference_no = $(this).parents('tr').find('.reference_no').text().trim();
                let timestamp = $(this).parents('tr').find('.timestamp').text().trim();
                let supplier_customer = $(this).parents('tr').find('.supplier_customer').text().trim();
                let amount = $(this).parents('tr').find('.amount').data('value');
                let note = $(this).parents('tr').find('.note').data('value');
                let tcategory = $(this).parents('tr').find('.tcategory').data('id');
                $("#edit_form .id").val(id);
                $("#edit_form .reference_no").val(reference_no);
                $("#edit_form .supplier_customer").val(supplier_customer);
                $("#edit_form .date").val(timestamp);
                $("#edit_form .amount").val(amount);
                $("#edit_form .tcategory").val(tcategory);
                $("#edit_form .note").val(note);
                $("#editModal").modal();
            });

            
            $("#btn-reset").click(function(){
                $("#search_keyword").val('');
                $("#search_type").val('');
                $("#search_tcategory").val('');
                $("#search_period").val('');
            });

            $("#pagesize").change(function(){
                $("#searchForm").submit();
            });

            if($(".ez_attach").length) {
                $(".ez_attach").EZView();
            }            
        })
    </script>
@endsection
