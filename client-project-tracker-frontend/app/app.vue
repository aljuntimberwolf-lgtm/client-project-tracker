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

/*
|--------------------------------------------------------------------------
| Search and Filters
|--------------------------------------------------------------------------
*/

const filteredProjects = computed(() => {
  if (!projects.value) {
    return []
  }

  const searchValue = search.value
    .trim()
    .toLowerCase()

  return projects.value.filter((project: any) => {
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
})
</script>

<template>
  <div class="container">

    <!-- Header -->
    <div class="header">
      <h1>Client Project Tracker</h1>

      <button
        class="primary-button"
        @click="
          showForm = !showForm;
          if (!showForm) resetForm()
        "
      >
        {{ showForm ? 'Cancel' : '+ Add Project' }}
      </button>
    </div>

    <!-- Project Form -->
    <div v-if="showForm" class="form-container">

      <h2>
        {{ editingProjectId ? 'Edit Project' : 'Add Project' }}
      </h2>

      <p v-if="formError" class="error">
        {{ formError }}
      </p>

      <form @submit.prevent="saveProject">

        <div class="form-group">
          <label for="client_name">
            Client Name
          </label>

          <input
            id="client_name"
            v-model="form.client_name"
            type="text"
            required
          />
        </div>

        <div class="form-group">
          <label for="project_name">
            Project Name
          </label>

          <input
            id="project_name"
            v-model="form.project_name"
            type="text"
            required
          />
        </div>

        <div class="form-group">
          <label for="description">
            Description
          </label>

          <textarea
            id="description"
            v-model="form.description"
          ></textarea>
        </div>

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

        <div class="date-row">

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

        <button
          type="submit"
          class="primary-button"
        >
          {{ editingProjectId ? 'Update Project' : 'Create Project' }}
        </button>

      </form>
    </div>

    <!-- Search and Filters -->
    <div class="filters">

      <div class="search-box">
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

      <div>
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

      <div>
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

    </div>

    <!-- Loading -->
    <p v-if="pending">
      Loading projects...
    </p>

    <!-- API Error -->
    <p v-else-if="error" class="error">
      Failed to load projects.
    </p>

    <!-- Projects -->
    <div v-else>

      <p v-if="!filteredProjects.length">
        No projects match your search or filters.
      </p>

      <table v-else>

        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Project</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Start Date</th>
            <th>Due Date</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>

          <tr
            v-for="project in filteredProjects"
            :key="project.id"
          >

            <td>
              {{ project.id }}
            </td>

            <td>
              {{ project.client_name }}
            </td>

            <td>
              {{ project.project_name }}
            </td>

            <td>
              {{ project.status }}
            </td>

            <td>
              {{ project.priority }}
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

  </div>
</template>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: #f5f5f5;
}

.container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.header h1 {
  margin: 0;
}

button {
  padding: 9px 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.primary-button {
  background: #111827;
  color: white;
}

.edit-button {
  background: #2563eb;
  color: white;
  margin-right: 6px;
}

.delete-button {
  background: #dc2626;
  color: white;
}

.form-container {
  background: white;
  padding: 25px;
  margin-bottom: 30px;
  border-radius: 6px;
}

.form-container h2 {
  margin-top: 0;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label,
.filters label {
  display: block;
  margin-bottom: 6px;
  font-weight: bold;
}

input,
textarea,
select {
  width: 100%;
  box-sizing: border-box;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

textarea {
  min-height: 100px;
  resize: vertical;
}

.date-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.error {
  color: #b91c1c;
  margin-bottom: 15px;
}

.filters {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 16px;
  background: white;
  padding: 20px;
  margin-bottom: 20px;
  border-radius: 6px;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

th,
td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: left;
}

th {
  background: #f0f0f0;
}

.actions {
  white-space: nowrap;
}
</style>