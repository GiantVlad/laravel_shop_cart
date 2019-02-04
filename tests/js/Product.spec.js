import {createWrapper, shallowMount} from '@vue/test-utils'
import Product from '../../resources/assets/js/components/Product.vue'

describe('Product.vue', () => {
    let  wrapper;
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

    it('finds description in html', () => {
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        expect(wrapper.html()).toContain(description)
    })

    it('checks Up-Down Slider', () => {
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        expect(wrapper.contains('.product-shop-desc.is-displayed')).toBe(false)
        expect(wrapper.contains('.center-block.is-displayed')).toBe(true)

        const triangleBtn = wrapper.find('div.effect')
        triangleBtn.trigger('click')

        expect(wrapper.contains('.product-shop-desc.is-displayed')).toBe(true)
        expect(wrapper.contains('.center-block.is-displayed')).toBe(false)
    })

    it('contains price', () => {
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        expect(wrapper.find('.cart-wrapper').html()).toContain('Price: 125.89')
    })

    it('The product is NOT displayed if the filter is set and the product has no property', () => {

        wrapper = shallowMount(Product, {
            propsData: productData
        })
        let emitData = {property_id: 2, option: 'min', value: 15}

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)
    })

    it('The product is NOT displayed if the filter is set and the product has no same property', () => {
        productData.product.properties[0] = {property_id: 1, value: 'Bosch'}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        let emitData = {property_id: 2, option: 'min', value: 15}

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)
    })

    it('The product is displayed if the filter is set in min=15 and the product has property value=20', () => {

        productData.product.properties[0] = {property_id: 2, value: 20}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        let emitData = {property_id: 2, option: 'min', value: 15}
        const rootWrapper = createWrapper(wrapper.vm.$root)

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(15)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
    })

    it('The product is NOT displayed if the filter is set in min=15 and the product has property value=10', () => {

        productData.product.properties[0] = {property_id: 2, value: 10}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        let emitData = {property_id: 2, option: 'min', value: 15}
        const rootWrapper = createWrapper(wrapper.vm.$root)

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(15)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)
    })

    it('The product is displayed if the filter is set in max=25 and the product has property with value=20', () => {

        productData.product.properties[0] = {property_id: 2, value: 20}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        let emitData = {property_id: 2, option: 'max', value: 25}
        const rootWrapper = createWrapper(wrapper.vm.$root)

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(25)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
    })

    it('The product is NOT displayed if the filter is set in max=25 and the product has property with value=30', () => {

        productData.product.properties[0] = {property_id: 2, value: 30}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        let emitData = {property_id: 2, option: 'max', value: 25}
        const rootWrapper = createWrapper(wrapper.vm.$root)

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(rootWrapper.emitted().product_filter[0][0].value).toEqual(25)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)
    })

    it('Combination of min and max filters', () => {

        productData.product.properties[0] = {property_id: 2, value: 30}
        wrapper = shallowMount(Product, {
            propsData: productData
        })

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        let emitData = {property_id: 2, option: 'max', value: 40}
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        emitData = {property_id: 2, option: 'min', value: 20}
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        emitData = {property_id: 2, option: 'max', value: 25}
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)

        emitData.value = null
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        emitData.option = 'min'
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
    })

    it('Combination of filters with different types (number and select)', () => {

        productData.product.properties[0] = {property_id: 1, value: "Bosch"}
        wrapper = shallowMount(Product, {
            propsData: productData
        })
        checkbox_values[2].value = true
        let emitData = {property_id: 1, option: 'checked', value: checkbox_values}

        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        emitData = {property_id: 2, option: 'max', value: 40}
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)

        emitData.value = null
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
    })

    it('The product is displayed if the filter is set in "Bosch"=checked and the product has property with value="Bosch"', () => {

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
        expect(rootWrapper.emitted().product_filter[0][0].value[2].value).toBe(true)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)

        checkbox_values[2].value = false
        emitData = {property_id: 1, option: 'checked', value: checkbox_values}
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(false)

        checkbox_values[4].value = false
        emitData = {property_id: 1, option: 'checked', value: checkbox_values}
        wrapper.vm.$root.$emit('product_filter', emitData)
        expect(wrapper.find('div.cart-wrapper').exists()).toBe(true)
    })
})