<script setup>
import axios from 'axios'
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
})

const previewUrl = ref(props.product?.imageUrl ?? null)
const properties = ref(
    (props.product?.properties ?? []).map((property) => ({
        ...property,
        value: property.propertyType === 'selector' ? property.id : property.value,
    }))
)

const showAddPropertyModal = ref(false)
const showNewPropertyModal = ref(false)
const availableProperties = ref([])
const selectedPropertyId = ref('')
const selectedPropertyType = ref('selector')
const propertyValues = ref([])
const selectedPropertyValue = ref('')
const manualPropertyValue = ref('')
const newProperty = ref({
    property_name: '',
    property_priority: '',
    property_type: 'number',
    property_value: '',
})

const form = useForm({
    id: props.product?.id ?? '',
    name: props.product?.name ?? '',
    description: props.product?.description ?? '',
    category: props.product?.categoryId ?? '',
    price: props.product?.price ?? '',
    image: null,
    propertyIds: [],
    propertyTypes: [],
    propertyValues: [],
})

const pageTitle = computed(() => (props.product ? 'Edit Product' : 'Add Product'))
const canManageProperties = computed(() => Boolean(props.product?.id))

const syncPropertyFields = () => {
    form.propertyIds = properties.value.map((property) => property.propertyId)
    form.propertyTypes = properties.value.map((property) => property.propertyType)
    form.propertyValues = properties.value.map((property) => property.value)
}

const submit = () => {
    syncPropertyFields()
    form.post('/admin/products', {
        forceFormData: true,
    })
}

const updatePreview = (event) => {
    const [file] = event.target.files
    form.image = file ?? null
    previewUrl.value = file ? URL.createObjectURL(file) : props.product?.imageUrl ?? null
}

const loadProductProperties = async () => {
    if (!props.product?.id) {
        return
    }

    const { data } = await axios.get(`/admin/product/${props.product.id}/properties`, {
        headers: { Accept: 'application/json' },
    })

    properties.value = data.properties.map((property) => ({
        ...property,
        options: property.options ?? [],
        value: property.propertyType === 'selector' ? property.id : property.value,
    }))
}

const removeProperty = async (property) => {
    if (!window.confirm('Remove this property from the product?')) {
        return
    }

    await axios.delete(`/admin/product/${props.product.id}/property`, {
        headers: { Accept: 'application/json' },
        data: { value_id: property.id },
    })

    await loadProductProperties()
}

const fetchAvailableProperties = async () => {
    const { data } = await axios.get('/admin/products/property-types', {
        headers: { Accept: 'application/json' },
    })

    const selectedIds = new Set(properties.value.map((property) => property.propertyId))
    availableProperties.value = data.properties.filter((property) => !selectedIds.has(property.id))
    selectedPropertyId.value = availableProperties.value[0]?.id ?? ''
    selectedPropertyType.value = availableProperties.value[0]?.type ?? 'selector'
    await fetchPropertyValues()
}

const fetchPropertyValues = async () => {
    if (!selectedPropertyId.value) {
        propertyValues.value = []
        selectedPropertyValue.value = ''
        manualPropertyValue.value = ''
        return
    }

    const selected = availableProperties.value.find((property) => property.id === Number(selectedPropertyId.value))
    selectedPropertyType.value = selected?.type ?? 'selector'

    const { data } = await axios.get(`/admin/products/property/${selectedPropertyId.value}/values`, {
        headers: { Accept: 'application/json' },
    })

    propertyValues.value = data.propertyValues ?? []
    selectedPropertyValue.value = propertyValues.value[0]?.id ?? ''
    manualPropertyValue.value = ''
}

const openAddProperty = async () => {
    if (!canManageProperties.value) {
        return
    }

    await fetchAvailableProperties()
    showAddPropertyModal.value = true
}

const addPropertyToProduct = async () => {
    const propertyValue = selectedPropertyType.value === 'number' ? manualPropertyValue.value : selectedPropertyValue.value

    await axios.post('/admin/product/property-type', {
        product_id: props.product.id,
        property_id: selectedPropertyId.value,
        property_input_type: selectedPropertyType.value,
        property_value: propertyValue,
    }, {
        headers: { Accept: 'application/json' },
    })

    showAddPropertyModal.value = false
    await loadProductProperties()
}

const createProperty = async () => {
    await axios.post('/admin/properties', newProperty.value, {
        headers: { Accept: 'application/json' },
    })

    showNewPropertyModal.value = false
    showAddPropertyModal.value = true
    newProperty.value = {
        property_name: '',
        property_priority: '',
        property_type: 'number',
        property_value: '',
    }
    await fetchAvailableProperties()
}
</script>

