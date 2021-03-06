@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
@endsection
@section('content')
    @php
        $role = Auth::user()->role->slug;
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-plus-circle"></i> {{__('page.add_new_product')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('product.index')}}">{{__('page.product')}}</a></li>
                        <li class="active">{{__('page.add')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body p-lg-5">
                <form class="form-layout form-layout-1" action="{{route('product.save')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row my-3">
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_name')}}: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="text" name="name" placeholder="{{__('page.product_name')}}" required>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_code')}}: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="text" name="code" placeholder="{{__('page.product_code')}}" required>
                                @error('code')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.select_category')}}: <span class="tx-danger">*</span></label>
                                <select class="form-control select2" name="category_id" data-placeholder="{{__('page.select_category')}}" required>
                                    <option label="{{__('page.select_category')}}"></option>
                                    @foreach ($categories as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @if($role != 'secretary')
                            <div class="col-lg-4 col-md-6 mt-3">
                                <div class="form-group">
                                    <label class="form-control-label">{{__('page.cost')}}: <span class="tx-danger">*</span></label>
                                    <input class="form-control" type="number" step="0.01" min="0" name="cost" placeholder="{{__('page.cost')}}" required>
                                    @error('cost')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_price')}}1: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" min="0" name="price1" placeholder="{{__('page.product_price')}}1" required>
                                @error('price1')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_price')}}2: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" min="0" name="price2" placeholder="{{__('page.product_price')}}2" required>
                                @error('price2')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_price')}}3: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" min="0" name="price3" placeholder="{{__('page.product_price')}}3" required>
                                @error('price3')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_unit')}}: <span class="tx-danger">*</span></label>
                                <input class="form-control" type="text" name="unit" placeholder="{{__('page.product_unit')}}" required>
                                @error('unit')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_tax')}}:</label>
                                <select class="form-control select2" name="tax_id" data-placeholder="{{__('page.select_tax')}}">
                                    <option label="{{__('page.select_tax')}}"></option>
                                    @foreach ($taxes as $item)
                                        <option value="{{$item->id}}" @if($item->id == 4) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.alert_quantity')}}:</label>
                                <input class="form-control" type="number" name="alert_quantity" placeholder="{{__('page.alert_quantity')}}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.supplier')}}:</label>
                                <select class="form-control select2-show-search" name="supplier_id" id="product_supplier" data-placeholder="{{__('page.product_supplier')}}">
                                    <option>{{__('page.product_supplier')}}</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach                                    
                                </select>
                            </div>
                        </div>                        
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_image')}}:</label>                                
                                <label class="custom-file wd-100p">
                                    <input type="file" name="image[]" id="file2" class="file-input-styled" multiple accept="image/*, application/pdf">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_detail')}}:</label>
                                <textarea class="form-control" name="detail" rows="3" placeholder="{{__('page.product_detail')}}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-check mr-1"></i> {{__('page.save')}}</button>
                        <a href="{{route('product.index')}}" class="btn btn-warning"><i class="fa fa-times mr-1"></i> {{__('page.cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {        
        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });
        
        $('#product_supplier').wrap('<div class="position-relative"></div>')
                    .select2({
                        width: 'resolve',
                    });
    });
</script>
@endsection
