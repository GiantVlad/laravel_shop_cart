import { shallowMount } from '@vue/test-utils'
import SingleItem from '../../resources/assets/js/components/SingleItem.vue'
import {flushPromises} from "./flush-promises";

describe('SingleItem.vue', () => {
    let  wrapper;
    const description = 'It is full description of product';
    beforeEach(()=> {
        wrapper = shallowMount(SingleItem, {
            propsData: {
                itemData: {description: description, price: "125.89"}
            }
        });
    });

    it('find description in html', () => {
        expect(wrapper.html()).toContain(description);
    });

    it('shoul\'d be QTY input', () => {
        expect(wrapper.contains('input#single-productQty')).toBe(true);
    });

    it('validates a QTY input ', () => {
        expect(wrapper.vm.qty).toBe(1);
        setQty("8");
        expect(wrapper.vm.qty).toBe("8");
    });

    it('checks a computed property Total', (done) => {
        expect(wrapper.html()).toContain('Total: 125.89');

        wrapper.setData({qty: 5});
        flushPromises().then(() => {
            expect(wrapper.html()).toContain('Total: '+125.89*5);
            done();
        });
    });

    const setQty = val => {
        let qty = wrapper.find('input#single-productQty');
        qty.element.value = val;
        qty.trigger('input');
    }
})
