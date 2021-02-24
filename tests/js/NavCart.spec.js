import { shallowMount } from '@vue/test-utils'
import NavCart from '../../resources/assets/js/components/NavCart.vue'
import { flushPromises } from './flush-promises'

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
        });
    });

    it('finds count of items and total sum in nav bar', () => {
        expect(wrapper.find('span#nav-items').text()).toBe('4');
        expect(wrapper.find('span#nav-total').text()).toBe('4523.75');
    });

    it('finds count of items and total after ".add-to-cart" button click', (done) => {
        wrapper.vm.$root.$emit('nav_cart', {
            items: 7,
            total: 8567.89
        });
        flushPromises().then(() => {
            expect(wrapper.find('span#nav-items').text()).toBe('7');
            expect(wrapper.find('span#nav-total').text()).toBe('8567.89');
            done()
        });
    });

    it('finds count of items and total after "remove" button click', (done) => {
        wrapper.vm.$root.$emit('nav_cart', {
            items: 1,
            total: 0
        });
        flushPromises().then(() => {
            expect(wrapper.find('span#nav-items').text()).toBe('1');
            expect(wrapper.find('span#nav-total').text()).toBe('0');
            done()
        });
    });
});
