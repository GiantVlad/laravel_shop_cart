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
        expect(wrapper.contains('.product-shop-desc.is-displayed')).toBe(false);
        expect(wrapper.contains('.center-block.is-displayed')).toBe(true);

        const triangleBtn = wrapper.find('div.effect');
        triangleBtn.trigger('click');
        flushPromises().then(() => {
            expect(wrapper.contains('.product-shop-desc.is-displayed')).toBe(true);
            expect(wrapper.contains('.center-block.is-displayed')).toBe(false);
            done();
        });
    });

    it('contains price', () => {
        expect(wrapper.find('.cart-wrapper').html()).toContain('Price: 125.89');
    });

    it('The product is NOT displayed if the filter is set and the product has no property', (done) => {
        let emitData = {property_id: 2, option: 'min', value: 15};

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        flushPromises().then(() => {
            expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);
            done();
        });
    });

    it('The product is NOT displayed if the filter is set and the product has no same property', (done) => {
        productData.product.properties[0] = {property_id: 1, value: 'Bosch'};
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        let emitData = {property_id: 2, option: 'min', value: 15};

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        flushPromises().then(() => {
            expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);
            done();
        });
    });

    it('The product is displayed if the filter is set in min=15 and the product has property value=20', (done) => {
        productData.product.properties[0] = {property_id: 2, value: 20};
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        let emitData = {property_id: 2, option: 'min', value: 15};
        const rootWrapper = createWrapper(wrapper.vm.$root);

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        flushPromises().then(() => {
            expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(15);
            expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
            done();
        });
    })

    it('The product is NOT displayed if the filter is set in min=15 and the product has property value=10', (done) => {
        productData.product.properties[0] = {property_id: 2, value: 10};
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        let emitData = {property_id: 2, option: 'min', value: 15};
        const rootWrapper = createWrapper(wrapper.vm.$root);

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        flushPromises().then(() => {
            expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(15);
            expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);
            done();
        });
    });

    it('The product is displayed if the filter is set in max=25 and the product has property with value=20', (done) => {
        productData.product.properties[0] = {property_id: 2, value: 20};
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        let emitData = {property_id: 2, option: 'max', value: 25};
        const rootWrapper = createWrapper(wrapper.vm.$root);

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        flushPromises().then(() => {
            expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(25);
            expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
            done();
        });
    });

    it('The product is NOT displayed if the filter is set in max=25 and the product has property with value=30', (done) => {
        productData.product.properties[0] = {property_id: 2, value: 30};
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        let emitData = {property_id: 2, option: 'max', value: 25};
        const rootWrapper = createWrapper(wrapper.vm.$root);

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        flushPromises().then(() => {
            expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(25);
            expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);
            done();
        });
    })

    it('Combination of min and max filters', async () => {
        productData.product.properties[0] = {property_id: 2, value: 30}
        wrapper = shallowMount(Product, {
            propsData: productData
        })

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        let emitData = {property_id: 2, option: 'max', value: 40};
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);

        emitData = {property_id: 2, option: 'min', value: 20};
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);

        emitData = {property_id: 2, option: 'max', value: 25};
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);

        emitData.value = null;
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);

        emitData.option = 'min';
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
    });

    it('Combination of filters with different types (number and select)', async () => {
        productData.product.properties[0] = {property_id: 1, value: "Bosch"};
        wrapper = shallowMount(Product, {
            propsData: productData
        });
        checkbox_values[2].value = true;
        let emitData = {property_id: 1, option: 'checked', value: checkbox_values};

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);

        emitData = {property_id: 2, option: 'max', value: 40};
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);

        emitData.value = null;
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);

    });

    it('The product is displayed if the filter is set in "Bosch"=checked and the product has property with value="Bosch"', async () => {
        productData.product.properties[0] = {property_id: 1, value: "Bosch"}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        checkbox_values[2].value = true
        checkbox_values[4].value = true
        let emitData = {property_id: 1, option: 'checked', value: checkbox_values}
        const rootWrapper = createWrapper(wrapper.vm.$root)

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        await wrapper.vm.$nextTick();
        expect(rootWrapper.emitted().product_filter[0][0].value[2].value).toBe(true)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        checkbox_values[2].value = false;
        emitData = {property_id: 1, option: 'checked', value: checkbox_values};
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false);

        checkbox_values[4].value = false;
        emitData = {property_id: 1, option: 'checked', value: checkbox_values};
        wrapper.vm.$root.$emit('product_filter', emitData);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true);
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
