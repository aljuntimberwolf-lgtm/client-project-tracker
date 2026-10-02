<script setup lang="ts">
const config = useRuntimeConfig()

const {
  data: projects,
  error,
  pending,
  refresh
} = await useFetch(`${config.public.apiBase}/projects`)

const showForm = ref(false)
const editingProjectId = ref<number | null>(null)
const formError = ref('')

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')
const sortBy = ref('')

const form = ref({
  client_name: '',
  project_name: '',
  description: '',
  status: 'Planning',
  priority: 'Medium',
  start_date: '',
  due_date: ''
})

function resetForm() {
  form.value = {
    client_name: '',
    project_name: '',
    description: '',
    status: 'Planning',
    priority: 'Medium',
    start_date: '',
    due_date: ''
  }

  formError.value = ''
  editingProjectId.value = null
}

function openAddForm() {
  resetForm()
  showForm.value = true
}

function editProject(project: any) {
  editingProjectId.value = project.id

  form.value = {
    client_name: project.client_name,
    project_name: project.project_name,
    description: project.description || '',
    status: project.status,
    priority: project.priority,
    start_date: project.start_date.substring(0, 10),
    due_date: project.due_date.substring(0, 10)
  }

  formError.value = ''
  showForm.value = true
}

function cancelForm() {
  showForm.value = false
  resetForm()
}

async function saveProject() {
  formError.value = ''

  try {
    if (editingProjectId.value) {
      await $fetch(
        `${config.public.apiBase}/projects/${editingProjectId.value}`,
        {
          method: 'PUT',
          body: form.value,
          headers: {
            Accept: 'application/json'
          }
        }
      )
    } else {
      await $fetch(`${config.public.apiBase}/projects`, {
        method: 'POST',
        body: form.value,
        headers: {
          Accept: 'application/json'
        }
      })
    }

    showForm.value = false
    resetForm()

    await refresh()
  } catch (error: any) {
    if (error?.data?.errors) {
      const errors = error.data.errors

      formError.value = Object.values(errors)
        .flat()
        .join(' ')
    } else {
      formError.value =
        error?.data?.message || 'Failed to save project.'
    }
  }
}

async function deleteProject(id: number) {
  const confirmed = confirm(
    'Are you sure you want to delete this project?'
  )

  if (!confirmed) {
    return
  }

  try {
    await $fetch(
      `${config.public.apiBase}/projects/${id}`,
      {
        method: 'DELETE',
        headers: {
          Accept: 'application/json'
        }
      }
    )

    await refresh()
  } catch (error: any) {
    alert(
      error?.data?.message ||
      'Failed to delete project.'
    )
  }
}

