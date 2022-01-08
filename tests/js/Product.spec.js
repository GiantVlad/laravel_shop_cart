import {createWrapper, shallowMount} from '@vue/test-utils'
import Product from '../../resources/assets/js/components/Product.vue'
import MockAdapter from 'axios-mock-adapter'
import axios from 'axios'
import { flushPromises } from './flush-promises'

describe('Product.vue', () => {
    let  wrapper;
    let mock;
    const description = 'It is full description of product'
    const checkbox_values = [
        {name: "Canon", value: false},
        {name: "Philips", value: false},
        {name: "Bosch", value: false},
        {name: "Nesquik", value: false},
        {name: "Rolex", value: false},
        {name: "Ferrari", value: false}
    ]
    let productData = { product: {
            id: 123, name: 'test product', description: description, price: "125.89",
                catalogs: {
                name: 'first category'
            },
            properties: []
        }
    }

    beforeEach(() => {
        mock = new MockAdapter(axios);
        wrapper = shallowMount(Product, {
            propsData: productData
        });
    });

    afterEach(() => {
        mock.restore();
    });

    it('finds description in html', () => {
        expect(wrapper.html()).toContain(description);
    });

    it('checks Up-Down Slider', (done) => {
        expect(wrapper.find('.product-shop-desc.is-displayed').exists()).toBe(false);
        expect(wrapper.find('.center-block.is-displayed').exists()).toBe(true);

        const triangleBtn = wrapper.find('div.effect');
        triangleBtn.trigger('click');
        flushPromises().then(() => {
            expect(wrapper.find('.product-shop-desc.is-displayed').exists()).toBe(true);
            expect(wrapper.find('.center-block.is-displayed').exists()).toBe(false);
            done();
        });
    });

    it('contains price', () => {
        expect(wrapper.find('.cart-wrapper').html()).toContain('Price: 125.89');
    });

    it('checks "add to cart" button', (done) => {
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        wrapper.setData({ baseUrl: 'https://my-site.com' });
        mock.onPost('https://my-site.com/cart/add-to-cart')
            .reply(
                200,
                {
                    items: 5,
                    total: 1267.89
                });

        const rootWrapper = createWrapper(wrapper.vm.$root);

        wrapper.find('button.add-to-cart').trigger('click');

        flushPromises().then(() => {
            expect(rootWrapper.emitted().nav_cart[0][0].items).toEqual(5)
            expect(rootWrapper.emitted().nav_cart[0][0].total).toEqual(1267.89)
            done()
        });
    });
});
