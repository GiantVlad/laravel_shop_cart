import { shallowMount } from '@vue/test-utils'
import SingleItem from '../../resources/assets/js/components/SingleItem.vue'
import { flushPromises } from "./flush-promises"
import chai from 'chai'

describe('SingleItem.vue', () => {
    let wrapper
    const expect = chai.expect
    const description = 'It is full description of product'
    beforeEach(()=> {
        wrapper = shallowMount(SingleItem, {
            propsData: {
                itemData: {description: description, price: "125.89"}
            },
            mocks: {
                $baseUrl: 'https://my-site.com'
            }
        })
    })

    it('find description in html', () => {
        expect(wrapper.html()).to.contain(description)
    })

    it('shoul\'d be QTY input', () => {
        expect(wrapper.find('input#single-productQty').exists()).to.be.true
    });

    it('validates a QTY input ', () => {
        expect(wrapper.vm.qty).to.be.eq(1);
        setQty("8");
        expect(wrapper.vm.qty).to.be.eq("8");
    });

    it('checks a computed property Total', (done) => {
        expect(wrapper.html()).to.contain('Total: 125.89');

        wrapper.setData({qty: 5});
        flushPromises().then(() => {
            expect(wrapper.html()).to.contain('Total: '+125.89*5);
        }).finally(() => (done()))
    });

    const setQty = val => {
        let qty = wrapper.find('input#single-productQty');
        qty.element.value = val;
        qty.trigger('input');
    }
})
