
var app = new Vue({
    el: '#page',

    data: {
        order_items: [],
        products: [],
        selected_product: '',
        total: {
            quantity: 0,
            price: 0
        },
        grand_total: 0,
        customer_price_type: 1,
    },


    methods:{
        init() {
            // axios.get('/get_products')
            //     .then(response => {
            //         this.products = response.data;
            //     })
            //     .catch(error => {
            //         console.log(error);
            //     });
        },
        add_item() {
            axios.get('/get_first_product?page=sale')
                .then(response => {
                    if(response.data.quantity > 0) {
                        let tax_name = (response.data.tax) ? response.data.tax.name : ''
                        let tax_rate = (response.data.tax) ? response.data.tax.rate : 0;
                        
                        this.order_items.push({
                            product_id: response.data.id,
                            product_name_code: response.data.name + "(" + response.data.code + ")",
                            price: response.data['price'+this.customer_price_type],
                            tax_name: tax_name,
                            tax_rate: tax_rate,
                            quantity: 1,
                            sub_total: 0,
                            product: response.data,
                        })
                        Vue.nextTick(function() {
                            app.$refs['product'][app.$refs['product'].length - 1].select()
                        });  
                    }
                })
                .catch(error => {
                    console.log(error);
                });  
        },
        calc_subtotal() {
            data = this.order_items
            let total_quantity = 0;
            let total_price = 0;
            for(let i = 0; i < data.length; i++) {
                this.order_items[i].sub_total = (parseFloat(data[i].price) + (data[i].price*data[i].tax_rate)/100) * data[i].quantity
                total_quantity += parseFloat(data[i].quantity)
                total_price += data[i].sub_total
            }

            this.total.quantity = total_quantity
            this.total.price = total_price
        },
        calc_grand_total() {
            this.grand_total = this.total.price
        },
        remove(i) {
            this.order_items.splice(i, 1)
        },
        changeCustomer(e) {
            price_type = e.target.options[e.target.selectedIndex].getAttribute('data-value');
            if(price_type) {
                this.customer_price_type = price_type;
                this.order_items.map(item => {
                    item.price = item.product['price'+price_type];
                });
            }
        }
    },
    filters: {
        currency: function (value) {
            let val = parseFloat(value);
            return val.toFixed(2);
            // return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },

    mounted:function() {
        this.init();
        this.add_item();
        $("#page").css('opacity', 1);
    },
    updated: function() {
        this.calc_subtotal()
        this.calc_grand_total()
        $(".product").autocomplete({
            source : function( request, response ) {
                axios.post('/get_autocomplete_products', { keyword : request.term })
                    .then(resp => {
                        // response(resp.data);
                        response(
                            $.map(resp.data, function(item) {
                                let price = parseFloat(item['price'+app.customer_price_type]).toFixed(2);
                                if(item.quantity > 0) {
                                    return {
                                        label: item.name + "(" + item.code + ")",
                                        value: item.name + "(" + item.code + ")",
                                        id: item.id,
                                        price: price,
                                        tax_name: item.tax ? item.tax.name : '',
                                        tax_rate: item.tax ? item.tax.rate : 0,
                                        product: item,
                                    }  
                                }
                            })
                        );
                    })
                    .catch(error => {
                        console.log(error);
                    }
                );
            }, 
            minLength: 1,
            select: function( event, ui ) {
                let index = $(".product").index($(this));
                let price = parseFloat(ui.item.product['price'+app.customer_price_type]).toFixed(2);
                console.log(price);
                app.order_items[index].product_id = ui.item.id
                app.order_items[index].product_name_code = ui.item.label
                app.order_items[index].price = price
                app.order_items[index].tax_name = ui.item.tax_name
                app.order_items[index].tax_rate = ui.item.tax_rate
                app.order_items[index].quantity = 0
                app.order_items[index].sub_total = price + (price*ui.item.tax_rate)/100
                app.order_items[index].product = ui.item.product;
            }
        });
    }
});
