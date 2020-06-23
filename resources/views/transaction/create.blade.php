@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    @php
        $role = Auth::user()->role->slug;
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-plus-circle"></i> {{__('page.add_transaction')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('product.index')}}">{{__('page.transaction_management')}}</a></li>
                        <li class="active">{{__('page.add')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body p-lg-5">
                <form action="{{route('transaction.save')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.reference_no')}}</label>
                                <input class="form-control" type="text" name="reference_no" placeholder="{{__('page.reference_no')}}" required>
                                @error('reference_no')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.supplier')}} / {{__('page.customer')}}</label>
                                <input class="form-control" type="text" name="supplier_customer" placeholder="{{__('page.supplier')}} / {{__('page.customer')}}" required>
                                @error('supplier_customer')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-control-label">{{__('page.date')}}: <span class="text-danger">*</span></label>
                            <input class="datetimepicker form-control" type="text" name="date" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.date')}}" autocomplete="off" required>
                            @error('date')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 mt-md-3">
                            <label class="form-control-label">{{__('page.category')}}: <span class="text-danger">*</span></label>
                            <select class="form-control category select2" name="tcategory" id="category_search" required>
                                <option value="" hidden>{{__('page.select_category')}}</option>
                                @foreach ($tcategories as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>                            
                        </div>
                        <div class="col-lg-3 col-md-6 mt-md-3">
                            <label class="control-label">{{__('page.type')}}</label>
                            <select class="form-control type" name="type" required>
                                <option value="1">{{__('page.expense')}}</option>
                                <option value="2">{{__('page.incoming')}}</option>
                            </select>
                        </div>                                             
                        <div class="col-lg-3 col-md-6 mt-md-3">
                            <label class="control-label">{{__('page.amount')}}</label>
                            <input class="form-control amount" type="number" name="amount" min="0" step="0.01" placeholder="{{__('page.amount')}}" required>
                        </div>                                               
                        <div class="col-lg-3 col-md-6 mt-md-3">
                            <label class="control-label">{{__('page.attachment')}}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" data-toggle="custom-file-input" name="attachment" accept="image/*,application/pdf">
                                <label class="custom-file-label" for="example-file-input-custom">Choose File</label>
                            </div>
                        </div>
                    </div> 
                    <div class="row mt-md-5">
                        <div class="col-md-12">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="{{__('page.note')}}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-check mr-1"></i> {{__('page.save')}}</button>
                        <a href="{{route('transaction.index')}}" class="btn btn-warning"><i class="fa fa-times mr-1"></i> {{__('page.cancel')}}</a>
                    </div>
                </form>  
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script>
    $(document).ready(function () {        
        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });
        
        $(document).ready(function(){
            $('.datetimepicker').datetimepicker({
                dateFormat: 'yy-mm-dd',
            });
            // $('.select2').each(function() {
            //     $(this)
            //         .select2({
            //             width: 'resolve',
            //             placeholder: "{!! __('page.category') !!}"
            //         });                    
            // });
        });
    });
</script>
@endsection
