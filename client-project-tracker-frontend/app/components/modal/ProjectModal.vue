<script setup lang="ts">
const props = defineProps<{
  project?: any | null
  error?: string
}>()

const emit = defineEmits<{
  save: [project: any]
  close: []
}>()

const isEditing = computed(() => Boolean(props.project))

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

function close() {
  emit('close')
}

function onOverlayClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    close()
  }
}

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    close()
  }
}

let previousOverflow = ''

onMounted(() => {
  previousOverflow = document.body.style.overflow
  document.body.style.overflow = 'hidden'
  document.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
  document.body.style.overflow = previousOverflow
  document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
  <Teleport to="body">
    <div
      class="modal-overlay"
      @click="onOverlayClick"
    >
      <div
        class="modal-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="project-modal-title"
      >

        <header class="modal-header">

          <div class="section-header">
            <h2 id="project-modal-title">
              {{ isEditing ? 'Edit Project' : 'Add Project' }}
            </h2>

            <p>
              {{
                isEditing
                  ? 'Update the project information below.'
                  : 'Enter the project information below.'
              }}
            </p>
          </div>

          <button
            type="button"
            class="modal-close"
            aria-label="Close"
            @click="close"
          >
            &times;
          </button>

        </header>

        <div
          v-if="error"
          class="error-message"
        >
          {{ error }}
        </div>

        <form
          class="project-form"
          @submit.prevent="submitForm"
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
              @click="close"
            >
              Cancel
            </button>

            <button
              type="submit"
              class="primary-button"
            >
              {{
                isEditing
                  ? 'Update Project'
                  : 'Create Project'
              }}
            </button>
          </div>

        </form>

      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 40px 16px;
  overflow-y: auto;
  background: rgba(15, 23, 42, 0.55);
}

.modal-dialog {
  width: 100%;
  max-width: 720px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
  padding: 25px;
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 20px;
}

.modal-header .section-header {
  margin-bottom: 0;
}

.modal-close {
  flex-shrink: 0;
  border: none;
  background: transparent;
  padding: 0;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  font-size: 24px;
  line-height: 1;
  color: #64748b;
  transition: 0.2s ease;
}

.modal-close:hover {
  background: #f1f5f9;
  color: #111827;
}

@media (max-width: 700px) {
  .modal-overlay {
    padding: 20px 12px;
  }

  .modal-dialog {
    padding: 20px 16px;
  }
}
</style>
