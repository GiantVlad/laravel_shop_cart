<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AdminLayout from '../Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    category: {
        type: Object,
        default: null,
    },
    parentCategories: {
        type: Array,
        default: () => [],
    },
})

const previewUrl = ref(props.category?.imageUrl ?? null)

const form = useForm({
    id: props.category?.id ?? '',
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
    priority: props.category?.priority ?? '',
    parent: props.category?.parentId ?? '',
    image: null,
})

const pageTitle = computed(() => (props.category ? 'Edit Category' : 'Add Category'))

const submit = () => {
    form.post('/admin/categories', {
        forceFormData: true,
    })
}

const updatePreview = (event) => {
    const [file] = event.target.files
    form.image = file ?? null
    previewUrl.value = file ? URL.createObjectURL(file) : props.category?.imageUrl ?? null
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
                <input v-model="form.name" type="text" class="form-control" maxlength="30" required />
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <input v-model="form.description" type="text" class="form-control" />
            </div>

            <div class="col-md-3">
                <label class="form-label">Priority</label>
                <input v-model="form.priority" type="number" class="form-control" max="99" />
            </div>

            <div class="col-md-9">
                <label class="form-label">Parent</label>
                <select v-model="form.parent" class="form-select">
                    <option value="">No parent</option>
                    <option v-for="item in parentCategories" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Image</label>
                <input type="file" class="form-control" accept="image/*" @change="updatePreview" />
            </div>

            <div v-if="previewUrl" class="col-12">
                <img :src="previewUrl" class="preview-image" alt="Category preview" />
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit" :disabled="form.processing">
                    {{ props.category ? 'Save Category' : 'Create Category' }}
                </button>
            </div>
        </form>
    </section>
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

.page-title {
    margin: 0;
}

.preview-image {
    width: min(260px, 100%);
    border-radius: 1rem;
    object-fit: cover;
}
</style>
