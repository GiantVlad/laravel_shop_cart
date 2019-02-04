<template>
    <div>
        <p>{{property.name}}</p>
        <template v-if="property.type === 'selector'">
            <div v-for="(property_value, idx) in property.property_values" class="checkbox">
                <label>
                    <input type="checkbox" v-model="checked[idx].value" @change="filter('checked')">
                    {{property_value.value}}
                </label>
            </div>
        </template>
        <template v-else>
            <div class="form-group">
                <input type="text" :id="'select-property-min-'+property.id" class="form-control"
                       placeholder="min" v-model="min" @change="filter('min')">
            </div>
            <div class="form-group">
                <input type="text" :id="'select-property-max-'+property.id" class="form-control"
                       placeholder="max" v-model="max" @change="filter('max')">
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "ProductFilter",
        props: ['property'],
        data () {
            return {
                min: null,
                max: null,
                checked: [],
            }
        },
        created() {
            if (this.property.type === 'selector') {
                this.property.property_values.forEach(val => {
                    this.checked.push({name: val.value, value: false})
                })
            }
        },
        methods: {
            filter(option) {
                this.$root.$emit('product_filter', {property_id: this.property.id, option: option, value: this[option]})
            }
        }
    }
</script>

<style scoped>
</style>