const filteredProjects = computed(() => {
  if (!projects.value) {
    return []
  }

  const searchValue = search.value
    .trim()
    .toLowerCase()

  const filtered = projects.value.filter((project: any) => {
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
  })

  return filtered.sort((a: any, b: any) => {
    switch (sortBy.value) {
      case 'client_asc':
        return a.client_name.localeCompare(b.client_name)

      case 'client_desc':
        return b.client_name.localeCompare(a.client_name)

      case 'project_asc':
        return a.project_name.localeCompare(b.project_name)

      case 'project_desc':
        return b.project_name.localeCompare(a.project_name)

      case 'start_asc':
        return a.start_date.localeCompare(b.start_date)

      case 'start_desc':
        return b.start_date.localeCompare(a.start_date)

      case 'due_asc':
        return a.due_date.localeCompare(b.due_date)

      case 'due_desc':
        return b.due_date.localeCompare(a.due_date)

      case 'priority_asc': {
        const priorityOrder: Record<string, number> = {
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
        const priorityOrder: Record<string, number> = {
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
  })
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
          @click="showForm ? cancelForm() : openAddForm()"
        >
          <span>
            {{ showForm ? 'Cancel' : '+ Add Project' }}
          </span>
        </button>

      </header>

      <!-- Project Form -->
      <section
        v-if="showForm"
        class="card form-card"
      >

        <div class="section-header">
          <div>
            <h2>
              {{ editingProjectId ? 'Edit Project' : 'Add Project' }}
            </h2>

            <p>
              {{
                editingProjectId
                  ? 'Update the project information below.'
                  : 'Enter the project information below.'
              }}
            </p>
          </div>
        </div>

        <div
          v-if="formError"
          class="error-message"
        >
          {{ formError }}
        </div>

        <form
          @submit.prevent="saveProject"
          class="project-form"
        >

          <div class="form-grid">

            <!-- Client -->
            <div class="form-group">
              <label for="client_name">
                Client Name
              </label>

              <input
                id="client_name"
                v-model="form.client_name"
                type="text"
                placeholder="Enter client name"
                required
              />
            </div>

            <!-- Project -->
            <div class="form-group">
              <label for="project_name">
                Project Name
              </label>

              <input
                id="project_name"
                v-model="form.project_name"
                type="text"
                placeholder="Enter project name"
                required
              />
            </div>

            <!-- Status -->
            <div class="form-group">
              <label for="status">
                Status
              </label>

              <select
                id="status"
                v-model="form.status"
              >
                <option value="Planning">
                  Planning
                </option>

                <option value="In Progress">
                  In Progress
                </option>

                <option value="On Hold">
                  On Hold
                </option>

                <option value="Completed">
                  Completed
                </option>
              </select>
            </div>

            <!-- Priority -->
            <div class="form-group">
              <label for="priority">
                Priority
              </label>

              <select
                id="priority"
                v-model="form.priority"
              >
                <option value="Low">
                  Low
                </option>

                <option value="Medium">
                  Medium
                </option>

                <option value="High">
                  High
                </option>
              </select>
            </div>

            <!-- Start Date -->
            <div class="form-group">
              <label for="start_date">
                Start Date
              </label>

              <input
                id="start_date"
                v-model="form.start_date"
                type="date"
                required
              />
            </div>

            <!-- Due Date -->
            <div class="form-group">
              <label for="due_date">
                Due Date
              </label>

              <input
                id="due_date"
                v-model="form.due_date"
                type="date"
                required
              />
            </div>

          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="description">
              Description
            </label>

            <textarea
              id="description"
              v-model="form.description"
              placeholder="Enter project description"
              rows="4"
            ></textarea>
          </div>

          <div class="form-actions">

            <button
              type="button"
              class="secondary-button"
              @click="cancelForm"
            >
              Cancel
            </button>

            <button
              type="submit"
              class="primary-button"
            >
              {{
                editingProjectId
                  ? 'Update Project'
                  : 'Create Project'
              }}
            </button>

          </div>

        </form>

      </section>

      <!-- Filters -->
      <section class="card filters-card">

        <div class="filter-item search-item">
          <label for="search">
            Search
          </label>

          <input
            id="search"
            v-model="search"
            type="text"
            placeholder="Search client, project, or description..."
          />
        </div>

        <div class="filter-item">
          <label for="status-filter">
            Status
          </label>

          <select
            id="status-filter"
            v-model="statusFilter"
          >
            <option value="">
              All Statuses
            </option>

            <option value="Planning">
              Planning
            </option>

            <option value="In Progress">
              In Progress
            </option>

            <option value="On Hold">
              On Hold
            </option>

            <option value="Completed">
              Completed
            </option>
          </select>
        </div>

        <div class="filter-item">
          <label for="priority-filter">
            Priority
          </label>

          <select
            id="priority-filter"
            v-model="priorityFilter"
          >
            <option value="">
              All Priorities
            </option>

            <option value="Low">
              Low
            </option>

            <option value="Medium">
              Medium
            </option>

            <option value="High">
              High
            </option>
          </select>
        </div>

        <div class="filter-item">
          <label for="sort">
            Sort By
          </label>

          <select
            id="sort"
            v-model="sortBy"
          >
            <option value="">
              Default
            </option>

            <option value="client_asc">
              Client Name A → Z
            </option>

            <option value="client_desc">
              Client Name Z → A
            </option>

            <option value="project_asc">
              Project Name A → Z
            </option>

            <option value="project_desc">
              Project Name Z → A
            </option>

            <option value="start_asc">
              Start Date — Oldest
            </option>

            <option value="start_desc">
              Start Date — Newest
            </option>

            <option value="due_asc">
              Due Date — Earliest
            </option>

            <option value="due_desc">
              Due Date — Latest
            </option>

            <option value="priority_asc">
              Priority — Low to High
            </option>

            <option value="priority_desc">
              Priority — High to Low
            </option>
          </select>
        </div>

      </section>

      <!-- Results -->
      <section class="results-section">

        <div class="results-header">

          <div>
            <h2>
              Projects
            </h2>

            <p v-if="!pending">
              {{ filteredProjects.length }}
              {{ filteredProjects.length === 1 ? 'project' : 'projects' }}
            </p>
          </div>

        </div>

        <!-- Loading -->
        <div
          v-if="pending"
          class="state-card"
        >
          <div class="spinner"></div>
          <p>Loading projects...</p>
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
        <div
          v-else
          class="table-wrapper"
        >

          <table>

            <thead>
              <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Project</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Start Date</th>
                <th>Due Date</th>
                <th class="actions-column">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody>

              <tr
                v-for="project in filteredProjects"
                :key="project.id"
              >

                <td class="id-column">
                  {{ project.id }}
                </td>

                <td>
                  <strong>
                    {{ project.client_name }}
                  </strong>
                </td>

                <td>
                  {{ project.project_name }}
                </td>

                <td>

                  <span
                    class="badge status-badge"
                    :class="{
                      'status-planning':
                        project.status === 'Planning',

                      'status-progress':
                        project.status === 'In Progress',

                      'status-hold':
                        project.status === 'On Hold',

                      'status-completed':
                        project.status === 'Completed'
                    }"
                  >
                    {{ project.status }}
                  </span>

                </td>

                <td>

                  <span
                    class="badge priority-badge"
                    :class="{
                      'priority-low':
                        project.priority === 'Low',

                      'priority-medium':
                        project.priority === 'Medium',

                      'priority-high':
                        project.priority === 'High'
                    }"
                  >
                    {{ project.priority }}
                  </span>

                </td>

                <td>
                  {{ project.start_date.substring(0, 10) }}
                </td>

                <td>
                  {{ project.due_date.substring(0, 10) }}
                </td>

                <td class="actions">

                  <button
                    class="edit-button"
                    @click="editProject(project)"
                  >
                    Edit
                  </button>

                  <button
                    class="delete-button"
                    @click="deleteProject(project.id)"
                  >
                    Delete
                  </button>

                </td>

              </tr>

            </tbody>

          </table>

        </div>

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
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
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

.form-card {
  padding: 25px;
  margin-bottom: 25px;
}

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
  grid-template-columns: repeat(2, 1fr);
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
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 16px;
  padding: 20px;
  margin-bottom: 30px;
}

.filter-item {
  min-width: 0;
}

.search-item {
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
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
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

/* Empty / Loading */

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