import {createWrapper, shallowMount} from '@vue/test-utils'
import NavSearch from '../../resources/assets/js/components/NavSearch.vue'
import MockAdapter from 'axios-mock-adapter';
import axios from "axios";
import { flushPromises } from './flush-promises'

describe('NavSearch.vue', () => {
    let wrapper;
    let mock;
    beforeEach(() => {
        wrapper = shallowMount(NavSearch, {
            propsData: {
                searchUrl: '/search'
            }
        });
        mock = new MockAdapter(axios);
    });

    afterEach(() => {
        mock.restore();
    });

    it('finds button with id nav-search-btn', () => {
        expect(wrapper.contains('#nav-search-btn')).toBe(true);
    });

    it('run search by AJAX', (done) => {

        mock.onPost('/search').reply(200, `<div class="row"></div>`);

        const rootWrapper = createWrapper(wrapper.vm.$root)

        wrapper.find('button#nav-search-btn').trigger('click')

        flushPromises().then(() => {
            expect(rootWrapper.emitted()['open-model'][0][0]).toEqual(`<div class="row"></div>`)
            mock.restore();
            done()
        })
    })
});
