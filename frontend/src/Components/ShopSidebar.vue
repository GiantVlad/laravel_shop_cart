<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

defineProps({
  categories: { type: Array, default: () => [] },
  properties: { type: Array, default: () => [] },
})

const page = usePage()

// The current query string, e.g. /shop?category_id=3&values_7=1%2C2&page=2
const searchParams = computed(() => new URL(page.url, window.location.origin).searchParams)

const activeCategoryId = computed(() => Number(searchParams.value.get('category_id')) || null)

// values_7=1,2 -> { '7': ['1','2'] }
const activeValues = computed(() => {
  const map = {}
  for (const [key, raw] of searchParams.value.entries()) {
    if (key.startsWith('values_')) {
      map[key.replace('values_', '')] = raw.split(',')
    }
  }
  return map
})

const isChecked = (propertyId, valueId) =>
  (activeValues.value[propertyId] || []).includes(String(valueId))

const rangeValue = (propertyId, index) =>
  (activeValues.value[propertyId] || [])[index] ?? ''

/**
 * Merge `params` into the current /shop query and navigate.
 * Always drops `page` so a filter change restarts pagination.
 */
function navigate(params) {
  const search = new URLSearchParams(new URL(page.url, window.location.origin).search)
  search.delete('page')

  Object.entries(params).forEach(([key, value]) => {
    const empty =
      value === null || value === undefined || value === '' || (Array.isArray(value) && value.length === 0)
    if (empty) {
      search.delete(key)
    } else {
      search.set(key, Array.isArray(value) ? value.join(',') : String(value))
    }
  })

  const query = search.toString()
  router.get(`/shop${query ? `?${query}` : ''}`, {}, { preserveScroll: true, preserveState: true })
}

function selectCategory(id) {
  navigate({ category_id: id })
}

function toggleValue(propertyId, valueId) {
  const current = [...(activeValues.value[propertyId] || [])]
  const index = current.indexOf(String(valueId))
  if (index >= 0) {
    current.splice(index, 1)
  } else {
    current.push(String(valueId))
  }
  navigate({ [`values_${propertyId}`]: current })
}

function setRange(propertyId, index, raw) {
  const current = [...(activeValues.value[propertyId] || [])]
  while (current.length < 2) {
    current.push('')
  }
  current[index] = raw
  navigate({ [`values_${propertyId}`]: current })
}

function clearAll() {
  router.get('/shop', {}, { preserveScroll: true })
}
</script>

<template>
  <aside class="shop-sidebar">
    <!-- Categories -->
    <section class="sidebar-block">
      <h2 class="sidebar-title">Categories</h2>
      <ul class="category-list">
        <li>
          <button
            type="button"
            class="category-link"
            :class="{ 'is-active': activeCategoryId === null }"
            @click="selectCategory(null)"
          >
            All products
          </button>
        </li>
        <li v-for="category in categories" :key="category.id">
          <button
            type="button"
            class="category-link"
            :class="{ 'is-active': activeCategoryId === category.id }"
            @click="selectCategory(category.id)"
          >
            {{ category.name }}
          </button>
          <ul v-if="category.children?.length" class="category-sublist">
            <li v-for="child in category.children" :key="child.id">
              <button
                type="button"
                class="category-link category-link--child"
                :class="{ 'is-active': activeCategoryId === child.id }"
                @click="selectCategory(child.id)"
              >
                {{ child.name }}
              </button>
            </li>
          </ul>
        </li>
      </ul>
    </section>

    <!-- Property filters -->
    <section v-if="properties.length" class="sidebar-block">
      <h2 class="sidebar-title">Filters</h2>

      <div v-for="property in properties" :key="property.id" class="filter-group">
        <p class="filter-group__name">{{ property.name }}</p>

        <template v-if="property.type === 'selector'">
          <label
            v-for="value in property.property_values"
            :key="value.id"
            class="filter-checkbox"
          >
            <input
              type="checkbox"
              :checked="isChecked(property.id, value.id)"
              @change="toggleValue(property.id, value.id)"
            />
            <span>{{ value.value }}</span>
          </label>
        </template>

        <div v-else class="filter-range">
          <input
            type="number"
            class="filter-range__input"
            placeholder="min"
            :value="rangeValue(property.id, 0)"
            @change="setRange(property.id, 0, $event.target.value)"
          />
          <input
            type="number"
            class="filter-range__input"
            placeholder="max"
            :value="rangeValue(property.id, 1)"
            @change="setRange(property.id, 1, $event.target.value)"
          />
        </div>
      </div>

      <button type="button" class="sidebar-clear" @click="clearAll">Clear all filters</button>
    </section>
  </aside>
</template>

<style scoped>
.shop-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.sidebar-block {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.15rem 1.25rem;
}

.sidebar-title {
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #64748b;
  margin: 0 0 0.85rem;
}

.category-list,
.category-sublist {
  list-style: none;
  margin: 0;
  padding: 0;
}

.category-sublist {
  margin: 0.15rem 0 0.35rem 0.85rem;
  border-left: 1px solid #e2e8f0;
  padding-left: 0.6rem;
}

.category-link {
  display: block;
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  padding: 0.3rem 0.35rem;
  border-radius: 6px;
  font: inherit;
  font-size: 0.9rem;
  color: #334155;
  cursor: pointer;
}

.category-link:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.category-link.is-active {
  color: #0284c7;
  font-weight: 600;
  background: #f0f9ff;
}

.category-link--child {
  font-size: 0.85rem;
  color: #64748b;
}

.filter-group {
  margin-bottom: 1.1rem;
}

.filter-group:last-of-type {
  margin-bottom: 0.5rem;
}

.filter-group__name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.5rem;
  text-transform: capitalize;
}

.filter-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #334155;
  padding: 0.15rem 0;
  cursor: pointer;
}

.filter-checkbox input {
  accent-color: #0284c7;
}

.filter-range {
  display: flex;
  gap: 0.5rem;
}

.filter-range__input {
  width: 100%;
  min-width: 0;
  height: 34px;
  padding: 0 0.6rem;
  font: inherit;
  font-size: 0.85rem;
  color: #0f172a;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  outline: none;
}

.filter-range__input:focus {
  background: #ffffff;
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.sidebar-clear {
  margin-top: 0.5rem;
  background: none;
  border: none;
  padding: 0;
  font: inherit;
  font-size: 0.8rem;
  font-weight: 600;
  color: #0284c7;
  cursor: pointer;
}

.sidebar-clear:hover {
  text-decoration: underline;
}
</style>
