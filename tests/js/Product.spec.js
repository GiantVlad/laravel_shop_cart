import {shallowMount } from '@vue/test-utils'
import Product from '../../resources/assets/js/components/Product.vue'

describe('Product.vue', () => {
    let  wrapper;
    const description = 'It is full description of product';
    beforeEach(()=> {
        wrapper = shallowMount(Product, {
            propsData: {
                product: {
                    id: 123, name: 'test product', description: description, price: "125.89", catalogs: {
                        name: 'first category'
                    }
                }
            }
        })
    })

    it('finds description in html', () => {
        expect(wrapper.html()).toContain(description)
    })

    it('checks Up-Down Slider', () => {
        expect(wrapper.contains('.product-shop-desc.is-displayed')).toBe(false)
        expect(wrapper.contains('.center-block.is-displayed')).toBe(true)

        const triangleBtn = wrapper.find('div.effect')
        triangleBtn.trigger('click')

        expect(wrapper.contains('.product-shop-desc.is-displayed')).toBe(true)
        expect(wrapper.contains('.center-block.is-displayed')).toBe(false)
    })

    it('contains price', () => {
        expect(wrapper.find('.cart-wrapper').html()).toContain('Price: 125.89')
    })

})