<script setup lang="ts">
import { SORT_COMPARATORS } from '~/constants/project'
import type { Project, ProjectFormData } from '~/types/project'

const MIN_PENDING_MS = 500
const JSON_HEADERS = { Accept: 'application/json' }

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const {
  data: projects,
  error,
  pending,
  refresh
} = await useFetch<Project[]>(`${apiBase}/projects`)

const { toasts, showToast, dismissToast } = useToasts()

/* Filters */

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')
const sortBy = ref('')

const filteredProjects = computed(() => {
  const query = search.value.trim().toLowerCase()

  const matches = (projects.value ?? []).filter((project) => {
    const matchesSearch =
      !query ||
      project.client_name.toLowerCase().includes(query) ||
      project.project_name.toLowerCase().includes(query) ||
      (project.description ?? '').toLowerCase().includes(query)

    return (
      matchesSearch &&
      (!statusFilter.value || project.status === statusFilter.value) &&
      (!priorityFilter.value || project.priority === priorityFilter.value)
    )
  })

  const compare = SORT_COMPARATORS[sortBy.value]

  return compare ? matches.sort(compare) : matches
})

const projectCountLabel = computed(() => {
  const count = filteredProjects.value.length

  return `${count} ${count === 1 ? 'project' : 'projects'}`
})

/* Add / edit modal */

const showModal = ref(false)
const editingProject = ref<Project | null>(null)
const formError = ref('')
const isSaving = ref(false)
const projectModal = ref<{ requestClose: () => void } | null>(null)

const headerButtonLabel = computed(() =>
  showModal.value ? 'Cancel' : '+ Add Project'
)

function openAddForm() {
  editingProject.value = null
  formError.value = ''
  showModal.value = true
}

function editProject(project: Project) {
  editingProject.value = project
  formError.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingProject.value = null
  formError.value = ''
}

function handleHeaderAction() {
  if (showModal.value) {
    projectModal.value?.requestClose()
    return
  }

  openAddForm()
}

async function saveProject(formData: ProjectFormData) {
  const project = editingProject.value
  const isEditing = Boolean(project)

  formError.value = ''
  isSaving.value = true
  const startedAt = Date.now()

  let saved = false

  try {
    await $fetch(
      project ? `${apiBase}/projects/${project.id}` : `${apiBase}/projects`,
      {
        method: isEditing ? 'PUT' : 'POST',
        body: formData,
        headers: JSON_HEADERS
      }
    )

    saved = true
  } catch (err) {
    formError.value = readErrorMessage(err, 'Failed to save project.')
  }

  await waitForMinimumDuration(startedAt)
  isSaving.value = false

  if (!saved) {
    return
  }

  closeModal()
  showToast(
    isEditing
      ? 'Project updated successfully.'
      : 'Project created successfully.'
  )

  await refresh()
}

/* Delete modal */

const deletingProject = ref<Project | null>(null)
const deleteError = ref('')
const isDeleting = ref(false)

function openDeleteModal(project: Project) {
  deletingProject.value = project
  deleteError.value = ''
}

function closeDeleteModal() {
  deletingProject.value = null
  deleteError.value = ''
}

async function deleteProject() {
  const project = deletingProject.value

  if (!project) {
    return
  }

  deleteError.value = ''
  isDeleting.value = true
  const startedAt = Date.now()

  let deleted = false

  try {
    await $fetch(`${apiBase}/projects/${project.id}`, {
      method: 'DELETE',
      headers: JSON_HEADERS
    })

    deleted = true
  } catch (err) {
    deleteError.value = readErrorMessage(err, 'Failed to delete project.')
  }

  await waitForMinimumDuration(startedAt)
  isDeleting.value = false

  if (!deleted) {
    return
  }

  closeDeleteModal()
  showToast(`"${project.project_name}" was deleted.`)

  await refresh()
}

/* Helpers */

async function waitForMinimumDuration(startedAt: number) {
  const remaining = MIN_PENDING_MS - (Date.now() - startedAt)

  if (remaining > 0) {
    await new Promise((resolve) => setTimeout(resolve, remaining))
  }
}

function readErrorMessage(err: any, fallback: string) {
  const validationErrors = err?.data?.errors

  if (validationErrors) {
    return Object.values(validationErrors).flat().join(' ')
  }

  return err?.data?.message || fallback
}
</script>

<template>
  <div class="page">
    <main class="container">

      <!-- Header -->
      <header class="page-header">
        <div>
          <p class="eyebrow">PROJECT MANAGEMENT</p>
          <h1>Client Project Tracker</h1>
          <p class="subtitle">Manage and track your client projects in one place.</p>
        </div>

        <button
          class="primary-button"
          @click="handleHeaderAction"
        >
          {{ headerButtonLabel }}
        </button>
      </header>

      <!-- Modal -->
      <Transition name="modal-fade">
        <ProjectModal
          v-if="showModal"
          ref="projectModal"
          :project="editingProject"
          :error="formError"
          :pending="isSaving"
          @save="saveProject"
          @close="closeModal"
        />
      </Transition>

      <!-- Delete modal -->
      <Transition name="modal-fade">
        <DeleteProjectModal
          v-if="deletingProject"
          :project="deletingProject"
          :error="deleteError"
          :pending="isDeleting"
          @confirm="deleteProject"
          @cancel="closeDeleteModal"
        />
      </Transition>

      <!-- Toasts -->
      <ToastNotification
        :toasts="toasts"
        @dismiss="dismissToast"
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
            <h2>Projects</h2>
            <p v-if="!pending">{{ projectCountLabel }}</p>
          </div>
        </div>

        <!-- Loading -->
        <div
          v-if="pending"
          class="state-card"
        >
          <div class="spinner" />
          <p>Loading projects...</p>
        </div>

        <!-- Error -->
        <div
          v-else-if="error"
          class="state-card error-state"
        >
          <h3>Unable to load projects</h3>
          <p>Please check that the Laravel API is running.</p>
        </div>

        <!-- Empty -->
        <div
          v-else-if="!filteredProjects.length"
          class="state-card"
        >
          <h3>No projects found</h3>
          <p>Try changing your search or filters.</p>
        </div>

        <!-- Table -->
        <ProjectTable
          v-else
          :projects="filteredProjects"
          @edit="editProject"
          @delete="openDeleteModal"
        />
      </section>

    </main>
  </div>
</template>