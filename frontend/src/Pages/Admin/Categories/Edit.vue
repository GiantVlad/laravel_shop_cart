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
                <input v-model="form.name" type="text" class="admin-input" maxlength="30" required />
            </div>

            <div class="admin-col-12">
                <label class="admin-label">Description</label>
                <input v-model="form.description" type="text" class="admin-input" />
            </div>

            <div class="admin-col-3">
                <label class="admin-label">Priority</label>
                <input v-model="form.priority" type="number" class="admin-input" max="99" />
            </div>

            <div class="admin-col-12">
                <label class="admin-label">Parent</label>
                <select v-model="form.parent" class="admin-select">
                    <option value="">No parent</option>
                    <option v-for="item in parentCategories" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
            </div>

            <div class="admin-col-12">
                <label class="admin-label">Image</label>
                <input type="file" class="admin-input admin-file-input" accept="image/*" @change="updatePreview" />
            </div>

            <div v-if="previewUrl" class="admin-col-12">
                <img :src="previewUrl" class="admin-preview-image" alt="Category preview" />
            </div>

            <div class="admin-col-12">
                <button class="admin-btn admin-btn--primary" type="submit" :disabled="form.processing">
                    {{ props.category ? 'Save Category' : 'Create Category' }}
                </button>
            </div>
        </form>
    </section>
</template>
