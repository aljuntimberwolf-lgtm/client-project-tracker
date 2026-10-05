<script setup lang="ts">
import { PRIORITY_OPTIONS, STATUS_OPTIONS } from '~/constants/project'
import type { Project, ProjectFormData } from '~/types/project'

const props = defineProps<{
  project?: Project | null
  error?: string
  pending?: boolean
}>()

const emit = defineEmits<{
  save: [project: ProjectFormData]
  close: []
}>()

const dialog = ref<HTMLElement | null>(null)
const showDiscardPrompt = ref(false)

const isEditing = computed(() => Boolean(props.project))

function createEmptyForm(): ProjectFormData {
  return {
    client_name: '',
    project_name: '',
    description: '',
    status: 'Planning',
    priority: 'Medium',
    start_date: '',
    due_date: ''
  }
}

function formFromProject(project: Project): ProjectFormData {
  return {
    client_name: project.client_name,
    project_name: project.project_name,
    description: project.description ?? '',
    status: project.status,
    priority: project.priority,
    start_date: project.start_date.substring(0, 10),
    due_date: project.due_date.substring(0, 10)
  }
}

const form = ref<ProjectFormData>(createEmptyForm())
const savedForm = ref<ProjectFormData>(createEmptyForm())

const isDirty = computed(
  () => JSON.stringify(form.value) !== JSON.stringify(savedForm.value)
)

const submitLabel = computed(() => {
  if (props.pending) {
    return isEditing.value ? 'Updating...' : 'Creating...'
  }

  return isEditing.value ? 'Update Project' : 'Create Project'
})

watch(
  () => props.project,
  (project) => {
    form.value = project ? formFromProject(project) : createEmptyForm()
    savedForm.value = { ...form.value }
  },
  { immediate: true }
)

onMounted(() => dialog.value?.focus())

function submitForm() {
  emit('save', { ...form.value })
}

function requestClose() {
  if (props.pending) {
    return
  }

  showDiscardPrompt.value = true
}

function discardChanges() {
  showDiscardPrompt.value = false
  emit('close')
}

function keepEditing() {
  showDiscardPrompt.value = false
}

function onOverlayClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    requestClose()
  }
}

useModalDismiss(() => {
  if (!showDiscardPrompt.value) {
    requestClose()
  }
})

defineExpose({ requestClose })
</script>

<template>
  <div
    class="modal-overlay"
    @click="onOverlayClick"
  >
    <div
      ref="dialog"
      class="modal-dialog dialog-panel"
      role="dialog"
      aria-modal="true"
      aria-labelledby="project-modal-title"
      tabindex="-1"
    >
      <header class="modal-header">
        <div class="section-header">
          <h2 id="project-modal-title">
            {{ isEditing ? 'Edit Project' : 'Add Project' }}
          </h2>

          <p>
            {{ isEditing ? 'Update the project information below.' : 'Enter the project information below.' }}
          </p>
        </div>

        <button
          type="button"
          class="modal-close"
          aria-label="Close"
          :disabled="pending"
          @click="requestClose"
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
        :aria-busy="pending"
        @submit.prevent="submitForm"
      >
        <div class="form-grid">
          <!-- Client Name -->
          <div class="form-group">
            <label for="client_name">Client Name</label>
            <input
              id="client_name"
              v-model="form.client_name"
              type="text"
              placeholder="Enter client name"
              :disabled="pending"
              required
            />
          </div>

          <!-- Project Name -->
          <div class="form-group">
            <label for="project_name">Project Name</label>
            <input
              id="project_name"
              v-model="form.project_name"
              type="text"
              placeholder="Enter project name"
              :disabled="pending"
              required
            />
          </div>

          <!-- Status -->
          <div class="form-group">
            <label for="status">Status</label>
            <select
              id="status"
              v-model="form.status"
              :disabled="pending"
            >
              <option
                v-for="option in STATUS_OPTIONS"
                :key="option"
                :value="option"
              >
                {{ option }}
              </option>
            </select>
          </div>

          <!-- Priority -->
          <div class="form-group">
            <label for="priority">Priority</label>
            <select
              id="priority"
              v-model="form.priority"
              :disabled="pending"
            >
              <option
                v-for="option in PRIORITY_OPTIONS"
                :key="option"
                :value="option"
              >
                {{ option }}
              </option>
            </select>
          </div>

          <!-- Start Date -->
          <div class="form-group">
            <label for="start_date">Start Date</label>
            <input
              id="start_date"
              v-model="form.start_date"
              type="date"
              :disabled="pending"
              required
            />
          </div>

          <!-- Due Date -->
          <div class="form-group">
            <label for="due_date">Due Date</label>
            <input
              id="due_date"
              v-model="form.due_date"
              type="date"
              :disabled="pending"
              required
            />
          </div>
        </div>

        <!-- Description -->
        <div class="form-group">
          <label for="description">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            placeholder="Enter project description"
            :disabled="pending"
            rows="4"
          />
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button
            type="button"
            class="secondary-button"
            :disabled="pending"
            @click="requestClose"
          >
            Cancel
          </button>

          <button
            type="submit"
            class="primary-button"
            :disabled="pending"
          >
            <span
              v-if="pending"
              class="button-spinner"
              aria-hidden="true"
            />
            {{ submitLabel }}
          </button>
        </div>
      </form>
    </div>

    <!-- Unsaved changes prompt -->
    <Transition name="modal-fade">
      <DiscardChangesModal
        v-if="showDiscardPrompt"
        @discard="discardChanges"
        @keep="keepEditing"
      />
    </Transition>
  </div>
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
  outline: none;
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

.modal-close:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.project-form button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
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