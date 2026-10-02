<script setup lang="ts">
const MIN_PENDING_MS = 500

const config = useRuntimeConfig()

const {
  data: projects,
  error,
  pending,
  refresh
} = await useFetch(`${config.public.apiBase}/projects`)

const showModal = ref(false)
const isSaving = ref(false)
const modalRef = ref<any | null>(null)
const editingProject = ref<any | null>(null)
const formError = ref('')

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')
const sortBy = ref('')

function openAddForm() {
  editingProject.value = null
  formError.value = ''
  showModal.value = true
}

function editProject(project: any) {
  editingProject.value = project
  formError.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingProject.value = null
  formError.value = ''
}

async function saveProject(formData: any) {
  formError.value = ''

  const startedAt = Date.now()

  isSaving.value = true

  let saved = false

  try {
    if (editingProject.value) {
      await $fetch(
        `${config.public.apiBase}/projects/${editingProject.value.id}`,
        {
          method: 'PUT',
          body: formData,
          headers: {
            Accept: 'application/json'
          }
        }
      )
    } else {
      await $fetch(
        `${config.public.apiBase}/projects`,
        {
          method: 'POST',
          body: formData,
          headers: {
            Accept: 'application/json'
          }
        }
      )
    }

    saved = true
  } catch (error: any) {
    if (error?.data?.errors) {
      const errors = error.data.errors

      formError.value = Object.values(errors)
        .flat()
        .join(' ')
    } else {
      formError.value =
        error?.data?.message ||
        'Failed to save project.'
    }
  }

  const remaining =
    MIN_PENDING_MS - (Date.now() - startedAt)

  if (remaining > 0) {
    await new Promise((resolve) =>
      setTimeout(resolve, remaining)
    )
  }

  isSaving.value = false

  if (saved) {
    closeModal()

    await refresh()
  }
}

function openDeleteModal(project: any) {
  deletingProject.value = project
  deleteError.value = ''
}

function closeDeleteModal() {
  deletingProject.value = null
  deleteError.value = ''
  isDeleting.value = false
}

async function deleteProject() {
  const startedAt = Date.now()

  isDeleting.value = true
  deleteError.value = ''

  let deleted = false

  try {
    await $fetch(
      `${config.public.apiBase}/projects/${deletingProject.value.id}`,
      {
        method: 'DELETE',
        headers: {
          Accept: 'application/json'
        }
      }
    )

    deleted = true
  } catch (error: any) {
    deleteError.value =
      error?.data?.message ||
      'Failed to delete project.'
  }

  const remaining =
    MIN_PENDING_MS - (Date.now() - startedAt)

  if (remaining > 0) {
    await new Promise((resolve) =>
      setTimeout(resolve, remaining)
    )
  }

  isDeleting.value = false

  if (deleted) {
    closeDeleteModal()

    await refresh()
  }
}

const filteredProjects = computed(() => {
  if (!projects.value) {
    return []
  }

  const searchValue = search.value
    .trim()
    .toLowerCase()

  const filtered = projects.value.filter(
    (project: any) => {
      const matchesSearch =
        !searchValue ||
        project.client_name
          .toLowerCase()
          .includes(searchValue) ||
        project.project_name
          .toLowerCase()
          .includes(searchValue) ||
        (project.description || '')
          .toLowerCase()
          .includes(searchValue)

      const matchesStatus =
        !statusFilter.value ||
        project.status === statusFilter.value

      const matchesPriority =
        !priorityFilter.value ||
        project.priority === priorityFilter.value

      return (
        matchesSearch &&
        matchesStatus &&
        matchesPriority
      )
    }
  )

  return filtered.sort(
    (a: any, b: any) => {
      switch (sortBy.value) {
        case 'client_asc':
          return a.client_name.localeCompare(
            b.client_name
          )

        case 'client_desc':
          return b.client_name.localeCompare(
            a.client_name
          )

        case 'project_asc':
          return a.project_name.localeCompare(
            b.project_name
          )

        case 'project_desc':
          return b.project_name.localeCompare(
            a.project_name
          )

        case 'start_asc':
          return a.start_date.localeCompare(
            b.start_date
          )

        case 'start_desc':
          return b.start_date.localeCompare(
            a.start_date
          )

        case 'due_asc':
          return a.due_date.localeCompare(
            b.due_date
          )

        case 'due_desc':
          return b.due_date.localeCompare(
            a.due_date
          )

        case 'priority_asc': {
          const priorityOrder: Record<
            string,
            number
          > = {
            Low: 1,
            Medium: 2,
            High: 3
          }

          return (
            priorityOrder[a.priority] -
            priorityOrder[b.priority]
          )
        }

        case 'priority_desc': {
          const priorityOrder: Record<
            string,
            number
          > = {
            Low: 1,
            Medium: 2,
            High: 3
          }

          return (
            priorityOrder[b.priority] -
            priorityOrder[a.priority]
          )
        }

        default:
          return 0
      }
    }
  )
})
</script>

