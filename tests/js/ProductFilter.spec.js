import { shallowMount, createWrapper } from '@vue/test-utils'
import ProductFilter from '../../resources/assets/js/components/ProductFilter.vue'

describe('ProductFilter.vue', () => {
    let  wrapper_numb, wrapper_select;
    beforeEach(()=> {
        wrapper_numb = shallowMount(ProductFilter, {
            propsData: {
                property: {
                    id: 2,
                    name: "weight",
                    priority: "1",
                    prop_group_id: null,
                    type: "number",
                    property_values: [
                        {id: 8, property_id: 2, value: "5", unit_id: 1},
                        {id: 9, property_id: 2, value: "10", unit_id: 1},
                        {id: 10, property_id: 2, value: "20", unit_id: 1},
                        {id: 11, property_id: 2, value: "50", unit_id: 1}
                    ],
                }
            }
        });

        wrapper_select = shallowMount(ProductFilter, {
            propsData: {
                property: {
                    id: 1,
                    name: "manufacturer",
                    priority: "0",
                    prop_group_id: null,
                    type: "selector",
                    property_values: [
                        {id: 1, property_id: 1, value: "Canon", unit_id: null},
                        {id: 2, property_id: 1, value: "Philips", unit_id: null},
                        {id: 3, property_id: 1, value: "Bosch", unit_id: null},
                        {id: 4, property_id: 1, value: "Nesquik", unit_id: null},
                        {id: 5, property_id: 1, value: "Rolex", unit_id: null},
                        {id: 6, property_id: 1, value: "Ferrari", unit_id: null}
                    ],
                }
            }
        });
    });

    it('contains input for property with type "number"', () => {
        expect(wrapper_numb.contains('input#select-property-min-'+wrapper_numb.props('property').id)).toBe(true)
        expect(wrapper_numb.contains('input#select-property-max-'+wrapper_numb.props('property').id)).toBe(true)
    });

    it('contains checkboxes for property with type "select"', () => {
        wrapper_select.props('property').property_values.forEach(val=>{
            expect(wrapper_select.findAll('.checkbox > label').filter(w => w.text() === val.value).isEmpty())
                .toBe(false)
        })
    });

    it('checks that event has been emitted, when max/min inputs were changed', () => {
        let node = wrapper_numb.find('input#select-property-min-'+wrapper_numb.props('property').id);
        node.setValue(15);
        node.trigger('change');
        expect(wrapper_numb.vm.min).toBe('15')

        let rootWrapper = createWrapper(wrapper_numb.vm.$root)
        expect(rootWrapper.emitted().product_filter).toBeTruthy()
        expect(rootWrapper.emitted().product_filter[0][0].option).toEqual('min')
        expect(rootWrapper.emitted().product_filter[0][0].value).toEqual('15')
    });

    it('checks that event has been emitted, when the checkbox was changed', () => {
        let node = wrapper_select.findAll('.checkbox > label').filter(w => w.text() === 'Bosch').at(0);
        node.find('input').setChecked()
        const rootWrapper = createWrapper(wrapper_select.vm.$root)
        expect(rootWrapper.emitted().product_filter).toBeTruthy()
        expect(rootWrapper.emitted().product_filter[0][0].option).toEqual('checked')
        expect(rootWrapper.emitted().product_filter[0][0].value[0].name).toEqual("Canon")
        expect(rootWrapper.emitted().product_filter[0][0].value[0].value).toBe(false)
        expect(rootWrapper.emitted().product_filter[0][0].value[2].name).toEqual("Bosch")
        expect(rootWrapper.emitted().product_filter[0][0].value[2].value).toBe(true)
    })
});
