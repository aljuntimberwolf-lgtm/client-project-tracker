<script setup lang="ts">
const config = useRuntimeConfig()

const {
  data: projects,
  error,
  pending,
  refresh
} = await useFetch(`${config.public.apiBase}/projects`)

const showForm = ref(false)

const form = ref({
  client_name: '',
  project_name: '',
  description: '',
  status: 'Planning',
  priority: 'Medium',
  start_date: '',
  due_date: ''
})

const formError = ref('')

async function createProject() {
  formError.value = ''

  try {
    await $fetch(`${config.public.apiBase}/projects`, {
      method: 'POST',
      body: form.value,
      headers: {
        Accept: 'application/json'
      }
    })

    form.value = {
      client_name: '',
      project_name: '',
      description: '',
      status: 'Planning',
      priority: 'Medium',
      start_date: '',
      due_date: ''
    }

    showForm.value = false

    await refresh()
  } catch (error: any) {
    formError.value =
      error?.data?.message || 'Failed to create project.'
  }
}
</script>

<template>
  <div class="container">
    <div class="header">
      <h1>Client Project Tracker</h1>

      <button @click="showForm = !showForm">
        {{ showForm ? 'Cancel' : '+ Add Project' }}
      </button>
    </div>

    <div v-if="showForm" class="form-container">
      <h2>Add Project</h2>

      <p v-if="formError" class="error">
        {{ formError }}
      </p>

      <form @submit.prevent="createProject">
        <div class="form-group">
          <label>Client Name</label>
          <input
            v-model="form.client_name"
            type="text"
            required
          />
        </div>

        <div class="form-group">
          <label>Project Name</label>
          <input
            v-model="form.project_name"
            type="text"
            required
          />
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea
            v-model="form.description"
          ></textarea>
        </div>

        <div class="form-group">
          <label>Status</label>
          <select v-model="form.status">
            <option value="Planning">Planning</option>
            <option value="In Progress">In Progress</option>
            <option value="On Hold">On Hold</option>
            <option value="Completed">Completed</option>
          </select>
        </div>

        <div class="form-group">
          <label>Priority</label>
          <select v-model="form.priority">
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
          </select>
        </div>

        <div class="date-row">
          <div class="form-group">
            <label>Start Date</label>
            <input
              v-model="form.start_date"
              type="date"
              required
            />
          </div>

          <div class="form-group">
            <label>Due Date</label>
            <input
              v-model="form.due_date"
              type="date"
              required
            />
          </div>
        </div>

        <button type="submit">
          Create Project
        </button>
      </form>
    </div>

    <p v-if="pending">
      Loading projects...
    </p>

    <p v-else-if="error">
      Failed to load projects.
    </p>

    <div v-else>
      <p v-if="!projects?.length">
        No projects found.
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
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="project in projects"
            :key="project.id"
          >
            <td>{{ project.id }}</td>
            <td>{{ project.client_name }}</td>
            <td>{{ project.project_name }}</td>
            <td>{{ project.status }}</td>
            <td>{{ project.priority }}</td>
            <td>{{ project.start_date }}</td>
            <td>{{ project.due_date }}</td>
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

button {
  padding: 10px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  background: #111827;
  color: white;
}

.form-container {
  background: white;
  padding: 25px;
  margin-bottom: 30px;
  border-radius: 6px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
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
</style>