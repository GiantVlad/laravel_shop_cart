import {shallowMount} from '@vue/test-utils'
import NavCart from '../../resources/assets/js/components/NavCart.vue'

describe('NavCart.vue', () => {
    let wrapper;
    beforeEach(() => {
        wrapper = shallowMount(NavCart, {
            propsData: {
                cart: {
                    total: 4523.74999,
                    3: {productQty: 2},
                    9: {productQty: 1},
                    10: {productQty: 5},
                    17: {productQty: 3}

                }
            }
        })
    })

    it('finds count of items and total sum in nav bar', () => {
        expect(wrapper.find('span#nav-items').text()).toBe('4')
        expect(wrapper.find('span#nav-total').text()).toBe('4523.75')
    })

    it('finds count of items and total after ".add-to-cart" button click', () => {
        wrapper.vm.$root.$emit('nav_cart', {
            items: 7,
            total: 8567.89
        })
        expect(wrapper.find('span#nav-items').text()).toBe('7')
        expect(wrapper.find('span#nav-total').text()).toBe('8567.89')
    })
})