<script setup lang="ts">
import { PRIORITY_CLASSES, STATUS_CLASSES } from '~/constants/project'
import type { Project } from '~/types/project'

defineProps<{
  projects: Project[]
}>()

const emit = defineEmits<{
  edit: [project: Project]
  delete: [project: Project]
}>()

function formatDate(value: string) {
  return value.substring(0, 10)
}
</script>

<template>
  <div class="table-wrapper">
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
          <th class="actions-column">Actions</th>
        </tr>
      </thead>

      <TransitionGroup
        tag="tbody"
        name="row"
        appear
      >
        <tr
          v-for="(project, index) in projects"
          :key="project.id"
          :style="{ transitionDelay: `${Math.min(index, 10) * 40}ms` }"
        >
          <td class="id-column">{{ project.id }}</td>

          <td><strong>{{ project.client_name }}</strong></td>

          <td>{{ project.project_name }}</td>

          <!-- Status -->
          <td>
            <span
              class="badge"
              :class="STATUS_CLASSES[project.status]"
            >
              {{ project.status }}
            </span>
          </td>

          <!-- Priority -->
          <td>
            <span
              class="badge"
              :class="PRIORITY_CLASSES[project.priority]"
            >
              {{ project.priority }}
            </span>
          </td>

          <td>{{ formatDate(project.start_date) }}</td>

          <td>{{ formatDate(project.due_date) }}</td>

          <!-- Actions -->
          <td class="actions">
            <button
              class="edit-button"
              @click="emit('edit', project)"
            >
              Edit
            </button>

            <button
              class="delete-button"
              @click="emit('delete', project)"
            >
              Delete
            </button>
          </td>
        </tr>
      </TransitionGroup>

    </table>
  </div>
</template>

<style scoped>
.row-enter-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.row-leave-active {
  transition: opacity 0.2s ease;
}

.row-move {
  transition: transform 0.3s ease;
}

.row-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.row-leave-to {
  opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
  .row-enter-active,
  .row-leave-active {
    transition: none;
  }

  .row-enter-from,
  .row-leave-to {
    opacity: 0;
    transform: none;
  }
}
</style>