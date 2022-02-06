import { shallowMount } from '@vue/test-utils'
import NavCart from '../../resources/assets/js/components/NavCart.vue'
import { flushPromises } from './flush-promises'
import MockAdapter from "axios-mock-adapter";
import axios from "axios";

describe('NavCart.vue', () => {
    let wrapper, mock;
    beforeEach(() => {
        wrapper = shallowMount(NavCart, {});
        wrapper.setData({ baseUrl: 'https://my-site.com' });
        mock = new MockAdapter(axios);
        mock.onGet('https://my-site.com/cart/content').reply(200, {items: 3, total: 234.34});
    });

    it('finds count of items and total sum in nav bar', (done) => {
        flushPromises().then(() => {
            expect(wrapper.find('span#nav-items').text()).toBe('0');
            expect(wrapper.find('span#nav-total').text()).toBe('0');
            done()
        });
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