<template>
  <div class="page">

    <main class="container">

      <!-- Header -->
      <header class="page-header">

        <div>
          <p class="eyebrow">
            PROJECT MANAGEMENT
          </p>

          <h1>
            Client Project Tracker
          </h1>

          <p class="subtitle">
            Manage and track your client projects in one place.
          </p>
        </div>

        <button
          class="primary-button"
          @click="
            showModal
              ? modalRef?.requestClose()
              : openAddForm()
          "
        >
          {{
            showModal
              ? 'Cancel'
              : '+ Add Project'
          }}
        </button>

      </header>

      <!-- Modal -->
      <ProjectModal
        v-if="showModal"
        ref="modalRef"
        :project="editingProject"
        :error="formError"
        :pending="isSaving"
        @save="saveProject"
        @close="closeModal"
      />

      <!-- Filters -->
      <ProjectFilters
        v-model:search="search"
        v-model:status-filter="statusFilter"
        v-model:priority-filter="priorityFilter"
        v-model:sort-by="sortBy"
      />

      <!-- Results -->
      <section class="results-section">

        <div class="results-header">

          <div>
            <h2>
              Projects
            </h2>

            <p v-if="!pending">
              {{ filteredProjects.length }}
              {{
                filteredProjects.length === 1
                  ? 'project'
                  : 'projects'
              }}
            </p>
          </div>

        </div>

        <!-- Loading -->
        <div
          v-if="pending"
          class="state-card"
        >
          <div class="spinner"></div>

          <p>
            Loading projects...
          </p>
        </div>

        <!-- Error -->
        <div
          v-else-if="error"
          class="state-card error-state"
        >
          <h3>
            Unable to load projects
          </h3>

          <p>
            Please check that the Laravel API is running.
          </p>
        </div>

        <!-- Empty -->
        <div
          v-else-if="!filteredProjects.length"
          class="state-card"
        >
          <h3>
            No projects found
          </h3>

          <p>
            Try changing your search or filters.
          </p>
        </div>

        <!-- Table -->
        <ProjectTable
          v-else
          :projects="filteredProjects"
          @edit="editProject"
          @delete="deleteProject"
        />

      </section>

    </main>

  </div>
</template>

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family:
    Arial,
    Helvetica,
    sans-serif;

  background: #f5f7fb;
  color: #172033;
}

button,
input,
select,
textarea {
  font-family: inherit;
}

button {
  cursor: pointer;
}

.page {
  min-height: 100vh;
  padding: 40px 20px;
}

.container {
  max-width: 1250px;
  margin: 0 auto;
}

/* Header */

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 20px;
  margin-bottom: 30px;
}

.eyebrow {
  margin: 0 0 8px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1.2px;
  color: #64748b;
}

.page-header h1 {
  margin: 0;
  font-size: 32px;
  line-height: 1.2;
}

.subtitle {
  margin: 8px 0 0;
  color: #64748b;
  font-size: 15px;
}

/* Cards */

.card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow:
    0 2px 8px rgba(15, 23, 42, 0.04);
}

/* Buttons */

.primary-button,
.secondary-button,
.edit-button,
.delete-button {
  border: none;
  border-radius: 6px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 600;
  transition: 0.2s ease;
}

.primary-button {
  background: #111827;
  color: white;
}

.primary-button:hover {
  background: #1f2937;
}

.secondary-button {
  background: #e5e7eb;
  color: #374151;
}

.secondary-button:hover {
  background: #d1d5db;
}

.edit-button {
  background: #2563eb;
  color: white;
  margin-right: 6px;
}

