@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">    
@endsection
@section('content')
    @php
        $role = Auth::user()->role->slug;
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="fa fa-dashboard"></i> {{__('page.dashboard')}}</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.company_statistics')}}</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if ($role == 'admin')
                        @include('dashboard.top_filter')
                    @endif                    
                </div>                
            </div>
            <div class="br-section-wrapper mt-3">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <h4 class="tx-primary float-left">{{__('page.overview')}}</h4>
                        <form action="" class="form-inline float-right" method="post">
                            @csrf
                            <input type="hidden" name="top_company" value="{{$top_company}}" />
                            <input type="text" class="form-control" name="period" id="period" style="width:220px !important" value="{{$period}}" autocomplete="off" placeholder="{{__('page.period')}}">
                            <button type="submit" class="btn btn-primary pd-y-7 ml-3"> <i class="fa fa-search"></i> {{__('page.search')}}</button>
                        </form>
                    </div>
                </div>
                @php
                    $daily_key_array = $daily_array = array();

                    for ($dt=$chart_start; $dt < $chart_end; $dt->addDay()) {
                        $key = $dt->format('Y-m-d');
                        $key1 = $dt->format('M/d');
                        array_push($daily_key_array, $key1);
                        $sales = \App\Models\Sale::where('company_id', $top_company)->whereDate('timestamp', $key)->pluck('id')->toArray();
                        $orders = \App\Models\Order::whereIn('orderable_id', $sales)->where('orderable_type', 'App\Models\Sale')->get();
                        $sale_subtotals = $orders->sum('subtotal');
                        $sale_costs = 0;
                        foreach ($orders as $order) {
                            if($order->product) {
                                $cost = $order->product->cost * $order->quantity;
                            } else {
                                $cost = 0;
                            }
                            $sale_costs += $cost;
                        }
                        $earning = $sale_subtotals - $sale_costs;
                        array_push($daily_array, $earning);
                    }
                @endphp     
                <div class="row">
                    <div class="col-md-12"> 
                        <div class="card card-body">
                            <h3>{{__('page.daily_earnings')}}</h3>
                            <div id="daily_chart" style="height:400px;"></div>
                        </div>
                    </div>
                </div>
                @php
                    $monthly_key_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $monthly_array = array();
                    for ($i=1; $i <= 12; $i++) { 
                        $sales = \App\Models\Sale::where('company_id', $top_company)->whereYear('timestamp', date('Y'))->whereMonth('timestamp', $i)->pluck('id')->toArray();
                        $orders = \App\Models\Order::whereIn('orderable_id', $sales)->where('orderable_type', 'App\Models\Sale')->get();
                        $sale_subtotals = $orders->sum('subtotal');

                        $sale_costs = 0;
                        foreach ($orders as $order) {
                            if($order->product) {
                                $cost = $order->product->cost * $order->quantity;
                            } else {
                                $cost = 0;
                            }
                            $sale_costs += $cost;
                        }
                        $earning = $sale_subtotals - $sale_costs;
                        array_push($monthly_array, $earning);
                    }
                @endphp
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="card card-body">
                            <h3>{{__('page.monthly_earnings')}}</h3>
                            <div id="monthly_chart" style="height:400px;"></div>
                        </div>
                    </div>
                </div>
                <div class="row">                    
                    @php
                        $annual_key_array = ['2020', '2021', '2022'];
                        $annual_array = array();
                        for ($i=2020; $i <= 2022; $i++) { 
                            $sales = \App\Models\Sale::where('company_id', $top_company)->whereYear('timestamp', $i)->pluck('id')->toArray();
                            $orders = \App\Models\Order::whereIn('orderable_id', $sales)->where('orderable_type', 'App\Models\Sale')->get();
                            $sale_subtotals = $orders->sum('subtotal');

                            $sale_costs = 0;
                            foreach ($orders as $order) {
                                if($order->product) {
                                    $cost = $order->product->cost * $order->quantity;
                                } else {
                                    $cost = 0;
                                }
                                $sale_costs += $cost;
                            }
                            $earning = $sale_subtotals - $sale_costs;
                            array_push($annual_array, $earning);
                        }
                    @endphp
                    <div class="col-md-6">
                        <div class="card card-body">
                            <h3>{{__('page.annual_earnings')}}</h3>
                            <div id="annual_chart" style="height:400px;"></div>
                        </div>
                    </div>
                    @php
                        $gain_purchase = $gain_sale = $gain_inventory = 0;
                        foreach (\App\Models\Product::all() as $item) {
                            $quantity = $item->calc_quantity();
                            $g_purchase = $quantity * $item->cost;
                            $g_sale = $quantity * $item->price1;
                            $gain_purchase += $g_purchase;
                            $gain_sale += $g_sale;
                        }
                        $gain_inventory = $gain_sale - $gain_purchase;
                    @endphp
                    <div class="col-md-6">
                        <div class="card card-body">
                            <h3>{{__('page.inventory_gain')}}</h3>
                            <div id="gain_chart" style="height:400px;"></div>
                        </div>
                    </div>                    
                </div>
                <div class="row">
                    @php
                        $all_products = \App\Models\Product::all();
                        $sorted_products = $all_products->sortByDesc(function($product) {
                            $quantity_purchased = \App\Models\Order::where('product_id', $product->id)->where('orderable_type', 'App\Models\Purchase')->sum('quantity');
                            $quantity_saled = \App\Models\Order::where('product_id', $product->id)->where('orderable_type', 'App\Models\Sale')->sum('quantity');
                            if($quantity_purchased == 0 ) {
                                $rate = 0;
                            } else {
                                $rate = $quantity_saled / $quantity_purchased;
                            }
                            return $rate;
                        })->take(10);
                    @endphp
                    <div class="col-md-6">
                        <div class="card card-body table-responsive">
                            <h3>{{__('page.top_sold_10_products')}}</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{__('page.code')}}</th>
                                        <th>{{__('page.name')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sorted_products as $item)                                        
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{$item->code}}</td>
                                            <td>{{$item->name}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @php
                        $least_sold_products = $all_products->filter(function($product) {
                            $quantity_purchased = \App\Models\Order::where('product_id', $product->id)->where('orderable_type', 'App\Models\Purchase')->sum('quantity');
                            $quantity_saled = \App\Models\Order::where('product_id', $product->id)->where('orderable_type', 'App\Models\Sale')->sum('quantity');
                            if($quantity_purchased == 0 ) {
                                $rate = 0;
                            } else {
                                $rate = $quantity_saled / $quantity_purchased;
                            }
                            return $rate < 0.3;
                        });
                    @endphp
                    <div class="col-md-6">
                        <div class="card card-body table-responsive">
                            <h3>{{__('page.least_sold_product')}}</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{__('page.code')}}</th>
                                        <th>{{__('page.name')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($least_sold_products as $item)                                        
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{$item->code}}</td>
                                            <td>{{$item->name}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @php
                    $all_customers = \App\Models\Customer::all();
                    $sorted_customers = $all_customers->sortByDesc(function($customer){
                        $customer_total = $customer->sales()->sum('grand_total');
                        return $customer_total;
                    })->take(15);
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-body table-responsive">
                        <h3>{{__('page.top_15_clients')}}</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{__('page.name')}}</th>
                                        <th>{{__('page.email')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sorted_customers as $item)                                        
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{$item->name}}</td>
                                            <td>{{$item->email}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')    
    <script src="{{asset('master/plugins/echarts/echarts-en.js')}}"></script>
    <script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>

    <script>
        var Chart_overview = function() {

            var chart_statistics = function() {
                if (typeof echarts == 'undefined') {
                    console.warn('Warning - echarts.min.js is not loaded.');
                    return;
                }

                // Daily Chart
                var area_daily_element = document.getElementById('daily_chart');

                if (area_daily_element) {

                    var area_daily = echarts.init(area_daily_element);

                    area_daily.setOption({

                        color: ['#2ec7c9','#5ab1ef','#ff0000','#d87a80','#b6a2de'],

                        textStyle: {
                            fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                            fontSize: 13
                        },

                        animationDuration: 750,

                        grid: {
                            left: 0,
                            right: 40,
                            top: 35,
                            bottom: 0,
                            containLabel: true
                        },

                        tooltip: {
                            trigger: 'axis',
                            backgroundColor: 'rgba(0,0,0,0.75)',
                            padding: [10, 15],
                            textStyle: {
                                fontSize: 13,
                                fontFamily: 'Roboto, sans-serif'
                            }
                        },

                        xAxis: [{
                            type: 'category',
                            boundaryGap: false,
                            data: {!! json_encode($daily_key_array) !!},
                            axisLabel: {
                                color: '#333'
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#999'
                                }
                            },
                            splitLine: {
                                show: true,
                                lineStyle: {
                                    color: '#eee',
                                    type: 'dashed'
                                }
                            }
                        }],

                        yAxis: [{
                            type: 'value',
                            axisLabel: {
                                color: '#333'
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#999'
                                }
                            },
                            splitLine: {
                                lineStyle: {
                                    color: '#eee'
                                }
                            },
                            splitArea: {
                                show: true,
                                areaStyle: {
                                    color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                                }
                            }
                        }],

                        series: [
                            {
                                name: 'Daily Earnings',
                                type: 'line',
                                data: {!! json_encode($daily_array) !!},
                                areaStyle: {
                                    normal: {
                                        opacity: 0.25
                                    }
                                },
                                smooth: true,
                                symbolSize: 7,
                                itemStyle: {
                                    normal: {
                                        borderWidth: 2
                                    }
                                }
                            }
                        ]
                    });
                }

                var area_monthly_element = document.getElementById('monthly_chart');

                if (area_monthly_element) {

                    var area_monthly = echarts.init(area_monthly_element);

                    area_monthly.setOption({
                        color: ['#3398DB'],
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                                type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                            }
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: {!! json_encode($monthly_key_array) !!},
                                axisTick: {
                                    alignWithLabel: true
                                }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value'
                            }
                        ],
                        series: [
                            {
                                name: 'Monthly Earnings',
                                type: 'bar',
                                barWidth: '60%',
                                data: {!! json_encode($monthly_array) !!}
                            }
                        ]
                    });
                }

                var area_annual_element = document.getElementById('annual_chart');

                if (area_annual_element) {

                    var area_annual = echarts.init(area_annual_element);

                    area_annual.setOption({
                        color: ['#3398DB'],
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                                type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                            }
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: {!! json_encode($annual_key_array) !!},
                                axisTick: {
                                    alignWithLabel: true
                                }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value'
                            }
                        ],
                        series: [
                            {
                                name: 'Annual Earnings',
                                type: 'bar',
                                barWidth: '60%',
                                data: {!! json_encode($annual_array) !!}
                            }
                        ]
                    });
                }

                var area_gain_element = document.getElementById('gain_chart');

                if (area_gain_element) {

                    var area_gain = echarts.init(area_gain_element);

                    area_gain.setOption({
                        color: ['#3398DB'],
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                                type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                            }
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: ['Purchase Gain', 'Sale Gain', 'Inventory Gain'],
                                axisTick: {
                                    alignWithLabel: true
                                }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value'
                            }
                        ],
                        series: [
                            {
                                name: 'Inventory Gains',
                                type: 'bar',
                                barWidth: '50%',
                                data: {!! json_encode([$gain_purchase, $gain_sale, $gain_inventory]) !!}
                            }
                        ]
                    });
                }

                // Resize function
                var triggerChartResize = function() {
                    area_daily_element && area_daily.resize();
                    area_monthly_element && area_monthly.resize();
                    area_annual_element && area_annual.resize();
                    area_gain_element && area_gain.resize();
                };

                // On sidebar width change
                $(document).on('click', '.sidebar-control', function() {
                    setTimeout(function () {
                        triggerChartResize();
                    }, 0);
                });

                // On window resize
                var resizeCharts;
                window.onresize = function () {
                    clearTimeout(resizeCharts);
                    resizeCharts = setTimeout(function () {
                        triggerChartResize();
                    }, 200);
                };
            };          

                

            return {
                init: function() {
                    chart_statistics();
                }
            }
        }();

        document.addEventListener('DOMContentLoaded', function() {
            Chart_overview.init();
        });
    </script>


    <script>
        $(document).ready(function () {
            $("#period").dateRangePicker();
            $("#top_company_filter").change(function(){
                $("#top_filter_form").submit();
            });
        });
    </script>
@endsection