<script setup lang="ts">
const props = defineProps<{
  project?: any | null
  error?: string
}>()

const emit = defineEmits<{
  save: [project: any]
  cancel: []
}>()

const form = ref({
  client_name: '',
  project_name: '',
  description: '',
  status: 'Planning',
  priority: 'Medium',
  start_date: '',
  due_date: ''
})

watch(
  () => props.project,
  (project) => {
    if (project) {
      form.value = {
        client_name: project.client_name,
        project_name: project.project_name,
        description: project.description || '',
        status: project.status,
        priority: project.priority,
        start_date: project.start_date.substring(0, 10),
        due_date: project.due_date.substring(0, 10)
      }
    } else {
      resetForm()
    }
  },
  { immediate: true }
)

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
}

function submitForm() {
  emit('save', { ...form.value })
}
</script>

<template>
  <section class="card form-card">

    <div class="section-header">
      <div>
        <h2>
          {{ props.project ? 'Edit Project' : 'Add Project' }}
        </h2>

        <p>
          {{
            props.project
              ? 'Update the project information below.'
              : 'Enter the project information below.'
          }}
        </p>
      </div>
    </div>

    <div
      v-if="props.error"
      class="error-message"
    >
      {{ props.error }}
    </div>

    <form
      @submit.prevent="submitForm"
      class="project-form"
    >

      <div class="form-grid">

        <!-- Client Name -->
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

        <!-- Project Name -->
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

      <!-- Actions -->
      <div class="form-actions">

        <button
          type="button"
          class="secondary-button"
          @click="emit('cancel')"
        >
          Cancel
        </button>

        <button
          type="submit"
          class="primary-button"
        >
          {{
            props.project
              ? 'Update Project'
              : 'Create Project'
          }}
        </button>

      </div>

    </form>

  </section>
</template>