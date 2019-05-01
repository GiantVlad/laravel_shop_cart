
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import SingleItem from './components/SingleItem'
import Product from './components/Product'
import ProductFilter from './components/ProductFilter'
import NavCart from './components/NavCart'
import Cart from './components/Cart'
import NavSearch from './components/NavSearch'
import ModalWrapper from './components/ModalWrapper'
import OrderInfo from './components/OrderInfo'

import moment from 'moment'

//bootstrap 3 plugin
import * as uiv from 'uiv'
Vue.use(uiv)
Vue.filter('formatDate', (value, format = false) => {
    console.table(value)
    if (value) {
        return moment(String(value)).format(format || 'MMMM/DD/YYYY hh:mm');
    }
    return value;
});
const app = new Vue({
    el: '#app',
    components: {SingleItem, Product, ProductFilter, NavCart, Cart, OrderInfo, NavSearch, ModalWrapper},
});
