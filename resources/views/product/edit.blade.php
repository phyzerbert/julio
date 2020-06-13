@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-edit"></i> {{__('page.edit_product')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('product.index')}}">{{__('page.product')}}</a></li>
                        <li class="active">{{__('page.edit')}}</li>
                    </ol>
                </div>
            </div> 
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body p-lg-5">
                <form class="form-layout form-layout-1" action="{{route('product.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$product->id}}" />
                    <div class="row my-3">
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_name')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{$product->name}}" placeholder="{{__('page.product_name')}}" required>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_code')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="code" value="{{$product->code}}" placeholder="{{__('page.product_code')}}" required>
                                @error('code')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.category')}}: <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="category_id" data-placeholder="Select Category" required>
                                    <option value="" hidden>{{__('page.select_category')}}</option>
                                    @foreach ($categories as $item)
                                        <option value="{{$item->id}}" @if($product->category_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_price')}}1: <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" min="0" name="price1" value="{{$product->price1}}" placeholder="Product Price1" required>
                                @error('price1')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_price')}}2: <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" min="0" name="price2" value="{{$product->price2}}" placeholder="Product Price2" required>
                                @error('price2')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_price')}}3: <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" min="0" name="price3" value="{{$product->price3}}" placeholder="Product Price3" required>
                                @error('price3')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_unit')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="unit" value="{{$product->unit}}" placeholder="Product Unit" required>
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
                                <select class="form-control select2" name="tax_id" data-placeholder="Select Tax">
                                    <option label="Select Tax" hidden></option>
                                    @foreach ($taxes as $item)
                                        <option value="{{$item->id}}" @if($product->tax_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.alert_quantity')}}:</label>
                                <input class="form-control" type="text" name="alert_quantity" value="{{$product->alert_quantity}}" placeholder="{{__('page.alert_quantity')}}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.supplier')}}:</label>
                                <select class="form-control select2-show-search" name="supplier_id" id="product_supplier" data-placeholder="{{__('page.product_supplier')}}">
                                    <option value="" hidden>{{__('page.product_supplier')}}</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}" @if($product->supplier_id == $item->id) selected @endif>{{$item->name}}</option>
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
                                <textarea class="form-control" name="detail" rows="3" placeholder="{{__('page.product_detail')}}">{{$product->detail}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right mt-3">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-check mr-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('product.index')}}" class="btn btn-warning"><i class="fa fa-times mr-2"></i>{{__('page.cancel')}}</a>
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
