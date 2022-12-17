import {createWrapper, shallowMount} from '@vue/test-utils'
import NavSearch from '../../resources/assets/js/components/NavSearch.vue'
import { flushPromises } from './flush-promises'
import chai from 'chai'

describe('NavSearch.vue', () => {
    let wrapper;
    const expect = chai.expect
    beforeEach(() => {
        wrapper = shallowMount(NavSearch, {
            propsData: {
                searchUrl: '/search'
            }
        });
    });

    it('finds button with id nav-search-btn', () => {
        expect(wrapper.find('#nav-search-btn').exists()).to.be.true
    });

    it('clicks on search', (done) => {
        const rootWrapper = createWrapper(wrapper.vm.$root)
        const textInput = wrapper.find('input#nav-search')
        textInput.setValue('some value')
        delete global.window.location
        global.window.location = {pathname: '/shop'}
        wrapper.find('button#nav-search-btn').trigger('click')
        flushPromises().then(() => {
            expect(rootWrapper.emitted().product_search[0][0]).to.be.eq({keyword: 'some value'})
        }).finally(() => (done()))
    })
});
