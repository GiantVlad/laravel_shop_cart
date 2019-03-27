import {createWrapper, shallowMount} from '@vue/test-utils'
import NavSearch from '../../resources/assets/js/components/NavSearch.vue'
import moxios from 'moxios';

describe('NavSearch.vue', () => {
    let wrapper;
    beforeEach(() => {
        wrapper = shallowMount(NavSearch, {
            propsData: {
                searchUrl: '/search'
            }
        });
    });

    it('finds button with id nav-search-btn', () => {
        expect(wrapper.contains('#nav-search-btn')).toBe(true);
    });

    it('run search by AJAX', (done) => {

        moxios.uninstall()
        moxios.install()
        moxios.stubRequest(/search/, {
            status: 200,
            response: `<div class="row"></div>`
        });

        const rootWrapper = createWrapper(wrapper.vm.$root)

        wrapper.find('button#nav-search-btn').trigger('click')

        moxios.wait(()=>{
            expect(rootWrapper.emitted()['open-model'][0][0]).toEqual(`<div class="row"></div>`)
            done()
        })
    })
});