.edit-button:hover {
  background: #1d4ed8;
}

.delete-button {
  background: #dc2626;
  color: white;
}

.delete-button:hover {
  background: #b91c1c;
}

/* Form */

.section-header {
  margin-bottom: 25px;
}

.section-header h2 {
  margin: 0;
  font-size: 20px;
}

.section-header p {
  margin: 6px 0 0;
  color: #64748b;
  font-size: 14px;
}

.project-form {
  width: 100%;
}

.form-grid {
  display: grid;
  grid-template-columns:
    repeat(2, 1fr);
  gap: 18px;
}

.form-group {
  margin-bottom: 18px;
}

.form-group label,
.filter-item label {
  display: block;
  margin-bottom: 7px;
  font-size: 13px;
  font-weight: 700;
  color: #374151;
}

input,
select,
textarea {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 11px 12px;
  font-size: 14px;
  background: white;
  color: #111827;
  outline: none;
  transition: border-color 0.2s ease;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #2563eb;
}

textarea {
  resize: vertical;
  min-height: 100px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 5px;
}

.error-message {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  padding: 12px 14px;
  border-radius: 6px;
  margin-bottom: 20px;
  font-size: 14px;
}

/* Filters */

.filters-card {
  display: grid;
  grid-template-columns:
    2fr 1fr 1fr 1fr;
  gap: 16px;
  padding: 20px;
  margin-bottom: 30px;
}

.filter-item {
  min-width: 0;
}

/* Results */

.results-section {
  margin-bottom: 40px;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.results-header h2 {
  margin: 0;
  font-size: 20px;
}

.results-header p {
  margin: 5px 0 0;
  color: #64748b;
  font-size: 14px;
}

/* Table */

.table-wrapper {
  width: 100%;
  overflow-x: auto;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow:
    0 2px 8px rgba(15, 23, 42, 0.04);
}

table {
  width: 100%;
  min-width: 950px;
  border-collapse: collapse;
}

th {
  background: #f8fafc;
  color: #475569;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

th,
td {
  padding: 14px 16px;
  border-bottom: 1px solid #e5e7eb;
  text-align: left;
  white-space: nowrap;
}

tbody tr:last-child td {
  border-bottom: none;
}

tbody tr:hover {
  background: #f8fafc;
}

.id-column {
  color: #64748b;
  font-weight: 600;
}

.actions-column,
.actions {
  text-align: right;
}

.actions {
  white-space: nowrap;
}

/* Badges */

.badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 5px 10px;
  font-size: 12px;
  font-weight: 700;
}

.status-planning {
  background: #eff6ff;
  color: #1d4ed8;
}

.status-progress {
  background: #fff7ed;
  color: #c2410c;
}

.status-hold {
  background: #fef2f2;
  color: #b91c1c;
}

.status-completed {
  background: #f0fdf4;
  color: #15803d;
}

.priority-low {
  background: #f0fdf4;
  color: #15803d;
}

.priority-medium {
  background: #fffbeb;
  color: #a16207;
}

.priority-high {
  background: #fef2f2;
  color: #b91c1c;
}

/* State */

.state-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 50px 20px;
  text-align: center;
}

.state-card h3 {
  margin: 0 0 8px;
  font-size: 18px;
}

.state-card p {
  margin: 0;
  color: #64748b;
  font-size: 14px;
}

.error-state h3 {
  color: #b91c1c;
}

.spinner {
  width: 28px;
  height: 28px;
  margin: 0 auto 12px;
  border: 3px solid #e5e7eb;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Responsive */

@media (max-width: 900px) {
  .filters-card {
    grid-template-columns: 1fr 1fr;
  }

  .search-item {
    grid-column: span 2;
  }
}

@media (max-width: 700px) {
  .page {
    padding: 25px 12px;
  }

  .page-header {
    align-items: flex-start;
    flex-direction: column;
  }

  .page-header h1 {
    font-size: 26px;
  }

  .primary-button {
    width: 100%;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .filters-card {
    grid-template-columns: 1fr;
  }

  .search-item {
    grid-column: auto;
  }

  .form-actions {
    flex-direction: column-reverse;
  }

  .form-actions button {
    width: 100%;
  }

  .edit-button,
  .delete-button {
    padding: 8px 10px;
  }
}
</style>