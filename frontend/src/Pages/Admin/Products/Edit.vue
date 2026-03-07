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
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Catalog</p>
                <h2 class="page-title">{{ pageTitle }}</h2>
            </div>
        </div>

        <form class="row g-4" @submit.prevent="submit">
            <div class="col-lg-8">
                <label class="form-label">Name</label>
                <input v-model="form.name" type="text" class="form-control" maxlength="150" required />
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <input v-model="form.description" type="text" class="form-control" />
            </div>

            <div class="col-md-7">
                <label class="form-label">Category</label>
                <select v-model="form.category" class="form-select" required>
                    <option value="">Select category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label">Price</label>
                <input v-model="form.price" type="text" class="form-control" required />
            </div>

            <div class="col-12">
                <label class="form-label">Image</label>
                <input type="file" class="form-control" accept="image/*" @change="updatePreview" />
            </div>

            <div v-if="previewUrl" class="col-12">
                <img :src="previewUrl" class="preview-image" alt="Product preview" />
            </div>

            <div class="col-12 property-header">
                <div>
                    <h3 class="property-title">Properties</h3>
                    <p v-if="!canManageProperties" class="text-muted mb-0">
                        Save the product first to attach properties.
                    </p>
                </div>
                <button class="btn btn-outline-primary" type="button" :disabled="!canManageProperties" @click="openAddProperty">
                    Add Property
                </button>
            </div>

            <div v-if="properties.length" class="col-12 property-grid">
                <div v-for="property in properties" :key="`${property.propertyId}-${property.id}`" class="property-card">
                    <div class="property-meta">
                        <strong>{{ property.propertyName }}</strong>
                        <button class="btn btn-sm btn-outline-danger" type="button" @click="removeProperty(property)">
                            Remove
                        </button>
                    </div>

                    <select
                        v-if="property.propertyType === 'selector'"
                        v-model="property.value"
                        class="form-select"
                    >
                        <option v-for="option in property.options" :key="option.id" :value="option.id">
                            {{ option.value }}
                        </option>
                    </select>

                    <input
                        v-else
                        v-model="property.value"
                        type="text"
                        class="form-control"
                    />
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit" :disabled="form.processing">
                    {{ props.product ? 'Save Product' : 'Create Product' }}
                </button>
            </div>
        </form>
    </section>

    <div v-if="showAddPropertyModal" class="overlay">
        <div class="modal-card">
            <h3>Add Property</h3>
            <div v-if="availableProperties.length" class="row g-3">
                <div class="col-12">
                    <label class="form-label">Property</label>
                    <select v-model="selectedPropertyId" class="form-select" @change="fetchPropertyValues">
                        <option v-for="property in availableProperties" :key="property.id" :value="property.id">
                            {{ property.name }}
                        </option>
                    </select>
                </div>

                <div class="col-12" v-if="selectedPropertyType === 'selector'">
                    <label class="form-label">Value</label>
                    <select v-model="selectedPropertyValue" class="form-select">
                        <option v-for="value in propertyValues" :key="value.id" :value="value.id">
                            {{ value.value }}
                        </option>
                    </select>
                </div>

                <div class="col-12" v-else>
                    <label class="form-label">Value</label>
                    <input v-model="manualPropertyValue" type="text" class="form-control" />
                </div>
            </div>
            <p v-else class="mb-3">All properties are already attached to this product.</p>

            <div class="modal-actions">
                <button class="btn btn-outline-secondary" type="button" @click="showAddPropertyModal = false">Close</button>
                <button class="btn btn-outline-primary" type="button" @click="showNewPropertyModal = true; showAddPropertyModal = false">
                    New Property
                </button>
                <button class="btn btn-primary" type="button" :disabled="!availableProperties.length" @click="addPropertyToProduct">
                    Add
                </button>
            </div>
        </div>
    </div>

    <div v-if="showNewPropertyModal" class="overlay">
        <div class="modal-card">
            <h3>Create Property</h3>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Name</label>
                    <input v-model="newProperty.property_name" type="text" class="form-control" />
                </div>

                <div class="col-md-4">
                    <label class="form-label">Priority</label>
                    <input v-model="newProperty.property_priority" type="number" class="form-control" />
                </div>

                <div class="col-md-8">
                    <label class="form-label">Type</label>
                    <select v-model="newProperty.property_type" class="form-select">
                        <option value="number">Number</option>
                        <option value="selector">Selector</option>
                    </select>
                </div>

                <div class="col-12" v-if="newProperty.property_type === 'selector'">
                    <label class="form-label">Initial value</label>
                    <input v-model="newProperty.property_value" type="text" class="form-control" />
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-outline-secondary" type="button" @click="showNewPropertyModal = false">Close</button>
                <button class="btn btn-primary" type="button" @click="createProperty">Save Property</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.content-card {
    padding: 1.5rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.08);
}

.page-header {
    margin-bottom: 1.5rem;
}

.page-eyebrow {
    margin: 0 0 0.35rem;
    color: #0d6efd;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.8rem;
    font-weight: 700;
}

.page-title,
.property-title {
    margin: 0;
}

.preview-image {
    width: min(280px, 100%);
    border-radius: 1rem;
    object-fit: cover;
}

.property-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.property-grid {
    display: grid;
    gap: 1rem;
}

.property-card {
    padding: 1rem;
    border-radius: 1rem;
    background: #f5f8fc;
}

.property-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.overlay {
    position: fixed;
    inset: 0;
    display: grid;
    place-items: center;
    padding: 1rem;
    background: rgba(11, 37, 69, 0.45);
    z-index: 1050;
}

.modal-card {
    width: min(640px, 100%);
    padding: 1.5rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 25px 60px rgba(11, 37, 69, 0.2);
}

.modal-card h3 {
    margin-bottom: 1rem;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

@media (max-width: 767px) {
    .property-header,
    .property-meta,
    .modal-actions {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
