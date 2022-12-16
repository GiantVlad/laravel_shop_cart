import {createWrapper, shallowMount} from '@vue/test-utils'
import Product from '../../resources/assets/js/components/Product.vue'
import MockAdapter from 'axios-mock-adapter'
import axios from 'axios'
import { flushPromises } from './flush-promises'
import chai from 'chai'

describe('Product.vue', () => {
    let  wrapper;
    let mock;
    const description = 'It is full description of product'
    const expect = chai.expect
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
        mock = new MockAdapter(axios)
        mock.restore()
        wrapper = shallowMount(Product, {
            propsData: productData,
            mocks: {
                $baseUrl: 'https://my-site.com',
            },
        })
    })

    it('finds description in html', () => {
        expect(wrapper.html()).to.contain(description)
    });

    it('checks Up-Down Slider', (done) => {
        expect(wrapper.find('.product-shop-desc.is-displayed').exists()).to.be.eq(false)
        expect(wrapper.find('.center-block.is-displayed').exists()).to.be.eq(true)

        const triangleBtn = wrapper.find('div.effect')
        triangleBtn.trigger('click')
        flushPromises().then(() => {
            expect(wrapper.find('.product-shop-desc.is-displayed').exists()).to.be.eq(true)
            expect(wrapper.find('.center-block.is-displayed').exists()).to.be.eq(false)
        }).finally(() => (done()))
    })

    it('contains price', () => {
        expect(wrapper.find('.cart-wrapper').html()).to.contain('Price: 125.89');
    })

    it('checks "add to cart" button', (done) => {
        mock.onPost('https://my-site.com/cart/add-to-cart')
            .reply(
                200,
                {
                    status: 200,
                    data: {items: 5, total: 1267.89}
                }
            );

        const rootWrapper = createWrapper(wrapper.vm.$root);

        wrapper.find('button.add-to-cart').trigger('click');

        flushPromises().then(() => {
            expect(rootWrapper.emitted().nav_cart[0][0].items).to.be.eq(5)
            expect(rootWrapper.emitted().nav_cart[0][0].total).to.be.eq(1267.89)
        }).finally(() => (done()))
    });
});