<template>
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Catalog</p>
                <h2 class="admin-page-title">{{ pageTitle }}</h2>
            </div>
        </div>

        <form class="admin-form-grid" @submit.prevent="submit">
            <div class="admin-col-8">
                <label class="admin-label">Name</label>
                <input v-model="form.name" type="text" class="admin-input" maxlength="150" required />
            </div>

            <div class="admin-col-12">
                <label class="admin-label">Description</label>
                <input v-model="form.description" type="text" class="admin-input" />
            </div>

            <div class="admin-col-7">
                <label class="admin-label">Category</label>
                <select v-model="form.category" class="admin-select" required>
                    <option value="">Select category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div class="admin-col-5">
                <label class="admin-label">Price</label>
                <input v-model="form.price" type="text" class="admin-input" required />
            </div>

            <div class="admin-col-12">
                <label class="admin-label">Image</label>
                <input type="file" class="admin-input admin-file-input" accept="image/*" @change="updatePreview" />
            </div>

            <div v-if="previewUrl" class="admin-col-12">
                <img :src="previewUrl" class="admin-preview-image" alt="Product preview" />
            </div>

            <div class="admin-col-12 admin-property-header">
                <div>
                    <h3 class="admin-page-title">Properties</h3>
                    <p v-if="!canManageProperties" class="admin-muted">
                        Save the product first to attach properties.
                    </p>
                </div>
                <button class="admin-btn admin-btn--outline" type="button" :disabled="!canManageProperties" @click="openAddProperty">
                    Add Property
                </button>
            </div>

            <div v-if="properties.length" class="admin-col-12 admin-form-grid">
                <div v-for="property in properties" :key="`${property.propertyId}-${property.id}`" class="admin-col-12 admin-property-card">
                    <div class="admin-property-meta">
                        <strong>{{ property.propertyName }}</strong>
                        <button class="admin-btn admin-btn--sm admin-btn--danger" type="button" @click="removeProperty(property)">
                            Remove
                        </button>
                    </div>

                    <select
                        v-if="property.propertyType === 'selector'"
                        v-model="property.value"
                        class="admin-select"
                    >
                        <option v-for="option in property.options" :key="option.id" :value="option.id">
                            {{ option.value }}
                        </option>
                    </select>

                    <input
                        v-else
                        v-model="property.value"
                        type="text"
                        class="admin-input"
                    />
                </div>
            </div>

            <div class="admin-col-12">
                <button class="admin-btn admin-btn--primary" type="submit" :disabled="form.processing">
                    {{ props.product ? 'Save Product' : 'Create Product' }}
                </button>
            </div>
        </form>
    </section>

    <div v-if="showAddPropertyModal" class="admin-overlay">
        <div class="admin-modal">
            <h3>Add Property</h3>
            <div v-if="availableProperties.length" class="admin-form-grid">
                <div class="admin-col-12">
                    <label class="admin-label">Property</label>
                    <select v-model="selectedPropertyId" class="admin-select" @change="fetchPropertyValues">
                        <option v-for="property in availableProperties" :key="property.id" :value="property.id">
                            {{ property.name }}
                        </option>
                    </select>
                </div>

                <div class="admin-col-12" v-if="selectedPropertyType === 'selector'">
                    <label class="admin-label">Value</label>
                    <select v-model="selectedPropertyValue" class="admin-select">
                        <option v-for="value in propertyValues" :key="value.id" :value="value.id">
                            {{ value.value }}
                        </option>
                    </select>
                </div>

                <div class="admin-col-12" v-else>
                    <label class="admin-label">Value</label>
                    <input v-model="manualPropertyValue" type="text" class="admin-input" />
                </div>
            </div>
            <p v-else class="admin-muted">All properties are already attached to this product.</p>

            <div class="admin-modal-actions">
                <button class="admin-btn" type="button" @click="showAddPropertyModal = false">Close</button>
                <button class="admin-btn admin-btn--outline" type="button" @click="showNewPropertyModal = true; showAddPropertyModal = false">
                    New Property
                </button>
                <button class="admin-btn admin-btn--primary" type="button" :disabled="!availableProperties.length" @click="addPropertyToProduct">
                    Add
                </button>
            </div>
        </div>
    </div>

    <div v-if="showNewPropertyModal" class="admin-overlay">
        <div class="admin-modal">
            <h3>Create Property</h3>
            <div class="admin-form-grid">
                <div class="admin-col-12">
                    <label class="admin-label">Name</label>
                    <input v-model="newProperty.property_name" type="text" class="admin-input" />
                </div>

                <div class="admin-col-4">
                    <label class="admin-label">Priority</label>
                    <input v-model="newProperty.property_priority" type="number" class="admin-input" />
                </div>

                <div class="admin-col-8">
                    <label class="admin-label">Type</label>
                    <select v-model="newProperty.property_type" class="admin-select">
                        <option value="number">Number</option>
                        <option value="selector">Selector</option>
                    </select>
                </div>

                <div class="admin-col-12" v-if="newProperty.property_type === 'selector'">
                    <label class="admin-label">Initial value</label>
                    <input v-model="newProperty.property_value" type="text" class="admin-input" />
                </div>
            </div>

            <div class="admin-modal-actions">
                <button class="admin-btn" type="button" @click="showNewPropertyModal = false">Close</button>
                <button class="admin-btn admin-btn--primary" type="button" @click="createProperty">Save Property</button>
            </div>
        </div>
    </div>
</template>
